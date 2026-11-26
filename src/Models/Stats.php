<?php
declare(strict_types=1);

namespace LifeXP\Models;

use DateTimeImmutable;
use PDO;

final class Stats
{
    public function __construct(private PDO $pdo)
    {
    }

    /** XP earned on each of the last N days (days with no activity are 0) */
    public function xpPerDay(int $userId, int $days = 7): array
    {
        $start = (new DateTimeImmutable('today'))->modify('-' . ($days - 1) . ' days');

        $stmt = $this->pdo->prepare(
            'SELECT DATE(completed_at) AS d, SUM(xp_earned) AS xp
             FROM quest_completions
             WHERE user_id = :u AND completed_at >= :since
             GROUP BY DATE(completed_at)'
        );
        $stmt->execute(['u' => $userId, 'since' => $start->format('Y-m-d 00:00:00')]);
        $byDay = array_column($stmt->fetchAll(), 'xp', 'd');

        $labels = [];
        $values = [];
        for ($i = 0; $i < $days; $i++) {
            $day      = $start->modify('+' . $i . ' days');
            $labels[] = $day->format('D j');
            $values[] = (int) ($byDay[$day->format('Y-m-d')] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values, 'total' => array_sum($values)];
    }

    /** Completed quests per category (uses the snapshot, so deleted quests still count) */
    public function categoryBreakdown(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.name, c.color, COUNT(*) AS n
             FROM quest_completions qc
             JOIN categories c ON c.id = qc.category_id
             WHERE qc.user_id = :u
             GROUP BY c.id, c.name, c.color
             ORDER BY n DESC'
        );
        $stmt->execute(['u' => $userId]);
        $rows = $stmt->fetchAll();

        return [
            'labels' => array_column($rows, 'name'),
            'values' => array_map('intval', array_column($rows, 'n')),
            'colors' => array_column($rows, 'color'),
        ];
    }

    /** Every badge with its rule, its category and when the player earned it (null if still locked) */
    public function badgesForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT b.id, b.name, b.description, b.icon, b.category_id, b.track, b.metric,
                    b.threshold, b.tier, b.sort_order, c.name AS category_name, ub.earned_at
             FROM badges b
             LEFT JOIN categories c ON c.id = b.category_id
             LEFT JOIN user_badges ub ON ub.badge_id = b.id AND ub.user_id = :u
             ORDER BY (b.category_id IS NULL) DESC, c.name, b.sort_order, b.id'
        );
        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll();
    }

    public function leaderboard(int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));
        return $this->pdo->query(
            'SELECT username, level, total_xp FROM users
             WHERE show_on_leaderboards = 1
             ORDER BY total_xp DESC, id ASC LIMIT ' . $limit
        )->fetchAll();
    }

    /** 1 = top player among players who appear on the board. Ties share the better rank. */
    public function rankOf(int $userId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) + 1 FROM users
             WHERE show_on_leaderboards = 1
               AND total_xp > (SELECT total_xp FROM users WHERE id = :u)'
        );
        $stmt->execute(['u' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function siteTotals(): array
    {
        return $this->pdo->query(
            'SELECT (SELECT COUNT(*) FROM users) AS users,
                    (SELECT COUNT(*) FROM quests) AS quests,
                    (SELECT COUNT(*) FROM quest_completions) AS completions,
                    (SELECT COALESCE(SUM(total_xp), 0) FROM users) AS xp'
        )->fetch();
    }

    public function completedCount(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM quest_completions WHERE user_id = :u');
        $stmt->execute(['u' => $userId]);
        return (int) $stmt->fetchColumn();
    }
}