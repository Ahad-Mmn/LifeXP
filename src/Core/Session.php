<?php
declare(strict_types=1);

namespace LifeXP\Core;

final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'        => (int) $user['id'],
            'username'  => (string) $user['username'],
            'role'      => (string) $user['role'],
            'onboarded' => (int) ($user['onboarded'] ?? 0) === 1,
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function userId(): ?int
    {
        return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;
    }

    public static function isOnboarded(): bool
    {
        return !empty($_SESSION['user']['onboarded']);
    }

    public static function markOnboarded(): void
    {
        if (isset($_SESSION['user'])) {
            $_SESSION['user']['onboarded'] = true;
        }
    }

    /** Protects sensitive pages: call at the top of any page that needs a login. */
    public static function requireLogin(): void
    {
        if (self::userId() === null) {
            self::flash('error', 'Please log in to access that page.');
            header('Location: index.php?page=login');
            exit;
        }
    }

    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }

    public static function pullFlash(): array
    {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }
}