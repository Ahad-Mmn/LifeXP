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

final class AuthController
{
    private User $users;
    private Stats $stats;

    public function __construct(private Environment $twig, PDO $pdo)
    {
        $this->users = new User($pdo);
        $this->stats = new Stats($pdo);
    }

    public function register(): void
    {
        $this->guestOnly();
        $errors = [];
        $old = ['username' => '', 'email' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // ---- Input filtering ----
            $username = trim((string) ($_POST['username'] ?? ''));
            $email    = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $confirm  = (string) ($_POST['password_confirm'] ?? '');
            $captcha  = (string) ($_POST['captcha'] ?? '');
            $old = ['username' => $username, 'email' => $email];

            if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
                $errors[] = 'Your session expired. Please try again.';
            }
            if (!Captcha::verify($captcha)) {
                $errors[] = 'Wrong answer to the maths question.';
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
                    $this->users->create($username, $email, $hash);
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $captcha  = (string) ($_POST['captcha'] ?? '');
            $old = ['username' => $username];

            if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
                $errors[] = 'Your session expired. Please try again.';
            } elseif (!Captcha::verify($captcha)) {
                $errors[] = 'Wrong answer to the maths question.';
            } else {
                $user = $this->users->findByUsername($username);

                // Always run password_verify so response time doesn't reveal whether the username exists
                $hash = $user['password_hash'] ?? password_hash('placeholder', PASSWORD_DEFAULT);
                $ok = password_verify($password, $hash) && $user !== null;

                if ($ok) {
                    Session::login($user);
                    Session::flash('success', 'Welcome back, ' . $user['username'] . '!');
                    $this->redirect('dashboard');
                }
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
        Session::requireLogin();   // protected page

        $user = $this->users->findById((int) Session::userId());
        if ($user === null) {      // account was deleted while logged in
            Session::logout();
            $this->redirect('login');
        }

        $uid    = (int) $user['id'];
        $badges = $this->stats->badgesForUser($uid);

        echo $this->twig->render('pages/dashboard.twig', [
            'user'         => $user,
            'progress'     => XpService::progress((int) $user['total_xp']),
            'streak'       => XpService::activeStreak((int) $user['current_streak'], $user['last_active_date']),
            'week'         => $this->stats->xpPerDay($uid),
            'cat'          => $this->stats->categoryBreakdown($uid),
            'badges'       => $badges,
            'earned_count' => count(array_filter($badges, fn ($b) => $b['earned_at'] !== null)),
            'rank'         => $this->stats->rankOf($uid),
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