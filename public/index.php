<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache'      => false,
    'autoescape' => 'html',
    'debug'      => $config['app']['debug'],
]);

$twig->addGlobal('app_name', $config['app']['name']);

echo $twig->render('pages/home.twig', [
    'player' => 'Adventurer',
]);