<?php
declare(strict_types=1);

namespace LifeXP\Controllers;

use LifeXP\Core\Csrf;
use LifeXP\Core\Session;
use LifeXP\Models\AccountData;
use LifeXP\Models\Category;
use LifeXP\Models\Interest;
use LifeXP\Models\Stats;
use LifeXP\Models\User;
use LifeXP\Services\ActivityLog;
use LifeXP\Services\RateLimiter;
use LifeXP\Services\XpService;
use PDO;
use Throwable;
use Twig\Environment;

final class ProfileController
{
    private const TABS = [
        'overview'  => 'Overview',
        'badges'    => 'Badges',
        'interests' => 'Interests',
        'privacy'   => 'Privacy',
        'security'  => 'Security',
        'data'      => 'Data',
    ];
    private const HISTORY_LABELS = [
        'login'                 => 'Logged in',
        'password_changed'      => 'Password changed',
        'email_changed'         => 'Email changed',
        'password_check_failed' => 'Wrong password entered',
        'data_exported'         => 'Data downloaded',
    ];
    private const MIN_CATEGORIES = 3;
    private const BIO_MAX = 300;

    private User $users;
    private Stats $stats;
    private Interest $interests;
    private Category $categories;
    private RateLimiter $limiter;

    public function __construct(private Environment $twig, private PDO $pdo)
    {
        $this->users      = new User($pdo);
        $this->stats      = new Stats($pdo);
        $this->interests  = new Interest($pdo);
        $this->categories = new Category($pdo);
        $this->limiter    = new RateLimiter($pdo);
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
        } elseif ($tab === 'interests') {
            $chosen = $this->interests->categoryIds($userId);
            $data += [
                'categories' => $this->categories->all(),
                'chosen'     => $chosen,
                'groups'     => $this->interests->sampleGroups($chosen),
                'picked'     => $this->interests->templateIds($userId),
                'min'        => self::MIN_CATEGORIES,
            ];
        } elseif ($tab === 'security') {
            $data += ['history' => $this->history($userId)];
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

    public function savePrivacy(): void
    {
        Session::requireLogin();
        $this->requirePost('privacy');

        $visibility = (string) ($_POST['profile_visibility'] ?? '');
        if (!in_array($visibility, ['public', 'private'], true)) {
            Session::flash('error', 'Please choose who can see your profile.');
            $this->redirect('privacy');
        }

        $this->users->updatePrivacy(
            (int) Session::userId(),
            $visibility,
            isset($_POST['show_on_leaderboards']),
            isset($_POST['show_badges']),
            isset($_POST['show_activity'])
        );
        Session::flash('success', 'Privacy settings saved.');
        $this->redirect('privacy');
    }

    public function changePassword(): void
    {
        Session::requireLogin();
        $this->requirePost('security');

        $userId = (int) Session::userId();
        $user   = $this->users->findById($userId);
        if ($user === null) {
            $this->redirect('security');
        }

        $new     = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['new_password_confirm'] ?? '');
        $current = (string) ($_POST['current_password'] ?? '');

        $errors = [];
        if (strlen($new) < 8 || strlen($new) > 72) {
            $errors[] = 'The new password must be between 8 and 72 characters.';
        } elseif (!preg_match('/[A-Za-z]/', $new) || !preg_match('/\d/', $new)) {
            $errors[] = 'The new password must contain at least one letter and one number.';
        }
        if ($new !== $confirm) {
            $errors[] = 'The two new passwords do not match.';
        }
        if ($new === $current) {
            $errors[] = 'The new password must be different from the current one.';
        }
        if ($errors) {
            foreach ($errors as $error) {
                Session::flash('error', $error);
            }
            $this->redirect('security');
        }

        if (!$this->confirmPassword($user)) {
            $this->redirect('security');
        }

        $this->users->updatePassword($userId, password_hash($new, PASSWORD_DEFAULT));
        session_regenerate_id(true);
        ActivityLog::record($this->pdo, $userId, 'password_changed', '');
        Session::flash('success', 'Your password has been changed.');
        $this->redirect('security');
    }

