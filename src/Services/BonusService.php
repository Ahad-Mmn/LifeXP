<?php
declare(strict_types=1);

namespace LifeXP\Services;

use DateTimeImmutable;
use PDO;
use Throwable;

final class BonusService
{
    /** Accepts an offer: it becomes an active quest with its own time limit. Returns false if it is no longer available. */
    public static function accept(PDO $pdo, int $questId, int $userId): bool
    {
        $now = new DateTimeImmutable('now');

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                "SELECT id, template_id FROM quests
                 WHERE id = :id AND user_id = :u AND status = 'offered' AND quest_type = 'bonus'
                   AND expires_at >= :now FOR UPDATE"
            );
            $stmt->execute(['id' => $questId, 'u' => $userId, 'now' => $now->format('Y-m-d H:i:s')]);
            $quest = $stmt->fetch();

            if (!$quest) {
                $pdo->rollBack();
                return false;
            }

            $hours = 24;
            if ($quest['template_id'] !== null) {
                $stmt = $pdo->prepare('SELECT time_limit_hours FROM quest_templates WHERE id = :id');
                $stmt->execute(['id' => $quest['template_id']]);
                $found = $stmt->fetchColumn();
                if ($found !== false) {
                    $hours = max(1, (int) $found);
                }
            }

            $expires = $now->modify('+' . $hours . ' hours');
            $pdo->prepare(
                "UPDATE quests SET status = 'active', expires_at = :e, due_date = :d WHERE id = :id"
            )->execute([
                'e'  => $expires->format('Y-m-d H:i:s'),
                'd'  => $expires->format('Y-m-d'),
                'id' => $questId,
            ]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /** Declines an offer. It costs nothing. */
    public static function decline(PDO $pdo, int $questId, int $userId): bool
    {
        $stmt = $pdo->prepare(
            "UPDATE quests SET status = 'declined', resolved_at = :now
             WHERE id = :id AND user_id = :u AND status = 'offered' AND quest_type = 'bonus'"
        );
        $stmt->execute([
            'now' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            'id'  => $questId,
            'u'   => $userId,
        ]);
        return $stmt->rowCount() === 1;
    }
}