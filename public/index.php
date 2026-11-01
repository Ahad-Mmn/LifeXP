<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Autoload our own classes: LifeXP\Core\Database -> src/Core/Database.php
spl_autoload_register(function (string $class): void {
    $prefix = 'LifeXP\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

use LifeXP\Core\Database;

$config = require __DIR__ . '/../config/config.php';

if ($config['app']['debug']) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache'      => false,
    'autoescape' => 'html',
    'debug'      => $config['app']['debug'],
]);
$twig->addGlobal('app_name', $config['app']['name']);

$categories = [];
$dbError = null;

try {
    $pdo = Database::connect($config['db']);
    $categories = $pdo->query('SELECT id, name, color FROM categories ORDER BY name')->fetchAll();
} catch (\PDOException $e) {
    $dbError = $config['app']['debug'] ? $e->getMessage() : 'Database unavailable.';
}

echo $twig->render('pages/home.twig', [
    'player'     => 'Adventurer',
    'categories' => $categories,
    'db_error'   => $dbError,
]);