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

use LifeXP\Core\Csrf;
use LifeXP\Core\Database;
use LifeXP\Core\Session;
use LifeXP\Core\Legal;
use LifeXP\Controllers\AdminController;
use LifeXP\Controllers\AuthController;
use LifeXP\Controllers\QuestController;
use LifeXP\Controllers\AjaxController;
use LifeXP\Controllers\LeaderboardController;
use LifeXP\Controllers\OnboardingController;
use LifeXP\Controllers\ProfileController;
use LifeXP\Controllers\HomeController;
use LifeXP\Services\DeliveryService;
use LifeXP\Services\Captcha;

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
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; font-src 'self'; connect-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
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
$twig->addGlobal('current_page', (string) ($_GET['page'] ?? 'home'));
$twig->addGlobal('app_name', $config['app']['name']);
$twig->addGlobal('current_user', Session::user());
$twig->addGlobal('csrf_token', Csrf::token());
$twig->addGlobal('legal_version', Legal::TERMS_VERSION);
$twig->addGlobal('legal_updated', Legal::UPDATED);

set_exception_handler(function (\Throwable $e) use ($twig, $config): void {
    error_log('LifeXP error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());

    if (!headers_sent()) {
        http_response_code(500);
    }

    try {
        echo $twig->render('pages/500.twig', [
            'detail' => $config['app']['debug'] ? $e->getMessage() : null,
        ]);
    } catch (\Throwable $inner) {
        echo 'Something went wrong. Please try again later.';
    }
});

$pdo = Database::connect($config['db']);

$page = (string) ($_GET['page'] ?? 'home');
$openPages = ['home', 'home_board', 'forum', 'logout', 'onboarding', 'onboarding_samples', 'terms', 'privacy_policy', 'community_rules'];    
if (Session::userId() !== null && !Session::isOnboarded() && !in_array($page, $openPages, true)) {
    header('Location: index.php?page=onboarding');
    exit;
}

if (!$isAjax && Session::userId() !== null && Session::isOnboarded() && in_array($page, ['home', 'quests', 'dashboard'], true)) {
    DeliveryService::sync($pdo, (int) Session::userId());
}
$twig->addGlobal('flash', $isAjax ? [] : Session::pullFlash());

$auth = new AuthController($twig, $pdo);
$questCtl = new QuestController($twig, $pdo);
$ajax = new AjaxController($pdo);
$leaderboard = new LeaderboardController($twig, $pdo);
$admin = new AdminController($twig, $pdo);
$onboarding = new OnboardingController($twig, $pdo);
$profile = new ProfileController($twig, $pdo);
$home = new HomeController($twig, $pdo);

switch ($page) {
    case 'home':
    case 'home':
        $home->index();
        break;
    case 'home_board':
        $home->board();
        break;
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
    case 'forum':
        $categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
        echo $twig->render('pages/forum.twig', ['categories' => $categories]);
        break;
    case 'onboarding':
        $onboarding->categories();
        break;
    case 'onboarding_samples':
        $onboarding->samples();
        break;
    case 'profile':
        $profile->show();
        break;
    case 'profile_bio':
        $profile->saveBio();
        break;
    case 'profile_categories':
        $profile->saveCategories();
        break;
    case 'profile_samples':
        $profile->saveSamples();
        break;
    case 'profile_privacy':
        $profile->savePrivacy();
        break;
    case 'profile_password':
        $profile->changePassword();
        break;
    case 'profile_email':
        $profile->changeEmail();
        break;
    case 'profile_delete':
        $profile->deleteAccount();
        break;
    case 'profile_export':
        $profile->export();
        break;
    case 'terms':
        echo $twig->render('pages/terms.twig');
        break;
    case 'privacy_policy':
        echo $twig->render('pages/privacy_policy.twig');
        break;
    case 'community_rules':
        echo $twig->render('pages/community_rules.twig');
        break;
    case 'profile_terms':
        $profile->acceptTerms();
        break;
    case 'quest_view':
        $questCtl->view();
        break;
    case 'quest_accept':
        $questCtl->acceptBonus();
        break;
    case 'quest_decline':
        $questCtl->declineBonus();
        break;
    case 'quest_skip':
        $questCtl->skip();
        break;
    case 'captcha_image':
        Captcha::image(isset($_GET['new']));
    default:
        http_response_code(404);
        echo $twig->render('pages/404.twig');
}