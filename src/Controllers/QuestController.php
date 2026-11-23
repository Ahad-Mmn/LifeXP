<?php
declare(strict_types=1);

namespace LifeXP\Controllers;

use DateTimeImmutable;
use LifeXP\Core\Csrf;
use LifeXP\Core\Session;
use LifeXP\Models\Category;
use LifeXP\Models\Quest;
use LifeXP\Services\BonusService;
use LifeXP\Services\CompletionRefused;
use LifeXP\Services\CompletionService;
use LifeXP\Services\DeliveryService;
use PDO;
use Twig\Environment;
use LifeXP\Services\SkipService;

final class QuestController
{
    private const MAX_ACTIVE_CUSTOM = 10;

    private Quest $quests;
    private Category $categories;

    public function __construct(private Environment $twig, private PDO $pdo)
    {
        $this->quests = new Quest($pdo);
        $this->categories = new Category($pdo);
    }

    /** READ + SEARCH: full page normally, just the results when called by Ajax */
    public function index(): void
    {
        Session::requireLogin();
        $userId = (int) Session::userId();
        $ajax   = $this->isAjax();

        $filters = $this->cleanFilters($_GET);
        $quests  = $this->decorate($this->quests->search($userId, $filters));

        $active = array_filter([
            $filters['q'], $filters['category_id'], $filters['type'], $filters['difficulty'],
            $filters['status'], $filters['due_from'], $filters['due_to'],
        ]);

        $data = [
            'quests'      => $quests,
            'filters'     => $filters,
            'has_filters' => count($active) > 0,
        ];

        if ($ajax) {
            header('Vary: X-Requested-With');
            header('Cache-Control: no-store');
            echo $this->twig->render('partials/quest_results.twig', $data);
            return;
        }

        echo $this->twig->render('pages/quests.twig', $data + [
            'offers'       => $this->decorate($this->quests->offersFor($userId, $this->now())),
            'free_skips'   => SkipService::freeSkips($this->pdo, $userId),
            'categories'   => $this->categories->all(),
            'types'        => Quest::TYPES,
            'difficulties' => array_keys(Quest::XP),
            'sorts'        => [
                'due' => 'Due date', 'newest' => 'Newest first',
                'xp'  => 'Most XP',  'title'  => 'Title A-Z',
            ],
        ]);
    }

    /** Detail view: a full page, or just the panel when called by Ajax */
    public function view(): void
    {
        Session::requireLogin();
        $userId = (int) Session::userId();

        $quest = $this->quests->findDetailed((int) ($_GET['id'] ?? 0), $userId);
        if ($quest === null) {
            $this->notFound();
            return;
        }
        $quest = $this->decorate([$quest])[0];
        $skip  = SkipService::preview($quest, SkipService::freeSkips($this->pdo, $userId));

        $ajax = $this->isAjax();
        if ($ajax) {
            header('Vary: X-Requested-With');
            header('Cache-Control: no-store');
        }

        echo $this->twig->render(
            $ajax ? 'partials/quest_detail.twig' : 'pages/quest_view.twig',
            ['q' => $quest, 'skip' => $skip, 'in_dialog' => $ajax]
        );
    }

    /** CREATE a custom quest */
    public function create(): void
    {
        Session::requireLogin();
        $userId = (int) Session::userId();
        $errors = [];
        $form = ['title' => '', 'description' => '', 'category_id' => 0, 'difficulty' => 'easy', 'due_date' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$data, $errors] = $this->validate($_POST);
            $form = $this->formValues($_POST);

            if (!$errors && $this->quests->countActiveCustom($userId) >= self::MAX_ACTIVE_CUSTOM) {
                $errors[] = 'You can have up to ' . self::MAX_ACTIVE_CUSTOM . ' active custom quests. Finish or delete one first.';
            }

            if (!$errors) {
                $this->quests->create($userId, $data);
                Session::flash('success', 'Custom quest created!');
                $this->redirect('quests');
            }
        }

