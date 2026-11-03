<?php
declare(strict_types=1);

namespace LifeXP\Services;

use PDO;

final class BadgeService
{
    /**
     * Awards any badges the user has now earned. Call inside the completion
     * transaction, after the completion row is saved.
     * Returns the names of newly unlocked badges.
     */
    public static function award(PDO $pdo, int $userId, int $streak, int $level): array
    {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) AS total,
                    COUNT(DISTINCT q.category_id) AS cats,
                    SUM(q.difficulty = 'epic') AS epics
             FROM quest_completions qc
             JOIN quests q ON q.id = qc.quest_id
             WHERE qc.user_id = :u"
        );
        $stmt->execute(['u' => $userId]);
        $s = $stmt->fetch();

        $total = (int) $s['total'];
        $earned = [
            'first_quest'       => $total >= 1,
            'ten_quests'        => $total >= 10,
            'twentyfive_quests' => $total >= 25,
            'streak_3'          => $streak >= 3,
            'streak_7'          => $streak >= 7,
            'level_5'           => $level >= 5,
            'epic_slayer'       => (int) $s['epics'] >= 1,
            'all_rounder'       => (int) $s['cats'] >= 4,
        ];

        // Badges this user does not own yet
        $stmt = $pdo->prepare(
            'SELECT id, code, name FROM badges
             WHERE id NOT IN (SELECT badge_id FROM user_badges WHERE user_id = :u)'
        );
        $stmt->execute(['u' => $userId]);

        $insert = $pdo->prepare('INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (:u, :b)');
        $new = [];
        foreach ($stmt->fetchAll() as $badge) {
            if (!empty($earned[$badge['code']])) {
                $insert->execute(['u' => $userId, 'b' => $badge['id']]);
                if ($insert->rowCount() === 1) {
                    $new[] = $badge['name'];
                }
            }
        }
        return $new;
    }
}