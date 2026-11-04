<?php
declare(strict_types=1);

namespace LifeXP\Controllers;

use LifeXP\Core\Csrf;
use LifeXP\Core\Session;
use LifeXP\Models\Stats;
use LifeXP\Models\User;
use LifeXP\Services\ActivityLog;
use PDO;
use Twig\Environment;

final class AdminController
{
    private User $users;
    private Stats $stats;

    public function __construct(private Environment $twig, private PDO $pdo)
    {
        $this->users = new User($pdo);
        $this->stats = new Stats($pdo);
    }

    public function index(): void
    {
        $this->guard();

        echo $this->twig->render('pages/admin.twig', [
            'totals' => $this->stats->siteTotals(),
            'users'  => $this->users->listWithCounts(),
            'log'    => ActivityLog::recent($this->pdo, 25),
        ]);
    }

    public function setRole(): void
    {
        $me = $this->guard();
        $this->requirePost();

        $id   = (int) ($_POST['id'] ?? 0);
        $role = (string) ($_POST['role'] ?? '');

        if (!in_array($role, ['user', 'admin'], true)) {
            Session::flash('error', 'Invalid role.');
        } elseif ($id === (int) $me['id']) {
            Session::flash('error', 'You cannot change your own role.');
        } elseif (($target = $this->users->findById($id)) === null) {
            Session::flash('error', 'User not found.');
        } else {
            $this->users->setRole($id, $role);
            ActivityLog::record($this->pdo, (int) $me['id'], 'role_changed',
                $target['username'] . ' -> ' . $role);
            Session::flash('success', $target['username'] . ' is now ' . $role . '.');
        }
        $this->redirect();
    }

    public function deleteUser(): void
    {
        $me = $this->guard();
        $this->requirePost();

        $id = (int) ($_POST['id'] ?? 0);

        if ($id === (int) $me['id']) {
            Session::flash('error', 'You cannot delete your own account here.');
        } elseif (($target = $this->users->findById($id)) === null) {
            Session::flash('error', 'User not found.');
        } else {
            $this->users->deleteUser($id);
            ActivityLog::record($this->pdo, (int) $me['id'], 'user_deleted', $target['username']);
            Session::flash('success', 'User ' . $target['username'] . ' deleted.');
        }
        $this->redirect();
    }

    // ------------------------------------------------------------------

    /** Logged in AND admin in the database right now. Returns the admin's row. */
    private function guard(): array
    {
        Session::requireLogin();
        $me = $this->users->findById((int) Session::userId());

        if ($me === null || $me['role'] !== 'admin') {
            http_response_code(403);
            echo $this->twig->render('pages/403.twig');
            exit;
        }
        return $me;
    }

    private function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::flash('error', 'Invalid request.');
            $this->redirect();
        }
    }

    private function redirect(): never
    {
        header('Location: index.php?page=admin');
        exit;
    }
}