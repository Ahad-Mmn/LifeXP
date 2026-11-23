<?php
declare(strict_types=1);

namespace LifeXP\Services;

use DateTimeImmutable;
use PDO;
use Throwable;

final class SkipService
{
    /** Free skips left for each type */
    public static function freeSkips(PDO $pdo, int $userId): array
    {
        $stmt = $pdo->prepare('SELECT free_skips_main, free_skips_side FROM users WHERE id = :u');
        $stmt->execute(['u' => $userId]);
        $row = $stmt->fetch();

        return [
            'main' => (int) ($row['free_skips_main'] ?? 0),
            'side' => (int) ($row['free_skips_side'] ?? 0),
        ];
    }

    /** What skipping this quest would cost right now. Null when it cannot be skipped. */
    public static function preview(array $quest, array $free): ?array
    {
        $type = (string) $quest['quest_type'];
        if ($quest['status'] !== 'active' || !in_array($type, ['main', 'side', 'bonus'], true)) {
            return null;
        }

        if ($type === 'bonus') {
            return ['type' => 'bonus', 'free_left' => 0, 'cost' => 0, 'expiry_cost' => 0, 'pct' => 0];
        }

        $penalty  = XpService::penaltyFor($type, (int) $quest['xp_reward']);
        $freeLeft = (int) ($free[$type] ?? 0);

        return [
            'type'        => $type,
            'free_left'   => $freeLeft,
            'cost'        => $freeLeft > 0 ? 0 : $penalty,
            'expiry_cost' => $penalty,
            'pct'         => XpService::PENALTY_PERCENT[$type],
        ];
    }

    /**
     * Skips an active quest. Returns the outcome, or null when the quest does not exist,
     * is not yours, is not active or has already run out of time.
     */
    public static function skip(PDO $pdo, int $questId, int $userId): ?array
    {
        $now    = new DateTimeImmutable('now');
        $nowStr = $now->format('Y-m-d H:i:s');

        $pdo->beginTransaction();
        try {
            // The lock stops two tabs from skipping, or charging for, the same quest twice
            $stmt = $pdo->prepare(
                "SELECT id, title, quest_type, xp_reward FROM quests
                 WHERE id = :id AND user_id = :u AND status = 'active'
                   AND quest_type IN ('main','side','bonus')
                   AND (expires_at IS NULL OR expires_at >= :now) FOR UPDATE"
            );
            $stmt->execute(['id' => $questId, 'u' => $userId, 'now' => $nowStr]);
            $quest = $stmt->fetch();

            if (!$quest) {
                $pdo->rollBack();
                return null;
            }

            $stmt = $pdo->prepare(
                'SELECT total_xp, level, free_skips_main, free_skips_side FROM users WHERE id = :u FOR UPDATE'
            );
            $stmt->execute(['u' => $userId]);
            $user = $stmt->fetch();

            $type     = (string) $quest['quest_type'];
            $oldTotal = (int) $user['total_xp'];
            $oldLevel = (int) $user['level'];
            $penalty  = 0;
            $usedFree = false;
            $freeLeft = 0;
            $column   = null;

            if ($type !== 'bonus') {
                $column   = $type === 'main' ? 'free_skips_main' : 'free_skips_side';
                $freeLeft = (int) $user[$column];

                if ($freeLeft > 0) {
                    $usedFree = true;
                    $freeLeft--;
                } else {
                    $penalty = min(XpService::penaltyFor($type, (int) $quest['xp_reward']), $oldTotal);
                }
            }

            $pdo->prepare("UPDATE quests SET status = 'skipped', resolved_at = :now WHERE id = :id")
                ->execute(['now' => $nowStr, 'id' => $questId]);

            if ($usedFree) {
                $pdo->prepare('UPDATE users SET ' . $column . ' = :n WHERE id = :u')
                    ->execute(['n' => $freeLeft, 'u' => $userId]);
            }

            $newTotal = $oldTotal - $penalty;
            $newLevel = XpService::levelForXp($newTotal);

            if ($penalty > 0) {
                $pdo->prepare(
                    'INSERT INTO xp_ledger (user_id, delta, reason, quest_id, created_at)
                     VALUES (:u, :d, :r, :q, :at)'
                )->execute(['u' => $userId, 'd' => -$penalty, 'r' => 'skip_penalty', 'q' => $questId, 'at' => $nowStr]);

                $pdo->prepare('UPDATE users SET total_xp = :xp, level = :lvl WHERE id = :u')
                    ->execute(['xp' => $newTotal, 'lvl' => $newLevel, 'u' => $userId]);
            }

            $pdo->commit();

            return [
                'title'         => $quest['title'],
                'type'          => $type,
                'used_free'     => $usedFree,
                'free_left'     => $freeLeft,
                'penalty'       => $penalty,
                'level_dropped' => $penalty > 0 && $newLevel < $oldLevel,
                'new_level'     => $newLevel,
                'new_title'     => XpService::title($newLevel),
            ];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}