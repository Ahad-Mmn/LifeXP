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

use LifeXP\Controllers\AdminController;
use LifeXP\Controllers\AuthController;
use LifeXP\Core\Csrf;
use LifeXP\Core\Database;
use LifeXP\Core\Session;
use LifeXP\Controllers\QuestController;
use LifeXP\Controllers\AjaxController;
use LifeXP\Controllers\LeaderboardController;

$config = require __DIR__ . '/../config/config.php';

if ($config['app']['debug']) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

// Security headers
header_remove('X-Powered-By');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
header('Cache-Control: no-store');   // logged-in pages must not be cached
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header('Strict-Transport-Security: max-age=31536000');
}

Session::start();
date_default_timezone_set('Europe/London');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache'      => false,
    'autoescape' => 'html',   // output escaping
    'debug'      => $config['app']['debug'],
]);

$isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
$twig->addGlobal('app_name', $config['app']['name']);
$twig->addGlobal('current_user', Session::user());
$twig->addGlobal('csrf_token', Csrf::token());
$twig->addGlobal('flash', $isAjax ? [] : Session::pullFlash());

try {
    $pdo = Database::connect($config['db']);
} catch (\PDOException $e) {
    http_response_code(500);
    exit($config['app']['debug'] ? 'Database error: ' . htmlspecialchars($e->getMessage()) : 'Database unavailable.');
}

$page = (string) ($_GET['page'] ?? 'home');
$auth = new AuthController($twig, $pdo);
$questCtl = new QuestController($twig, $pdo);
$ajax = new AjaxController($pdo);
$leaderboard = new LeaderboardController($twig, $pdo);
$admin = new AdminController($twig, $pdo);

switch ($page) {
    case 'home':
        $categories = $pdo->query('SELECT id, name, color FROM categories ORDER BY name')->fetchAll();
        echo $twig->render('pages/home.twig', ['categories' => $categories]);
        break;
    case 'register':
        $auth->register();
        break;
    case 'login':
        $auth->login();
        break;
    case 'logout':
        $auth->logout();
        break;
    case 'dashboard':
        $auth->dashboard();
        break;
    case 'quests':
        $questCtl->index();
        break;
    case 'quest_create':
        $questCtl->create();
        break;
    case 'quest_edit':
        $questCtl->edit();
        break;
    case 'quest_delete':
        $questCtl->delete();
        break;
    case 'quest_complete':
        $questCtl->complete();
        break;
    case 'ajax_username':
        $ajax->usernameCheck();
        break;
    case 'ajax_complete':
        $ajax->complete();
        break;
    case 'leaderboard':
        $leaderboard->index();
        break;
    case 'admin':
        $admin->index();
        break;
    case 'admin_role':
        $admin->setRole();
        break;
    case 'admin_delete':
        $admin->deleteUser();
        break;
    default:
        http_response_code(404);
        echo $twig->render('pages/404.twig');
}