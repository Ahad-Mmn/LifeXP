<?php
declare(strict_types=1);

namespace LifeXP\Models;

use PDO;

final class User
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, username, email, total_xp, level, role, created_at,
             current_streak, longest_streak, last_active_date
             FROM users WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :u');
        $stmt->execute(['u' => $username]);
        return $stmt->fetch() ?: null;
    }

    public function usernameExists(string $username): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE username = :u');
        $stmt->execute(['u' => $username]);
        return (bool) $stmt->fetchColumn();
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE email = :e');
        $stmt->execute(['e' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    public function create(string $username, string $email, string $passwordHash): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, email, password_hash) VALUES (:u, :e, :p)'
        );
        $stmt->execute(['u' => $username, 'e' => $email, 'p' => $passwordHash]);
        return (int) $this->pdo->lastInsertId();
    }

        /** Admin list: every user plus how many quests they have */
    public function listWithCounts(): array
    {
        return $this->pdo->query(
            'SELECT u.id, u.username, u.email, u.role, u.level, u.total_xp, u.created_at,
                    (SELECT COUNT(*) FROM quests q WHERE q.user_id = u.id) AS quest_count
             FROM users u
             ORDER BY u.created_at DESC, u.id DESC
             LIMIT 200'
        )->fetchAll();
    }

    public function setRole(int $id, string $role): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET role = :r WHERE id = :id');
        $stmt->execute(['r' => $role, 'id' => $id]);
    }

    /** Quests, completions and badges are removed by the foreign keys (ON DELETE CASCADE) */
    public function deleteUser(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}