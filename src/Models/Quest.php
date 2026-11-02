<?php
declare(strict_types=1);

namespace LifeXP\Models;

use PDO;

final class Quest
{
    /** Difficulty => XP reward. The server decides XP, never the browser. */
    public const XP = ['easy' => 10, 'medium' => 25, 'hard' => 50, 'epic' => 100];

    public function __construct(private PDO $pdo)
    {
    }

    /** READ (list): all quests belonging to one user */
    public function allForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT q.*, c.name AS category_name, c.color AS category_color
             FROM quests q
             JOIN categories c ON c.id = q.category_id
             WHERE q.user_id = :uid
             ORDER BY q.status ASC, q.due_date IS NULL, q.due_date ASC, q.created_at DESC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    /** READ (one): only returns the quest if it belongs to this user */
    public function find(int $id, int $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM quests WHERE id = :id AND user_id = :uid');
        $stmt->execute(['id' => $id, 'uid' => $userId]);
        return $stmt->fetch() ?: null;
    }

    /** CREATE */
    public function create(int $userId, array $d): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO quests (user_id, category_id, title, description, difficulty, xp_reward, due_date)
             VALUES (:uid, :cat, :title, :descr, :diff, :xp, :due)'
        );
        $stmt->execute([
            'uid'   => $userId,
            'cat'   => $d['category_id'],
            'title' => $d['title'],
            'descr' => $d['description'],
            'diff'  => $d['difficulty'],
            'xp'    => self::XP[$d['difficulty']],
            'due'   => $d['due_date'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    /** UPDATE */
    public function update(int $id, int $userId, array $d): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE quests
             SET category_id = :cat, title = :title, description = :descr,
                 difficulty = :diff, xp_reward = :xp, due_date = :due
             WHERE id = :id AND user_id = :uid'
        );
        $stmt->execute([
            'cat'   => $d['category_id'],
            'title' => $d['title'],
            'descr' => $d['description'],
            'diff'  => $d['difficulty'],
            'xp'    => self::XP[$d['difficulty']],
            'due'   => $d['due_date'],
            'id'    => $id,
            'uid'   => $userId,
        ]);
    }

    /** DELETE */
    public function delete(int $id, int $userId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM quests WHERE id = :id AND user_id = :uid');
        $stmt->execute(['id' => $id, 'uid' => $userId]);
    }
}