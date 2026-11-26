<?php
declare(strict_types=1);

namespace LifeXP\Services;

use PDO;

final class BadgeService
{
    /**
     * Everything a badge rule can ask about one player.
     * Custom quests are left out, so only delivered quests count toward badges.
     */
    public static function metrics(PDO $pdo, int $userId): array
    {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) AS completed,
                    COALESCE(SUM(quest_type = 'main'), 0)  AS main_done,
                    COALESCE(SUM(difficulty = 'epic'), 0)  AS epic_done,
                    COUNT(DISTINCT category_id)            AS cats
             FROM quest_completions
             WHERE user_id = :u AND quest_type <> 'custom'"
        );
        $stmt->execute(['u' => $userId]);
        $r = $stmt->fetch();

        $categoryXp = [];
        $stmt = $pdo->prepare(
            "SELECT category_id, SUM(xp_earned) AS xp FROM quest_completions
             WHERE user_id = :u AND quest_type <> 'custom' AND category_id IS NOT NULL
             GROUP BY category_id"
        );
        $stmt->execute(['u' => $userId]);
        foreach ($stmt->fetchAll() as $row) {
            $categoryXp[(int) $row['category_id']] = (int) $row['xp'];
        }

        $trackXp = [];
        $stmt = $pdo->prepare(
            "SELECT track, SUM(xp_earned) AS xp FROM quest_completions
             WHERE user_id = :u AND quest_type <> 'custom' AND track IS NOT NULL
             GROUP BY track"
        );
        $stmt->execute(['u' => $userId]);
        foreach ($stmt->fetchAll() as $row) {
            $trackXp[(string) $row['track']] = (int) $row['xp'];
        }

        $stmt = $pdo->prepare('SELECT level, longest_streak FROM users WHERE id = :u');
        $stmt->execute(['u' => $userId]);
        $user = $stmt->fetch() ?: ['level' => 1, 'longest_streak' => 0];

        return [
            'completed'      => (int) $r['completed'],
            'main_completed' => (int) $r['main_done'],
            'epic_completed' => (int) $r['epic_done'],
            'categories'     => (int) $r['cats'],
            'streak'         => (int) $user['longest_streak'],
            'level'          => (int) $user['level'],
            'category_xp'    => $categoryXp,
            'track_xp'       => $trackXp,
        ];
    }

    /** The player's current value for one badge rule */
    public static function valueFor(array $badge, array $metrics): int
    {
        return match ((string) $badge['metric']) {
            'category_xp'    => (int) ($metrics['category_xp'][(int) $badge['category_id']] ?? 0),
            'track_xp'       => (int) ($metrics['track_xp'][(string) $badge['track']] ?? 0),
            'completed'      => $metrics['completed'],
            'main_completed' => $metrics['main_completed'],
            'epic_completed' => $metrics['epic_completed'],
            'categories'     => $metrics['categories'],
            'streak'         => $metrics['streak'],
            'level'          => $metrics['level'],
            default          => 0,
        };
    }

    /**
     * Awards every badge the player has now earned. Call inside the completion transaction,
     * after the completion and the user row are saved. Returns the names of new badges.
     */
    public static function award(PDO $pdo, int $userId): array
    {
        $metrics = self::metrics($pdo, $userId);

        $stmt = $pdo->prepare(
            'SELECT b.id, b.name, b.metric, b.category_id, b.track, b.threshold
             FROM badges b
             LEFT JOIN user_badges ub ON ub.badge_id = b.id AND ub.user_id = :u
             WHERE ub.badge_id IS NULL AND b.metric IS NOT NULL AND b.threshold > 0
             ORDER BY b.tier, b.id'
        );
        $stmt->execute(['u' => $userId]);

        $insert = $pdo->prepare('INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (:u, :b)');
        $new = [];

        foreach ($stmt->fetchAll() as $badge) {
            if (self::valueFor($badge, $metrics) >= (int) $badge['threshold']) {
                $insert->execute(['u' => $userId, 'b' => $badge['id']]);
                if ($insert->rowCount() === 1) {
                    $new[] = $badge['name'];
                }
            }
        }
        return $new;
    }
}