    public function changeEmail(): void
    {
        Session::requireLogin();
        $this->requirePost('security');

        $userId = (int) Session::userId();
        $user   = $this->users->findById($userId);
        if ($user === null) {
            $this->redirect('security');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        if (strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid email address.');
            $this->redirect('security');
        }
        if (strcasecmp($email, (string) $user['email']) === 0) {
            Session::flash('error', 'That is already your email address.');
            $this->redirect('security');
        }
        if (!$this->confirmPassword($user)) {
            $this->redirect('security');
        }
        if ($this->users->emailExists($email)) {
            Session::flash('error', 'That email is already registered.');
            $this->redirect('security');
        }

        try {
            $this->users->updateEmail($userId, $email);
        } catch (\PDOException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }
            Session::flash('error', 'That email is already registered.');
            $this->redirect('security');
        }

        ActivityLog::record($this->pdo, $userId, 'email_changed', '');
        Session::flash('success', 'Your email address has been updated.');
        $this->redirect('security');
    }

    public function deleteAccount(): void
    {
        Session::requireLogin();
        $this->requirePost('security');

        $userId = (int) Session::userId();
        $user   = $this->users->findById($userId);
        if ($user === null) {
            $this->redirect('security');
        }

        if (trim((string) ($_POST['confirm_text'] ?? '')) !== 'DELETE') {
            Session::flash('error', 'Type DELETE in capital letters to confirm.');
            $this->redirect('security');
        }
        if (!$this->confirmPassword($user)) {
            $this->redirect('security');
        }
        if ($user['role'] === 'admin' && $this->users->countAdmins() <= 1) {
            Session::flash('error', 'You are the only admin. Make someone else an admin before deleting this account.');
            $this->redirect('security');
        }

        $this->pdo->beginTransaction();
        try {
            // Remove the address from the player's old log rows before the link to them is cleared
            $this->pdo->prepare("UPDATE activity_log SET ip = '' WHERE user_id = :u")
                ->execute(['u' => $userId]);
            $this->users->deleteUser($userId);
            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }

        ActivityLog::record($this->pdo, null, 'account_deleted', 'user id ' . $userId);

        Session::logout();
        Session::flash('success', 'Your account and its data have been deleted.');
        header('Location: index.php?page=home');
        exit;
    }

    public function export(): void
    {
        Session::requireLogin();
        $this->requirePost('data');

        $userId = (int) Session::userId();
        $json   = json_encode(
            (new AccountData($this->pdo))->forUser($userId),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
        ActivityLog::record($this->pdo, $userId, 'data_exported', '');

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="lifexp-data-' . date('Ymd') . '.json"');
        header('Content-Length: ' . strlen($json));
        echo $json;
        exit;
    }

    // ------------------------------------------------------------------

    /** Checks the current password from the form, counting wrong attempts against the rate limiter */
    private function confirmPassword(array $user): bool
    {
        $ip  = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $key = 'pw:' . $user['username'];

        if ($this->limiter->isLocked($ip, $key)) {
            Session::flash('error', 'Too many wrong passwords. Please wait 15 minutes and try again.');
            return false;
        }

        $hash  = $this->users->passwordHash((int) $user['id']);
        $given = (string) ($_POST['current_password'] ?? '');

        if ($hash === null || !password_verify($given, $hash)) {
            $this->limiter->recordFailure($ip, $key);
            ActivityLog::record($this->pdo, (int) $user['id'], 'password_check_failed', '');
            Session::flash('error', 'Your current password is not correct.');
            return false;
        }

        $this->limiter->clear($ip, $key);
        return true;
    }

    private function history(int $userId): array
    {
        return array_map(
            fn (array $row) => $row + ['label' => self::HISTORY_LABELS[$row['action']] ?? $row['action']],
            ActivityLog::forUser($this->pdo, $userId, 15)
        );
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