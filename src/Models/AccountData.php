<?php
declare(strict_types=1);

namespace LifeXP\Models;

use PDO;

final class AccountData
{
    public function __construct(private PDO $pdo)
    {
    }

    /** Everything stored about one player, without the password hash */
    public function forUser(int $id): array
    {
        return [
            'exported_at' => date('c'),
            'account'     => $this->one(
                'SELECT username, email, bio, level, total_xp, current_streak, longest_streak,
                        last_active_date, created_at, profile_visibility, show_on_leaderboards,
                        show_badges, show_activity, terms_accepted_at, terms_version
                 FROM users WHERE id = :id',
                $id
            ),
            'interests'   => $this->column(
                'SELECT c.name FROM user_interests ui
                 JOIN categories c ON c.id = ui.category_id
                 WHERE ui.user_id = :id ORDER BY c.name',
                $id
            ),
            'quests'      => $this->all(
                'SELECT title, description, quest_type, difficulty, xp_reward, status, due_date, created_at
                 FROM quests WHERE user_id = :id ORDER BY id',
                $id
            ),
            'completions' => $this->all(
                'SELECT c.name AS category, qc.quest_type, qc.difficulty, qc.xp_earned, qc.completed_at
                 FROM quest_completions qc
                 LEFT JOIN categories c ON c.id = qc.category_id
                 WHERE qc.user_id = :id ORDER BY qc.id',
                $id
            ),
            'xp_history'  => $this->all(
                'SELECT delta, reason, created_at FROM xp_ledger WHERE user_id = :id ORDER BY id',
                $id
            ),
            'badges'      => $this->all(
                'SELECT b.name, ub.earned_at FROM user_badges ub
                 JOIN badges b ON b.id = ub.badge_id
                 WHERE ub.user_id = :id ORDER BY ub.earned_at',
                $id
            ),
            'activity'    => $this->all(
                'SELECT action, detail, ip, created_at FROM activity_log
                 WHERE user_id = :id ORDER BY id DESC LIMIT 200',
                $id
            ),
        ];
    }

    private function one(string $sql, int $id): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: [];
    }

    private function all(string $sql, int $id): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }

    private function column(string $sql, int $id): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}