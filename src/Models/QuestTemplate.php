<?php
declare(strict_types=1);

namespace LifeXP\Models;

use PDO;

final class QuestTemplate
{
    public const TYPES = ['main', 'side', 'bonus'];

    public function __construct(private PDO $pdo)
    {
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM quest_templates WHERE id = :id AND is_active = 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** Active templates in one category, optionally limited to one type */
    public function forCategory(int $categoryId, ?string $type = null): array
    {
        $sql = 'SELECT * FROM quest_templates WHERE category_id = :cat AND is_active = 1';
        $params = ['cat' => $categoryId];

        if ($type !== null && in_array($type, self::TYPES, true)) {   // whitelist
            $sql .= ' AND quest_type = :type';
            $params['type'] = $type;
        }

        $stmt = $this->pdo->prepare($sql . ' ORDER BY quest_type, title');
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** How many templates each category has, per type (used to check the pool is full) */
    public function countsByCategory(): array
    {
        return $this->pdo->query(
            'SELECT c.name AS category, t.quest_type, COUNT(*) AS n
             FROM quest_templates t
             JOIN categories c ON c.id = t.category_id
             WHERE t.is_active = 1
             GROUP BY c.id, c.name, t.quest_type
             ORDER BY c.name, t.quest_type'
        )->fetchAll();
    }
}