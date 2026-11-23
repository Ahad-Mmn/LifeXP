<?php
declare(strict_types=1);

namespace LifeXP\Models;

use DateTimeImmutable;
use PDO;

final class Leaderboard
{
    public const METRICS = [
        'xp'        => 'XP earned',
        'completed' => 'Quests completed',
        'streak'    => 'Current streak',
        'level'     => 'Highest level',
    ];

    public const PERIODS = [
        'today' => 'Today',
        'week'  => 'This week',
        'month' => 'This month',
        'all'   => 'All time',
    ];

    public const TYPES = [
        'delivered' => 'Delivered quests',
        'all'       => 'All quests, including custom',
        'main'      => 'Main quests',
        'side'      => 'Side quests',
        'bonus'     => 'Bonus quests',
        'custom'    => 'Custom quests',
    ];

    public const LIMIT = 10;

    public function __construct(private PDO $pdo)
    {
    }

    /** The top of a board, best first. Players who opted out are never included. */
    public function top(array $f, DateTimeImmutable $now): array
    {
        $params = [];
        $inner  = $this->inner($f, $now, $params);

        $stmt = $this->pdo->prepare(
            'SELECT id, username, level, total_xp, metric_value
             FROM (' . $inner . ') t
             WHERE metric_value > 0
             ORDER BY metric_value DESC, total_xp DESC, id ASC
             LIMIT ' . self::LIMIT
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Where one player stands on a board */
    public function position(array $f, int $userId, DateTimeImmutable $now): array
    {
        $stmt = $this->pdo->prepare('SELECT show_on_leaderboards FROM users WHERE id = :u');
        $stmt->execute(['u' => $userId]);
        if ((int) $stmt->fetchColumn() !== 1) {
            return ['status' => 'hidden'];
        }

        $params = [];
        $inner  = $this->inner($f, $now, $params);
        $stmt   = $this->pdo->prepare('SELECT metric_value FROM (' . $inner . ') t WHERE id = :me');
        $stmt->execute($params + ['me' => $userId]);
        $value = (int) $stmt->fetchColumn();

        if ($value <= 0) {
            return ['status' => 'none'];
        }

        $params = [];
        $inner  = $this->inner($f, $now, $params);
        $stmt   = $this->pdo->prepare(
            'SELECT COALESCE(SUM(metric_value > :mine), 0) AS ahead, COUNT(*) AS total
             FROM (' . $inner . ') t
             WHERE metric_value > 0'
        );
        $stmt->execute($params + ['mine' => $value]);
        $r = $stmt->fetch();

        return [
            'status' => 'ranked',
            'rank'   => (int) $r['ahead'] + 1,
            'total'  => (int) $r['total'],
            'value'  => $value,
        ];
    }

    /**
     * One row per visible player with a metric_value. Every fragment of SQL here is fixed;
     * everything that comes from the request is bound as a parameter.
     */
    private function inner(array $f, DateTimeImmutable $now, array &$params): string
    {
        $metric = $f['metric'];

        if ($metric === 'streak' || $metric === 'level') {
            if ($metric === 'streak') {
                $expr = 'CASE WHEN u.last_active_date >= :yday THEN u.current_streak ELSE 0 END';
                $params['yday'] = $now->modify('-1 day')->format('Y-m-d');
            } else {
                $expr = 'u.total_xp';
            }

            return 'SELECT u.id, u.username, u.level, u.total_xp, ' . $expr . ' AS metric_value
                    FROM users u
                    WHERE u.show_on_leaderboards = 1';
        }

        $expr  = $metric === 'xp' ? 'SUM(qc.xp_earned)' : 'COUNT(*)';
        $where = ['u.show_on_leaderboards = 1'];

        if ($f['category_id'] > 0) {
            $where[] = 'qc.category_id = :cat';
            $params['cat'] = $f['category_id'];
        }

        $since = $this->since($f['period'], $now);
        if ($since !== null) {
            $where[] = 'qc.completed_at >= :since';
            $params['since'] = $since;
        }

        if ($f['type'] === 'delivered') {
            $where[] = "qc.quest_type IN ('main','side','bonus')";
        } elseif ($f['type'] !== 'all') {
            $where[] = 'qc.quest_type = :type';
            $params['type'] = $f['type'];
        }

        return 'SELECT u.id, u.username, u.level, u.total_xp, ' . $expr . ' AS metric_value
                FROM quest_completions qc
                JOIN users u ON u.id = qc.user_id
                WHERE ' . implode(' AND ', $where) . '
                GROUP BY u.id, u.username, u.level, u.total_xp';
    }

    private function since(string $period, DateTimeImmutable $now): ?string
    {
        $start = match ($period) {
            'today' => $now->setTime(0, 0),
            'week'  => $now->modify('monday this week')->setTime(0, 0),
            'month' => $now->modify('first day of this month')->setTime(0, 0),
            default => null,
        };
        return $start?->format('Y-m-d H:i:s');
    }
}