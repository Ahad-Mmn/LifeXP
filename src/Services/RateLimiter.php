<?php
declare(strict_types=1);

namespace LifeXP\Services;

use PDO;

final class RateLimiter
{
    private const MAX_PER_USER = 5;    // failures per IP + username
    private const MAX_PER_IP   = 20;   // failures per IP across all usernames
    private const WINDOW_MIN   = 15;

    public function __construct(private PDO $pdo)
    {
    }

    public function isLocked(string $ip, string $username): bool
    {
        $username = $this->clean($username);
        $window   = '(NOW() - INTERVAL ' . self::WINDOW_MIN . ' MINUTE)';

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) AS per_ip, COALESCE(SUM(username = :u), 0) AS per_user
             FROM login_attempts
             WHERE ip = :ip AND attempted_at > ' . $window
        );
        $stmt->execute(['ip' => $ip, 'u' => $username]);
        $r = $stmt->fetch();

        return (int) $r['per_user'] >= self::MAX_PER_USER || (int) $r['per_ip'] >= self::MAX_PER_IP;
    }

    public function recordFailure(string $ip, string $username): void
    {
        $this->pdo->prepare('INSERT INTO login_attempts (ip, username) VALUES (:ip, :u)')
            ->execute(['ip' => $ip, 'u' => $this->clean($username)]);

        // Housekeeping: forget old attempts
        $this->pdo->exec('DELETE FROM login_attempts WHERE attempted_at < (NOW() - INTERVAL 1 DAY)');
    }

    public function clear(string $ip, string $username): void
    {
        $this->pdo->prepare('DELETE FROM login_attempts WHERE ip = :ip AND username = :u')
            ->execute(['ip' => $ip, 'u' => $this->clean($username)]);
    }

    private function clean(string $username): string
    {
        return mb_substr(mb_strtolower($username), 0, 30);
    }
}