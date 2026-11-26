<?php
declare(strict_types=1);

namespace LifeXP\Controllers;

use LifeXP\Core\Csrf;
use LifeXP\Core\Session;
use LifeXP\Models\User;
use LifeXP\Services\Captcha;
use PDO;
use Twig\Environment;
use LifeXP\Services\XpService;
use LifeXP\Models\Stats;
use LifeXP\Services\ActivityLog;
use LifeXP\Services\RateLimiter;
use LifeXP\Core\Legal;
use LifeXP\Services\DeliveryService;

final class AuthController
{
    private User $users;
    private Stats $stats;
    private RateLimiter $limiter;

    public function __construct(private Environment $twig, private PDO $pdo)
    {
        $this->users   = new User($pdo);
        $this->stats   = new Stats($pdo);
        $this->limiter = new RateLimiter($pdo);
    }

    public function register(): void
    {
        $this->guestOnly();
        $errors = [];
        $old = ['username' => '', 'email' => '', 'accepted' => false];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // ---- Input filtering ----
            $username = trim((string) ($_POST['username'] ?? ''));
            $email    = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $confirm  = (string) ($_POST['password_confirm'] ?? '');
            $captcha  = (string) ($_POST['captcha'] ?? '');
            $accepted = isset($_POST['accept_terms']);
            $old = ['username' => $username, 'email' => $email, 'accepted' => $accepted];

            if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
                $errors[] = 'Your session expired. Please try again.';
            }
            if (!Captcha::verify($captcha)) {
                $errors[] = 'The characters did not match the image. Please try again.';
            }
            if (!$accepted) {
                $errors[] = 'You must accept the Terms of Use, Privacy Policy and Community Rules.';
            }
            if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $username)) {
                $errors[] = 'Username must be 3-20 characters: letters, numbers and underscores only.';
            }
            if (strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address.';
            }
            if (strlen($password) < 8 || strlen($password) > 72) {
                $errors[] = 'Password must be between 8 and 72 characters.';
            } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
                $errors[] = 'Password must contain at least one letter and one number.';
            }
            if ($password !== $confirm) {
                $errors[] = 'The two passwords do not match.';
            }

            if (!$errors) {
                if ($this->users->usernameExists($username)) {
                    $errors[] = 'That username is already taken.';
                }
                if ($this->users->emailExists($email)) {
                    $errors[] = 'That email is already registered.';
                }
            }

            if (!$errors) {
                // ---- Password encryption (hashing) ----
                $hash = password_hash($password, PASSWORD_DEFAULT);
                try {
                    $this->users->create($username, $email, $hash, Legal::TERMS_VERSION);
                } catch (\PDOException $e) {
                    if ($e->getCode() !== '23000') {   // 23000 = duplicate key (race condition)
                        throw $e;
                    }
                    $errors[] = 'That username or email is already registered.';
                }
                if (!$errors) {
                    Session::flash('success', 'Account created! You can now log in.');
                    $this->redirect('login');
                }
            }
        }

        echo $this->twig->render('pages/register.twig', [
            'errors'           => $errors,
            'old'              => $old,
            'captcha_question' => Captcha::generate(),
        ]);
    }

    public function login(): void
    {
        $this->guestOnly();
        $errors = [];
        $old = ['username' => ''];
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $captcha  = (string) ($_POST['captcha'] ?? '');
            $old = ['username' => $username];

            if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
                $errors[] = 'Your session expired. Please try again.';
            } elseif ($this->limiter->isLocked($ip, $username)) {
                $errors[] = 'Too many failed attempts. Please wait 15 minutes and try again.';
            } elseif (!Captcha::verify($captcha)) {
                $errors[] = 'The characters did not match the image. Please try again.';
            } else {
                $user = $this->users->findByUsername($username);

                // Always run password_verify so response time doesn't reveal whether the username exists
                $hash = $user['password_hash'] ?? password_hash('placeholder', PASSWORD_DEFAULT);
                $ok = password_verify($password, $hash) && $user !== null;

                if ($ok) {
                    $this->limiter->clear($ip, $username);
                    Session::login($user);
                    ActivityLog::record($this->pdo, (int) $user['id'], 'login', '');
                    Session::flash('success', 'Welcome back, ' . $user['username'] . '!');
                    $this->redirect('dashboard');
                }

                $this->limiter->recordFailure($ip, $username);
                ActivityLog::record($this->pdo, null, 'login_failed', $username);
                // One generic message for both "no such user" and "wrong password"
                $errors[] = 'Invalid username or password.';
            }
        }

        echo $this->twig->render('pages/login.twig', [
            'errors'           => $errors,
            'old'              => $old,
            'captcha_question' => Captcha::generate(),
        ]);
    }

    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::logout();
            Session::flash('success', 'You have been logged out.');
        }
        $this->redirect('login');
    }

    public function dashboard(): void
    {
        Session::requireLogin();

        $user = $this->users->findById((int) Session::userId());
        if ($user === null) {
            Session::logout();
            $this->redirect('login');
        }

        $uid = (int) $user['id'];

        echo $this->twig->render('pages/dashboard.twig', [
            'user'     => $user,
            'progress' => XpService::progress((int) $user['total_xp']),
            'streak'   => XpService::activeStreak((int) $user['current_streak'], $user['last_active_date']),
            'week'     => $this->stats->xpPerDay($uid),
            'cat'      => $this->stats->categoryBreakdown($uid),
            'rank'     => $this->stats->rankOf($uid),
        ]);
    }

    private function guestOnly(): void
    {
        if (Session::userId() !== null) {
            $this->redirect('dashboard');
        }
    }

    private function redirect(string $page): never
    {
        header('Location: index.php?page=' . $page);
        exit;
    }
}