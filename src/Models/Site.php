<?php
declare(strict_types=1);

namespace LifeXP\Models;

use LifeXP\Services\XpService;
use PDO;

final class Site
{
    public function __construct(private PDO $pdo)
    {
    }

    /** Site-wide counters for the home page */
    public function totals(): array
    {
        $row = $this->pdo->query(
            'SELECT (SELECT COUNT(*) FROM users) AS players,
                    (SELECT COUNT(*) FROM quest_completions) AS completions,
                    (SELECT COALESCE(SUM(xp_earned), 0) FROM quest_completions) AS xp'
        )->fetch();

        return [
            'players'     => (int) $row['players'],
            'completions' => (int) $row['completions'],
            'xp'          => (int) $row['xp'],
        ];
    }

    /** Every category with the number of quests in its library */
    public function categoryCards(): array
    {
        $rows = $this->pdo->query(
            'SELECT c.id, c.name, c.color, COUNT(t.id) AS quests
             FROM categories c
             LEFT JOIN quest_templates t ON t.category_id = c.id AND t.is_active = 1
             GROUP BY c.id, c.name, c.color
             ORDER BY c.name'
        )->fetchAll();

        return array_map(fn (array $r) => [
            'id'     => (int) $r['id'],
            'name'   => $r['name'],
            'color'  => $r['color'],
            'quests' => (int) $r['quests'],
        ], $rows);
    }

    /** One random main, side and bonus quest from the library, with the XP they would pay */
    public function featuredQuests(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT t.title, t.summary, t.difficulty, t.quest_type, t.time_limit_hours,
                    c.name AS category_name, c.color AS category_color
             FROM quest_templates t
             JOIN categories c ON c.id = t.category_id
             WHERE t.is_active = 1 AND t.quest_type = :type
             ORDER BY RAND() LIMIT 1'
        );

        $quests = [];
        foreach (['main', 'side', 'bonus'] as $type) {
            $stmt->execute(['type' => $type]);
            $row = $stmt->fetch();
            if (!$row) {
                continue;
            }

            $hours = (int) $row['time_limit_hours'];
            $quests[] = [
                'title'          => $row['title'],
                'summary'        => $row['summary'],
                'type'           => $row['quest_type'],
                'difficulty'     => $row['difficulty'],
                'category_name'  => $row['category_name'],
                'category_color' => $row['category_color'],
                'xp'             => XpService::questXp((string) $row['difficulty'], $hours),
                'time'           => $this->timeText($hours),
            ];
        }
        return $quests;
    }

    /** A few approachable badges to show what can be earned */
    public function badgeTeaser(): array
    {
        $rows = $this->pdo->query(
            'SELECT name, description, icon FROM badges WHERE tier <= 2 AND metric IS NOT NULL ORDER BY RAND() LIMIT 6'
        )->fetchAll();

        return array_map(fn (array $r) => [
            'name'        => $r['name'],
            'description' => $r['description'],
            'icon'        => (int) $r['icon'],
        ], $rows);
    }

    private function timeText(int $hours): string
    {
        if ($hours < 48) {
            return $hours . ' hours';
        }
        return (int) floor($hours / 24) . ' days';
    }
}