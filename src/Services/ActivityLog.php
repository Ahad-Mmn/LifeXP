<?php
declare(strict_types=1);

namespace LifeXP\Services;

use PDO;

final class ActivityLog
{
    public static function record(PDO $pdo, ?int $userId, string $action, string $detail = ''): void
    {
        try {
            $pdo->prepare(
                'INSERT INTO activity_log (user_id, action, detail, ip) VALUES (:u, :a, :d, :ip)'
            )->execute([
                'u'  => $userId,
                'a'  => $action,
                'd'  => mb_substr($detail, 0, 255),
                'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
            ]);
        } catch (\PDOException $e) {
            // Logging must never break the site
        }
    }

    public static function recent(PDO $pdo, int $limit = 25): array
    {
        $limit = max(1, min($limit, 100));   // integer, clamped, safe in the query
        return $pdo->query(
            'SELECT a.action, a.detail, a.ip, a.created_at, u.username
             FROM activity_log a
             LEFT JOIN users u ON u.id = a.user_id
             ORDER BY a.id DESC LIMIT ' . $limit
        )->fetchAll();
    }
}