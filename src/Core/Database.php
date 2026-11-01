<?php
declare(strict_types=1);

namespace LifeXP\Core;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connect(array $db): PDO
    {
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $db['host'], $db['name'], $db['charset']
            );

            self::$pdo = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,  // real prepared statements (SQL injection defence)
            ]);
        }

        return self::$pdo;
    }

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            throw new \RuntimeException('Database not connected yet.');
        }
        return self::$pdo;
    }
}