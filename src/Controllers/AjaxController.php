<?php
declare(strict_types=1);

namespace LifeXP\Controllers;

use LifeXP\Core\Csrf;
use LifeXP\Core\Session;
use LifeXP\Models\User;
use LifeXP\Services\CompletionService;
use LifeXP\Services\XpService;
use PDO;

final class AjaxController
{
    public function __construct(private PDO $pdo)
    {
    }

    /** GET: is this username free? */
    public function usernameCheck(): void
    {
        // Throttle: max 30 checks per minute per session
        $now  = time();
        $hits = array_filter($_SESSION['username_checks'] ?? [], fn ($t) => $t > $now - 60);
        if (count($hits) >= 30) {
            $this->json(['available' => false, 'message' => 'Too many checks. Wait a moment.'], 429);
        }
        $hits[] = $now;
        $_SESSION['username_checks'] = $hits;

        $name = trim((string) ($_GET['u'] ?? ''));
        if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $name)) {
            $this->json(['available' => false, 'message' => '3-20 characters: letters, numbers, underscores.']);
        }

        $taken = (new User($this->pdo))->usernameExists($name);
        $this->json([
            'available' => !$taken,
            'message'   => $taken ? 'Already taken.' : 'Available!',
        ]);
    }

    /** POST: complete a quest and return the result as JSON */
    public function complete(): void
    {
        if (Session::userId() === null) {
            $this->json(['ok' => false, 'message' => 'Please log in again.'], 401);
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verify($_POST['csrf_token'] ?? null)) {
            $this->json(['ok' => false, 'message' => 'Invalid request. Reload the page.'], 400);
        }

        $userId = (int) Session::userId();
        $result = (new CompletionService($this->pdo))->complete((int) ($_POST['id'] ?? 0), $userId);

        if ($result === null) {
            $this->json(['ok' => false, 'message' => 'Quest not found or already completed.'], 409);
        }

        $user     = (new User($this->pdo))->findById($userId);
        $progress = XpService::progress((int) $user['total_xp']);

        $this->json([
            'ok'         => true,
            'earned'     => $result['earned'],
            'bonus_pct'  => $result['bonus_pct'],
            'streak'     => $result['streak'],
            'leveled_up' => $result['leveled_up'],
            'new_level'  => $result['new_level'],
            'new_title'  => $result['new_title'],
            'total_xp'   => (int) $user['total_xp'],
            'percent'    => $progress['percent'],
        ]);
    }

    private function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($data, JSON_THROW_ON_ERROR);
        exit;
    }
}