<?php
declare(strict_types=1);

namespace LifeXP\Services;

use DateTimeImmutable;
use PDO;
use Throwable;

final class CompletionService
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Completes a quest and awards XP.
     * Returns a result array, or null if the quest doesn't exist, isn't yours, or is already completed.
     */
    public function complete(int $questId, int $userId): ?array
    {
        $today = new DateTimeImmutable('today');

        $this->pdo->beginTransaction();
        try {
            // FOR UPDATE locks the row, so a double-click or two tabs can't award XP twice
            $stmt = $this->pdo->prepare(
                "SELECT id, title, xp_reward, category_id, difficulty, quest_type FROM quests
                 WHERE id = :id AND user_id = :uid AND status = 'active' FOR UPDATE"
            );
            $stmt->execute(['id' => $questId, 'uid' => $userId]);
            $quest = $stmt->fetch();

            if (!$quest) {
                $this->pdo->rollBack();
                return null;
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
            $base     = (int) $quest['xp_reward'];
            $bonusPct = XpService::streakBonusPercent($streak);
            $earned   = (int) round($base * (1 + $bonusPct / 100));

            $oldLevel = (int) $user['level'];
            $newTotal = (int) $user['total_xp'] + $earned;
            $newLevel = XpService::levelForXp($newTotal);

            // ---- Save everything ----
            $this->pdo->prepare("UPDATE quests SET status = 'completed' WHERE id = :id")
                ->execute(['id' => $questId]);

            $this->pdo->prepare(
                'INSERT INTO quest_completions (quest_id, user_id, xp_earned, category_id, difficulty, quest_type)
                 VALUES (:q, :u, :xp, :cat, :diff, :type)'
            )->execute([
                'q'    => $questId,
                'u'    => $userId,
                'xp'   => $earned,
                'cat'  => $quest['category_id'],
                'diff' => $quest['difficulty'],
                'type' => $quest['quest_type'],
            ]);

            $this->pdo->prepare(
                'INSERT INTO xp_ledger (user_id, delta, reason, quest_id)
                 VALUES (:u, :d, :r, :q)'
            )->execute(['u' => $userId, 'd' => $earned, 'r' => 'quest_complete', 'q' => $questId]);

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
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}