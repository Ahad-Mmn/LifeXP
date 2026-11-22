<?php
declare(strict_types=1);

namespace LifeXP\Services;

use DateTimeImmutable;
use PDO;

final class ProductivityService
{
    public const NEUTRAL = 50;
    private const WINDOW_DAYS = 14;
    private const MIN_RESOLVED = 3;

    /** Hidden score from 0 to 100, based on how delivered quests ended over the last 14 days */
    public static function score(PDO $pdo, int $userId, DateTimeImmutable $now): int
    {
        $since = $now->modify('-' . self::WINDOW_DAYS . ' days')->format('Y-m-d H:i:s');

        $stmt = $pdo->prepare(
            "SELECT COALESCE(SUM(status = 'completed'), 0) AS done,
                    COALESCE(SUM(status = 'skipped'), 0)   AS skipped,
                    COALESCE(SUM(status = 'expired'), 0)   AS expired,
                    COUNT(DISTINCT CASE WHEN status = 'completed' THEN DATE(resolved_at) END) AS days
             FROM quests
             WHERE user_id = :u
               AND quest_type IN ('main','side','bonus')
               AND status IN ('completed','skipped','expired')
               AND resolved_at >= :since"
        );
        $stmt->execute(['u' => $userId, 'since' => $since]);
        $r = $stmt->fetch();

        $done    = (int) $r['done'];
        $skipped = (int) $r['skipped'];
        $expired = (int) $r['expired'];
        $days    = (int) $r['days'];
        $ended   = $done + $skipped + $expired;

        if ($ended < self::MIN_RESOLVED) {
            return self::NEUTRAL;
        }

        $score = 45 * ($done / $ended)
               + 25 * min(1.0, $days / 7)
               + 15 * (1 - $skipped / $ended)
               + 15 * (1 - $expired / $ended);

        return max(0, min(100, (int) round($score)));
    }
}