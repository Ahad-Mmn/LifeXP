<?php
declare(strict_types=1);

namespace LifeXP\Controllers;

use DateTimeImmutable;
use LifeXP\Core\Session;
use LifeXP\Models\Category;
use LifeXP\Models\Leaderboard;
use LifeXP\Services\XpService;
use PDO;
use Twig\Environment;

final class LeaderboardController
{
    private const VALUE_LABELS = [
        'xp'        => 'XP',
        'completed' => 'Quests',
        'streak'    => 'Days',
        'level'     => 'Total XP',
    ];

    private Leaderboard $board;
    private Category $categories;

    public function __construct(private Environment $twig, PDO $pdo)
    {
        $this->board      = new Leaderboard($pdo);
        $this->categories = new Category($pdo);
    }

    public function index(): void
    {
        Session::requireLogin();

        $userId     = (int) Session::userId();
        $now        = new DateTimeImmutable('now');
        $categories = $this->categories->all();
        $filters    = $this->cleanFilters($_GET, $categories);
        $applies    = in_array($filters['metric'], ['xp', 'completed'], true);

        $rows = $this->rank($this->board->top($filters, $now), $userId);
        $me   = $this->board->position($filters, $userId, $now);

        $data = [
            'filters'     => $filters,
            'applies'     => $applies,
            'heading'     => $this->heading($filters, $categories, $applies),
            'value_label' => self::VALUE_LABELS[$filters['metric']],
            'rows'        => $rows,
            'me'          => $me,
            'in_top'      => (bool) array_filter($rows, fn (array $r) => $r['is_me']),
            'limit'       => Leaderboard::LIMIT,
        ];

        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
            header('Vary: X-Requested-With');
            header('Cache-Control: no-store');
            echo $this->twig->render('partials/board_results.twig', $data);
            return;
        }

        echo $this->twig->render('pages/leaderboard.twig', $data + [
            'categories' => $categories,
            'metrics'    => Leaderboard::METRICS,
            'periods'    => Leaderboard::PERIODS,
            'types'      => Leaderboard::TYPES,
        ]);
    }

    /** Input filtering: every value is checked against a fixed list, and anything else is replaced by a default */
    private function cleanFilters(array $get, array $categories): array
    {
        $metric = (string) ($get['metric'] ?? 'xp');
        if (!array_key_exists($metric, Leaderboard::METRICS)) {
            $metric = 'xp';
        }

        $period = (string) ($get['period'] ?? 'week');
        if (!array_key_exists($period, Leaderboard::PERIODS)) {
            $period = 'week';
        }

        $type = (string) ($get['type'] ?? 'delivered');
        if (!array_key_exists($type, Leaderboard::TYPES)) {
            $type = 'delivered';
        }

        $categoryId = (int) ($get['category'] ?? 0);
        $valid      = array_map('intval', array_column($categories, 'id'));
        if (!in_array($categoryId, $valid, true)) {
            $categoryId = 0;
        }

        return ['metric' => $metric, 'period' => $period, 'type' => $type, 'category_id' => $categoryId];
    }

    /** Adds ranks (ties share one) and drops everything the page does not need to show */
    private function rank(array $raw, int $userId): array
    {
        $rows = [];
        $rank = 0;
        $previous = null;

        foreach ($raw as $i => $r) {
            $value = (int) $r['metric_value'];
            if ($value !== $previous) {
                $rank = $i + 1;
                $previous = $value;
            }
            $level = (int) $r['level'];

            $rows[] = [
                'rank'     => $rank,
                'username' => $r['username'],
                'level'    => $level,
                'title'    => XpService::title($level),
                'value'    => $value,
                'is_me'    => (int) $r['id'] === $userId,
            ];
        }
        return $rows;
    }

    private function heading(array $f, array $categories, bool $applies): string
    {
        $parts = [Leaderboard::METRICS[$f['metric']]];

        if ($applies) {
            foreach ($categories as $c) {
                if ((int) $c['id'] === $f['category_id']) {
                    $parts[] = $c['name'];
                }
            }
            $parts[] = Leaderboard::PERIODS[$f['period']];
            $parts[] = Leaderboard::TYPES[$f['type']];
        }
        return implode(' · ', $parts);
    }
}