        $this->renderForm('create', $form, $errors, 'index.php?page=quest_create');
    }

    /** UPDATE: only your own active custom quests */
    public function edit(): void
    {
        Session::requireLogin();
        $userId = (int) Session::userId();
        $id     = (int) ($_GET['id'] ?? 0);

        $quest = $this->quests->find($id, $userId);
        if ($quest === null) {
            $this->notFound();
            return;
        }
        if ($quest['quest_type'] !== 'custom' || $quest['status'] !== 'active') {
            Session::flash('error', 'Only your own active custom quests can be edited.');
            $this->redirect('quests');
        }

        $errors = [];
        $form = [
            'title'       => $quest['title'],
            'description' => (string) $quest['description'],
            'category_id' => (int) $quest['category_id'],
            'difficulty'  => $quest['difficulty'] === 'epic' ? 'hard' : $quest['difficulty'],
            'due_date'    => (string) $quest['due_date'],
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$data, $errors] = $this->validate($_POST);
            $form = $this->formValues($_POST);

            if (!$errors) {
                $this->quests->update($id, $userId, $data);
                Session::flash('success', 'Quest updated!');
                $this->redirect('quests');
            }
        }

        $this->renderForm('edit', $form, $errors, 'index.php?page=quest_edit&id=' . $id);
    }

    /** DELETE (POST only): only your own active custom quests */
    public function delete(): void
    {
        Session::requireLogin();
        $this->requirePost();

        $userId = (int) Session::userId();
        $id     = (int) ($_POST['id'] ?? 0);
        $quest  = $this->quests->find($id, $userId);

        if ($quest === null) {
            Session::flash('error', 'Quest not found.');
            $this->redirect('quests');
        }
        if ($quest['quest_type'] !== 'custom' || $quest['status'] !== 'active') {
            Session::flash('error', 'Only your own active custom quests can be deleted. Delivered quests can only be completed.');
            $this->redirect('quests');
        }

        $this->quests->delete($id, $userId);
        Session::flash('success', 'Quest deleted.');
        $this->redirect('quests');
    }

    /** COMPLETE (POST only): awards XP, updates level and streak */
    public function complete(): void
    {
        Session::requireLogin();
        $this->requirePost();

        $userId = (int) Session::userId();
        $id     = (int) ($_POST['id'] ?? 0);

        try {
            $result = (new CompletionService($this->pdo))->complete($id, $userId);
        } catch (CompletionRefused $e) {
            Session::flash('error', $e->getMessage());
            $this->redirect('quests');
        }

        if ($result === null) {
            Session::flash('error', 'That quest was not found, is already finished or has run out of time.');
            $this->redirect('quests');
        }

        DeliveryService::sync($this->pdo, $userId);

        if ($result['earned'] > 0) {
            $msg = 'Quest complete! +' . $result['earned'] . ' XP';
            if ($result['bonus_pct'] > 0) {
                $msg .= ' (includes +' . $result['bonus_pct'] . '% streak bonus)';
            }
        } else {
            $msg = 'Quest complete.';
        }
        Session::flash('success', $msg);

        $note = CompletionService::noteText($result['note']);
        if ($note !== null) {
            Session::flash('error', $note);
        }
        if ($result['leveled_up']) {
            Session::flash('success', 'LEVEL UP! You are now level ' . $result['new_level']
                . ' - ' . $result['new_title'] . '!');
        }
        foreach ($result['new_badges'] as $badgeName) {
            Session::flash('success', 'Badge unlocked: ' . $badgeName . '!');
        }

        $this->redirect('quests');
    }

    public function acceptBonus(): void
    {
        Session::requireLogin();
        $this->requirePost();

        $ok = BonusService::accept($this->pdo, (int) ($_POST['id'] ?? 0), (int) Session::userId());
        Session::flash(
            $ok ? 'success' : 'error',
            $ok ? 'Bonus quest accepted. The clock is running!' : 'That offer is no longer available.'
        );
        $this->redirect('quests');
    }

    public function declineBonus(): void
    {
        Session::requireLogin();
        $this->requirePost();

        $ok = BonusService::decline($this->pdo, (int) ($_POST['id'] ?? 0), (int) Session::userId());
        Session::flash(
            $ok ? 'success' : 'error',
            $ok ? 'Offer declined.' : 'That offer is no longer available.'
        );
        $this->redirect('quests');
    }

        /** SKIP (POST only): free while free skips last, then it costs XP */
    public function skip(): void
    {
        Session::requireLogin();
        $this->requirePost();

        $userId = (int) Session::userId();
        $result = SkipService::skip($this->pdo, (int) ($_POST['id'] ?? 0), $userId);

        if ($result === null) {
            Session::flash('error', 'That quest was not found, is no longer active or has run out of time.');
            $this->redirect('quests');
        }

        if ($result['type'] === 'bonus') {
            Session::flash('success', 'Bonus quest skipped.');
        } elseif ($result['used_free']) {
            Session::flash('success', 'Quest skipped. No XP lost. Free ' . $result['type']
                . ' skips left: ' . $result['free_left'] . '.');
        } elseif ($result['penalty'] > 0) {
            Session::flash('error', 'Quest skipped. You lost ' . $result['penalty'] . ' XP.');
        } else {
            Session::flash('success', 'Quest skipped.');
        }

        if ($result['level_dropped']) {
            Session::flash('error', 'Your level dropped to ' . $result['new_level']
                . ' (' . $result['new_title'] . ').');
        }

        DeliveryService::sync($this->pdo, $userId);
        $this->redirect('quests');
    }

    // ------------------------------------------------------------------

    /** Input filtering for custom quests: returns [cleanData, errors] */
    private function validate(array $post): array
    {
        $errors = [];

        if (!Csrf::verify($post['csrf_token'] ?? null)) {
            $errors[] = 'Your session expired. Please try again.';
        }

        $title = trim((string) ($post['title'] ?? ''));
        $description = trim((string) ($post['description'] ?? ''));
        $categoryId = (int) ($post['category_id'] ?? 0);
        $difficulty = (string) ($post['difficulty'] ?? '');
        $dueRaw = trim((string) ($post['due_date'] ?? ''));

        $len = mb_strlen($title);
        if ($len < 3 || $len > 100) {
            $errors[] = 'Title must be between 3 and 100 characters.';
        }
        if (mb_strlen($description) > 1000) {
            $errors[] = 'Description must be 1000 characters or fewer.';
        }

        $validCategoryIds = array_map('intval', array_column($this->categories->all(), 'id'));
        if (!in_array($categoryId, $validCategoryIds, true)) {
            $errors[] = 'Please choose a valid category.';
        }

        if (!array_key_exists($difficulty, Quest::CUSTOM_XP)) {
            $errors[] = 'Please choose a valid difficulty.';
        }

        $due = null;
        if ($dueRaw !== '') {
            $dt = \DateTime::createFromFormat('Y-m-d', $dueRaw);
            if ($dt === false || $dt->format('Y-m-d') !== $dueRaw) {
                $errors[] = 'Due date is not a valid date.';
            } else {
                $due = $dueRaw;
            }
        }

        return [[
            'title'       => $title,
            'description' => $description === '' ? null : $description,
            'category_id' => $categoryId,
            'difficulty'  => $difficulty,
            'due_date'    => $due,
        ], $errors];
    }

    /** Input filtering for search: anything invalid is ignored, never trusted */
    private function cleanFilters(array $get): array
    {
        $q = mb_substr(trim((string) ($get['q'] ?? '')), 0, 100);

        $difficulty = (string) ($get['difficulty'] ?? '');
        if (!array_key_exists($difficulty, Quest::XP)) {
            $difficulty = '';
        }

        $type = (string) ($get['type'] ?? '');
        if (!in_array($type, Quest::TYPES, true)) {
            $type = '';
        }

        $status = (string) ($get['status'] ?? '');
        if (!in_array($status, ['active', 'completed'], true)) {
            $status = '';
        }

        $categoryId = (int) ($get['category'] ?? 0);
        $validIds = array_map('intval', array_column($this->categories->all(), 'id'));
        if (!in_array($categoryId, $validIds, true)) {
            $categoryId = 0;
        }

        $sort = (string) ($get['sort'] ?? 'due');
        if (!array_key_exists($sort, Quest::SORTS)) {
            $sort = 'due';
        }

        return [
            'q'           => $q,
            'category_id' => $categoryId,
            'type'        => $type,
            'difficulty'  => $difficulty,
            'status'      => $status,
            'due_from'    => $this->validDate((string) ($get['due_from'] ?? '')),
            'due_to'      => $this->validDate((string) ($get['due_to'] ?? '')),
            'sort'        => $sort,
        ];
    }

    private function validDate(string $value): string
    {
        $value = trim($value);
        $dt = \DateTime::createFromFormat('Y-m-d', $value);
        return ($dt !== false && $dt->format('Y-m-d') === $value) ? $value : '';
    }

    /** Values to refill the form after a validation error */
    private function formValues(array $post): array
    {
        return [
            'title'       => trim((string) ($post['title'] ?? '')),
            'description' => trim((string) ($post['description'] ?? '')),
            'category_id' => (int) ($post['category_id'] ?? 0),
            'difficulty'  => (string) ($post['difficulty'] ?? 'easy'),
            'due_date'    => trim((string) ($post['due_date'] ?? '')),
        ];
    }

    private function renderForm(string $mode, array $form, array $errors, string $action): void
    {
        echo $this->twig->render('pages/quest_form.twig', [
            'mode'         => $mode,
            'form'         => $form,
            'errors'       => $errors,
            'action'       => $action,
            'categories'   => $this->categories->all(),
            'difficulties' => Quest::CUSTOM_XP,
        ]);
    }

    /** Adds a readable "time left" to active quests and open offers */
    private function decorate(array $quests): array
    {
        return array_map(function (array $q): array {
            $showTime = in_array($q['status'], ['active', 'offered'], true);
            return $q + ['time_left' => $showTime ? $this->timeLeft($q['expires_at'] ?? null) : null];
        }, $quests);
    }

    private function timeLeft(?string $expires): ?array
    {
        if ($expires === null) {
            return null;
        }
        $seconds = strtotime($expires) - time();
        if ($seconds <= 0) {
            return ['text' => 'Time is up', 'urgent' => true];
        }
        $hours = (int) floor($seconds / 3600);
        if ($hours < 1) {
            return ['text' => 'Less than an hour left', 'urgent' => true];
        }
        if ($hours < 48) {
            return ['text' => $hours . ' hour' . ($hours === 1 ? '' : 's') . ' left', 'urgent' => $hours < 6];
        }
        $days = (int) floor($hours / 24);
        return ['text' => $days . ' days left', 'urgent' => false];
    }

    private function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
    }

    private function now(): string
    {
        return (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
    }

    private function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::flash('error', 'Invalid request.');
            $this->redirect('quests');
        }
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo $this->twig->render('pages/404.twig');
    }

    private function redirect(string $page): never
    {
        header('Location: index.php?page=' . $page);
        exit;
    }
}