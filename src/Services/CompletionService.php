<?php
declare(strict_types=1);

namespace LifeXP\Services;

use DateTimeImmutable;
use LifeXP\Models\Quest;
use PDO;
use Throwable;

final class CompletionService
{
    private const CUSTOM_DAILY_CAP = 60;
    private const CUSTOM_MIN_AGE_SECONDS = 120;

    public function __construct(private PDO $pdo)
    {
    }

    /** Plain-language explanation for a completion that gave less XP than expected */
    public static function noteText(?string $note): ?string
    {
        return match ($note) {
            'cap'       => 'Custom quests give at most ' . self::CUSTOM_DAILY_CAP . ' XP a day, so this one gave less or none.',
            'duplicate' => 'You already completed a custom quest with this title today, so it gave no XP.',
            default     => null,
        };
    }

    /**
     * Completes a quest and awards XP.
     * Returns a result array, or null if the quest does not exist, is not yours, is finished or has run out of time.
     * Throws CompletionRefused when a rule says it cannot be completed yet.
     */
    public function complete(int $questId, int $userId): ?array
    {
        $now      = new DateTimeImmutable('now');
        $nowStr   = $now->format('Y-m-d H:i:s');
        $today    = new DateTimeImmutable('today');
        $dayStart = $today->format('Y-m-d H:i:s');

        $this->pdo->beginTransaction();
        try {
            // FOR UPDATE locks the row, so a double-click or two tabs cannot award XP twice
            $stmt = $this->pdo->prepare(
                "SELECT id, title, xp_reward, category_id, difficulty, quest_type,
                        TIMESTAMPDIFF(SECOND, created_at, NOW()) AS age_seconds
                 FROM quests
                 WHERE id = :id AND user_id = :uid AND status = 'active'
                   AND (expires_at IS NULL OR expires_at >= :now) FOR UPDATE"
            );
            $stmt->execute(['id' => $questId, 'uid' => $userId, 'now' => $nowStr]);
            $quest = $stmt->fetch();

            if (!$quest) {
                $this->pdo->rollBack();
                return null;
            }

            $isCustom = $quest['quest_type'] === 'custom';
            if ($isCustom && (int) $quest['age_seconds'] < self::CUSTOM_MIN_AGE_SECONDS) {
                throw new CompletionRefused('Give this quest a couple of minutes first. Custom quests cannot be completed straight after you create them.');
            }

            $stmt = $this->pdo->prepare(
                'SELECT total_xp, level, current_streak, longest_streak, last_active_date
                 FROM users WHERE id = :id FOR UPDATE'
            );
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();

            // ---- Streak ----
            $last   = $user['last_active_date'];
            $streak = (int) $user['current_streak'];
            if ($last === $today->format('Y-m-d')) {
                $streak = max($streak, 1);
            } elseif ($last === $today->modify('-1 day')->format('Y-m-d')) {
                $streak++;
            } else {
                $streak = 1;
            }
            $longest = max((int) $user['longest_streak'], $streak);

            // ---- XP ----
            $base = $isCustom
                ? Quest::customXp((string) $quest['difficulty'])
                : (int) $quest['xp_reward'];
            $bonusPct = XpService::streakBonusPercent($streak);
            $earned   = (int) round($base * (1 + $bonusPct / 100));
            $note     = null;

            if ($isCustom) {
                $stmt = $this->pdo->prepare(
                    "SELECT COUNT(*) FROM quests
                     WHERE user_id = :u AND quest_type = 'custom' AND status = 'completed'
                       AND id <> :id AND LOWER(title) = LOWER(:t) AND resolved_at >= :start"
                );
                $stmt->execute(['u' => $userId, 'id' => $questId, 't' => $quest['title'], 'start' => $dayStart]);

                if ((int) $stmt->fetchColumn() > 0) {
                    $earned = 0;
                    $note   = 'duplicate';
                } else {
                    $stmt = $this->pdo->prepare(
                        "SELECT COALESCE(SUM(xp_earned), 0) FROM quest_completions
                         WHERE user_id = :u AND quest_type = 'custom' AND completed_at >= :start"
                    );
                    $stmt->execute(['u' => $userId, 'start' => $dayStart]);
                    $room = max(0, self::CUSTOM_DAILY_CAP - (int) $stmt->fetchColumn());

                    if ($earned > $room) {
                        $earned = $room;
                        $note   = 'cap';
                    }
                }
            }

            $oldLevel = (int) $user['level'];
            $newTotal = (int) $user['total_xp'] + $earned;
            $newLevel = XpService::levelForXp($newTotal);

            // ---- Save everything ----
            $this->pdo->prepare("UPDATE quests SET status = 'completed', resolved_at = :now WHERE id = :id")
                ->execute(['now' => $nowStr, 'id' => $questId]);

            $this->pdo->prepare(
                'INSERT INTO quest_completions
                    (quest_id, user_id, xp_earned, category_id, difficulty, quest_type, completed_at)
                 VALUES (:q, :u, :xp, :cat, :diff, :type, :at)'
            )->execute([
                'q'    => $questId,
                'u'    => $userId,
                'xp'   => $earned,
                'cat'  => $quest['category_id'],
                'diff' => $quest['difficulty'],
                'type' => $quest['quest_type'],
                'at'   => $nowStr,
            ]);

            if ($earned > 0) {
                $this->pdo->prepare(
                    'INSERT INTO xp_ledger (user_id, delta, reason, quest_id, created_at)
                     VALUES (:u, :d, :r, :q, :at)'
                )->execute(['u' => $userId, 'd' => $earned, 'r' => 'quest_complete', 'q' => $questId, 'at' => $nowStr]);
            }

            $this->pdo->prepare(
                'UPDATE users SET total_xp = :xp, level = :lvl, current_streak = :cs,
                                  longest_streak = :ls, last_active_date = :d
                 WHERE id = :id'
            )->execute([
                'xp'  => $newTotal,
                'lvl' => $newLevel,
                'cs'  => $streak,
                'ls'  => $longest,
                'd'   => $today->format('Y-m-d'),
                'id'  => $userId,
            ]);

            $skipColumn = match ($quest['quest_type']) {
                'main'  => 'free_skips_main',
                'side'  => 'free_skips_side',
                default => null,
            };
            if ($skipColumn !== null) {
                $this->pdo->prepare('UPDATE users SET ' . $skipColumn . ' = 2 WHERE id = :id')
                    ->execute(['id' => $userId]);
            }

            $newBadges = BadgeService::award($this->pdo, $userId, $streak, $newLevel);

            $this->pdo->commit();

            return [
                'title'      => $quest['title'],
                'base_xp'    => $base,
                'earned'     => $earned,
                'bonus_pct'  => $bonusPct,
                'streak'     => $streak,
                'leveled_up' => $newLevel > $oldLevel,
                'new_level'  => $newLevel,
                'new_title'  => XpService::title($newLevel),
                'new_badges' => $newBadges,
                'note'       => $note,
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}