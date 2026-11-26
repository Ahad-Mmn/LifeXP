<?php
declare(strict_types=1);

namespace LifeXP\Controllers;

use DateTimeImmutable;
use LifeXP\Core\Cache;
use LifeXP\Core\Session;
use LifeXP\Models\Category;
use LifeXP\Models\Leaderboard;
use LifeXP\Models\Quest;
use LifeXP\Models\Site;
use LifeXP\Models\User;
use LifeXP\Services\XpService;
use PDO;
use Twig\Environment;

final class HomeController
{
    private const WIDGET_ROWS = 5;
    private const PERIODS = ['week' => 'This week', 'all' => 'All time'];

    private Site $site;
    private Leaderboard $leaderboard;
    private Category $categories;
    private User $users;
    private Quest $quests;

    public function __construct(private Environment $twig, PDO $pdo)
    {
        $this->site        = new Site($pdo);
        $this->leaderboard = new Leaderboard($pdo);
        $this->categories  = new Category($pdo);
        $this->users       = new User($pdo);
        $this->quests      = new Quest($pdo);
    }

    public function index(): void
    {
        $site = Cache::remember('site', fn () => [
            'totals'   => $this->site->totals(),
            'cards'    => $this->site->categoryCards(),
            'featured' => $this->site->featuredQuests(),
            'badges'   => $this->site->badgeTeaser(),
        ]);

        $filters = $this->cleanFilters($_GET, $site['cards']);

        echo $this->twig->render('pages/home.twig', [
            'totals'     => $site['totals'],
            'cards'      => $site['cards'],
            'featured'   => $site['featured'],
            'badges'     => $site['badges'],
            'player'     => $this->player(),
            'onboarded'  => Session::isOnboarded(),
            'periods'    => self::PERIODS,
            'filters'    => $filters,
            'heading'    => $this->heading($filters, $site['cards']),
            'rows'       => $this->boardRows($filters),
        ]);
    }

    /** Public Ajax endpoint: only the top players widget */
    public function board(): void
    {
        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'fetch') {
            header('Location: index.php?page=home');
            exit;
        }

        $categories = $this->categories->all();
        $filters    = $this->cleanFilters($_GET, $categories);

        header('Vary: X-Requested-With');
        echo $this->twig->render('partials/home_board.twig', [
            'filters' => $filters,
            'heading' => $this->heading($filters, $categories),
            'rows'    => $this->boardRows($filters),
        ]);
    }

    /** The logged-in player's own summary. Never cached. */
    private function player(): ?array
    {
        $userId = Session::userId();
        if ($userId === null) {
            return null;
        }

        $user = $this->users->findById($userId);
        if ($user === null) {
            return null;
        }

        return [
            'user'     => $user,
            'progress' => XpService::progress((int) $user['total_xp']),
            'streak'   => XpService::activeStreak((int) $user['current_streak'], $user['last_active_date']),
            'open'     => $this->quests->openCounts($userId),
        ];
    }

    /** Input filtering: values are checked against fixed lists, and anything else is replaced by a default */
    private function cleanFilters(array $get, array $categories): array
    {
        $period = (string) ($get['period'] ?? 'week');
        if (!array_key_exists($period, self::PERIODS)) {
            $period = 'week';
        }

        $categoryId = (int) ($get['category'] ?? 0);
        $valid      = array_map('intval', array_column($categories, 'id'));
        if (!in_array($categoryId, $valid, true)) {
            $categoryId = 0;
        }

        return ['period' => $period, 'category_id' => $categoryId];
    }

    /** Top rows for the widget, ranked with ties, containing nothing but what is shown */
    private function boardRows(array $filters): array
    {
        $key = 'board:' . $filters['category_id'] . ':' . $filters['period'];

        $cached = Cache::remember($key, function () use ($filters): array {
            $raw = $this->leaderboard->top([
                'metric'      => 'xp',
                'period'      => $filters['period'],
                'type'        => 'delivered',
                'category_id' => $filters['category_id'],
            ], new DateTimeImmutable('now'));

            $rows     = [];
            $rank     = 0;
            $previous = null;

            foreach (array_slice($raw, 0, self::WIDGET_ROWS) as $i => $r) {
                $value = (int) $r['metric_value'];
                if ($value !== $previous) {
                    $rank     = $i + 1;
                    $previous = $value;
                }
                $level  = (int) $r['level'];
                $rows[] = [
                    'rank'     => $rank,
                    'username' => $r['username'],
                    'level'    => $level,
                    'title'    => XpService::title($level),
                    'value'    => $value,
                ];
            }
            return ['rows' => $rows];
        });

        return $cached['rows'];
    }

    private function heading(array $filters, array $categories): string
    {
        $name = 'All categories';
        foreach ($categories as $c) {
            if ((int) $c['id'] === $filters['category_id']) {
                $name = $c['name'];
            }
        }
        return 'Top players - ' . $name . ' - ' . self::PERIODS[$filters['period']];
    }
}