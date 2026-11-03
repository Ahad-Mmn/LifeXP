<?php
declare(strict_types=1);

namespace LifeXP\Controllers;

use LifeXP\Core\Session;
use LifeXP\Models\Stats;
use LifeXP\Services\XpService;
use PDO;
use Twig\Environment;

final class LeaderboardController
{
    private Stats $stats;

    public function __construct(private Environment $twig, PDO $pdo)
    {
        $this->stats = new Stats($pdo);
    }

    public function index(): void
    {
        Session::requireLogin();

        $rows = array_map(
            fn (array $r) => $r + ['title' => XpService::title((int) $r['level'])],
            $this->stats->leaderboard(10)
        );

        echo $this->twig->render('pages/leaderboard.twig', [
            'rows'    => $rows,
            'my_rank' => $this->stats->rankOf((int) Session::userId()),
        ]);
    }
}