<?php
declare(strict_types=1);

namespace LifeXP\Models;

use PDO;
use Throwable;

final class Interest
{
    public function __construct(private PDO $pdo)
    {
    }

    /** Category ids the player has chosen */
    public function categoryIds(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT category_id FROM user_interests WHERE user_id = :u ORDER BY category_id');
        $stmt->execute(['u' => $userId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    /** Sample quest ids the player has ticked */
    public function templateIds(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT template_id FROM user_interest_templates WHERE user_id = :u');
        $stmt->execute(['u' => $userId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    /** Replaces the chosen categories and drops ticked samples from categories no longer chosen */
    public function setCategories(int $userId, array $categoryIds): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare('DELETE FROM user_interests WHERE user_id = :u')
                ->execute(['u' => $userId]);

            $insert = $this->pdo->prepare('INSERT INTO user_interests (user_id, category_id) VALUES (:u, :c)');
            foreach ($categoryIds as $categoryId) {
                $insert->execute(['u' => $userId, 'c' => (int) $categoryId]);
            }

            $this->pdo->prepare(
                'DELETE uit FROM user_interest_templates uit
                 JOIN quest_templates t ON t.id = uit.template_id
                 WHERE uit.user_id = :u
                   AND t.category_id NOT IN (SELECT category_id FROM user_interests WHERE user_id = :u2)'
            )->execute(['u' => $userId, 'u2' => $userId]);

            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    /** Replaces the ticked sample quests */
    public function setTemplates(int $userId, array $templateIds): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare('DELETE FROM user_interest_templates WHERE user_id = :u')
                ->execute(['u' => $userId]);

            $insert = $this->pdo->prepare(
                'INSERT IGNORE INTO user_interest_templates (user_id, template_id) VALUES (:u, :t)'
            );
            foreach ($templateIds as $templateId) {
                $insert->execute(['u' => $userId, 't' => (int) $templateId]);
            }

            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function markOnboarded(int $userId): void
    {
        $this->pdo->prepare('UPDATE users SET onboarded = 1 WHERE id = :u')
            ->execute(['u' => $userId]);
    }

    /**
     * For each chosen category: the category row plus one side quest per track.
     * The same query always returns the same samples, so a posted form can be checked against it.
     */
    public function sampleGroups(array $categoryIds): array
    {
        $catStmt = $this->pdo->prepare('SELECT id, name, color FROM categories WHERE id = :id');
        $sampleStmt = $this->pdo->prepare(
            "SELECT t.id, t.title, t.summary, t.track
             FROM quest_templates t
             JOIN (
                 SELECT MIN(id) AS id
                 FROM quest_templates
                 WHERE category_id = :c AND is_active = 1 AND quest_type = 'side'
                 GROUP BY track
             ) pick ON pick.id = t.id
             ORDER BY t.track"
        );

        $groups = [];
        foreach ($categoryIds as $categoryId) {
            $catStmt->execute(['id' => (int) $categoryId]);
            $category = $catStmt->fetch();
            if (!$category) {
                continue;
            }
            $sampleStmt->execute(['c' => (int) $categoryId]);
            $samples = $sampleStmt->fetchAll();
            if ($samples) {
                $groups[] = ['category' => $category, 'samples' => $samples];
            }
        }
        return $groups;
    }
}