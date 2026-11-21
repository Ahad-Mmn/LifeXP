<?php
declare(strict_types=1);

namespace LifeXP\Controllers;

use LifeXP\Core\Csrf;
use LifeXP\Core\Session;
use LifeXP\Models\Category;
use LifeXP\Models\Interest;
use LifeXP\Models\Stats;
use LifeXP\Models\User;
use LifeXP\Services\XpService;
use PDO;
use Twig\Environment;

final class ProfileController
{
    private const TABS = [
        'overview'  => 'Overview',
        'badges'    => 'Badges',
        'interests' => 'Interests',
    ];
    private const MIN_CATEGORIES = 3;
    private const BIO_MAX = 300;

    private User $users;
    private Stats $stats;
    private Interest $interests;
    private Category $categories;

    public function __construct(private Environment $twig, PDO $pdo)
    {
        $this->users      = new User($pdo);
        $this->stats      = new Stats($pdo);
        $this->interests  = new Interest($pdo);
        $this->categories = new Category($pdo);
    }

    public function show(): void
    {
        Session::requireLogin();

        $tab = (string) ($_GET['tab'] ?? 'overview');
        if (!array_key_exists($tab, self::TABS)) {
            $tab = 'overview';
        }

        $userId = (int) Session::userId();
        $user   = $this->users->findById($userId);
        if ($user === null) {
            Session::logout();
            header('Location: index.php?page=login');
            exit;
        }

        $data = [
            'tab'      => $tab,
            'tabs'     => self::TABS,
            'user'     => $user,
            'progress' => XpService::progress((int) $user['total_xp']),
        ];

        if ($tab === 'overview') {
            $badges = $this->stats->badgesForUser($userId);
            $data += [
                'streak'       => XpService::activeStreak((int) $user['current_streak'], $user['last_active_date']),
                'rank'         => $this->stats->rankOf($userId),
                'completed'    => $this->stats->completedCount($userId),
                'earned_count' => $this->countEarned($badges),
                'badge_total'  => count($badges),
                'bio_max'      => self::BIO_MAX,
            ];
        } elseif ($tab === 'badges') {
            $badges = $this->stats->badgesForUser($userId);
            $data += [
                'badges'       => $badges,
                'earned_count' => $this->countEarned($badges),
            ];
        } else {
            $chosen = $this->interests->categoryIds($userId);
            $data += [
                'categories' => $this->categories->all(),
                'chosen'     => $chosen,
                'groups'     => $this->interests->sampleGroups($chosen),
                'picked'     => $this->interests->templateIds($userId),
                'min'        => self::MIN_CATEGORIES,
            ];
        }

        echo $this->twig->render('pages/profile.twig', $data);
    }

    public function saveBio(): void
    {
        Session::requireLogin();
        $this->requirePost('overview');

        $bio = trim((string) ($_POST['bio'] ?? ''));
        if (!mb_check_encoding($bio, 'UTF-8')) {
            Session::flash('error', 'That text could not be saved.');
            $this->redirect('overview');
        }

        // Remove control characters but keep line breaks
        $bio = preg_replace('/[^\P{C}\n]+/u', '', $bio) ?? '';

        if (mb_strlen($bio) > self::BIO_MAX) {
            Session::flash('error', 'Your bio must be ' . self::BIO_MAX . ' characters or fewer.');
            $this->redirect('overview');
        }

        $this->users->updateBio((int) Session::userId(), $bio === '' ? null : $bio);
        Session::flash('success', 'Bio saved.');
        $this->redirect('overview');
    }

    public function saveCategories(): void
    {
        Session::requireLogin();
        $this->requirePost('interests');

        $valid  = array_map('intval', array_column($this->categories->all(), 'id'));
        $posted = array_map('intval', (array) ($_POST['categories'] ?? []));
        $chosen = array_values(array_unique(array_intersect($posted, $valid)));

        if (count($chosen) < self::MIN_CATEGORIES) {
            Session::flash('error', 'Please choose at least ' . self::MIN_CATEGORIES . ' categories.');
            $this->redirect('interests');
        }

        $this->interests->setCategories((int) Session::userId(), $chosen);
        Session::flash('success', 'Your categories are saved. New quests will come from these.');
        $this->redirect('interests');
    }

    public function saveSamples(): void
    {
        Session::requireLogin();
        $this->requirePost('interests');

        $userId = (int) Session::userId();
        $groups = $this->interests->sampleGroups($this->interests->categoryIds($userId));

        $offered = [];
        foreach ($groups as $group) {
            foreach ($group['samples'] as $sample) {
                $offered[] = (int) $sample['id'];
            }
        }

        $posted = array_map('intval', (array) ($_POST['templates'] ?? []));
        $picked = array_values(array_unique(array_intersect($posted, $offered)));

        $this->interests->setTemplates($userId, $picked);
        Session::flash('success', 'Your favourite quests are saved.');
        $this->redirect('interests');
    }

    private function countEarned(array $badges): int
    {
        return count(array_filter($badges, fn (array $b) => $b['earned_at'] !== null));
    }

    private function requirePost(string $tab): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::flash('error', 'Invalid request. Please try again.');
            $this->redirect($tab);
        }
    }

    private function redirect(string $tab): never
    {
        header('Location: index.php?page=profile&tab=' . $tab);
        exit;
    }
}