<?php
declare(strict_types=1);

namespace LifeXP\Models;

use PDO;

final class Category
{
    public function __construct(private PDO $pdo)
    {
    }

    public function all(): array
    {
        return $this->pdo->query('SELECT id, name, color FROM categories ORDER BY name')->fetchAll();
    }
}