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

    /** Completed quests per category */
    public function categoryBreakdown(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.name, c.color, COUNT(*) AS n
             FROM quest_completions qc
             JOIN quests q     ON q.id = qc.quest_id
             JOIN categories c ON c.id = q.category_id
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

    /** Every badge, with earned_at filled in (or null if still locked) */
    public function badgesForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT b.id, b.name, b.description, b.icon, ub.earned_at
             FROM badges b
             LEFT JOIN user_badges ub ON ub.badge_id = b.id AND ub.user_id = :u
             ORDER BY b.id'
        );
        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll();
    }

    public function leaderboard(int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));   // integer, clamped, safe to put in the query
        return $this->pdo->query(
            'SELECT username, level, total_xp FROM users ORDER BY total_xp DESC, id ASC LIMIT ' . $limit
        )->fetchAll();
    }

    /** 1 = top player. Ties share the better rank. */
    public function rankOf(int $userId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) + 1 FROM users
             WHERE total_xp > (SELECT total_xp FROM users WHERE id = :u)'
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
}