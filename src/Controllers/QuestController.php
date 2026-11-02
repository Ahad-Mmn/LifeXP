<?php
declare(strict_types=1);

namespace LifeXP\Controllers;

use LifeXP\Core\Csrf;
use LifeXP\Core\Session;
use LifeXP\Models\Category;
use LifeXP\Models\Quest;
use PDO;
use Twig\Environment;
use LifeXP\Services\CompletionService;

final class QuestController
{
    private Quest $quests;
    private Category $categories;

    public function __construct(private Environment $twig, private PDO $pdo)
    {
        $this->quests = new Quest($pdo);
        $this->categories = new Category($pdo);
    }

    /** READ: list my quests */
    public function index(): void
    {
        Session::requireLogin();
        $quests = $this->quests->allForUser((int) Session::userId());
        echo $this->twig->render('pages/quests.twig', ['quests' => $quests]);
    }

    /** CREATE */
    public function create(): void
    {
        Session::requireLogin();
        $errors = [];
        $form = ['title' => '', 'description' => '', 'category_id' => 0, 'difficulty' => 'easy', 'due_date' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$data, $errors] = $this->validate($_POST);
            $form = $this->formValues($_POST);

            if (!$errors) {
                $this->quests->create((int) Session::userId(), $data);
                Session::flash('success', 'Quest created!');
                $this->redirect('quests');
            }
        }

        $this->renderForm('create', $form, $errors, 'index.php?page=quest_create');
    }

    /** UPDATE */
    public function edit(): void
    {
        Session::requireLogin();
        $id = (int) ($_GET['id'] ?? 0);
        $quest = $this->quests->find($id, (int) Session::userId());
        if ($quest === null) {
            $this->notFound();
            return;
        }

        $errors = [];
        $form = [
            'title'       => $quest['title'],
            'description' => (string) $quest['description'],
            'category_id' => (int) $quest['category_id'],
            'difficulty'  => $quest['difficulty'],
            'due_date'    => (string) $quest['due_date'],
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$data, $errors] = $this->validate($_POST);
            $form = $this->formValues($_POST);

            if (!$errors) {
                $this->quests->update($id, (int) Session::userId(), $data);
                Session::flash('success', 'Quest updated!');
                $this->redirect('quests');
            }
        }

        $this->renderForm('edit', $form, $errors, 'index.php?page=quest_edit&id=' . $id);
    }

    /** DELETE (POST only) */
    public function delete(): void
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verify($_POST['csrf_token'] ?? null)) {
            Session::flash('error', 'Invalid request.');
            $this->redirect('quests');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($this->quests->find($id, (int) Session::userId()) === null) {
            Session::flash('error', 'Quest not found.');
            $this->redirect('quests');
        }

        $this->quests->delete($id, (int) Session::userId());
        Session::flash('success', 'Quest deleted.');
        $this->redirect('quests');
    }

    /** COMPLETE (POST only): awards XP, updates level and streak */
    public function complete(): void
{
    Session::requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::verify($_POST['csrf_token'] ?? null)) {
        Session::flash('error', 'Invalid request.');
        $this->redirect('quests');
    }

    $id = (int) ($_POST['id'] ?? 0);
    $result = (new CompletionService($this->pdo))->complete($id, (int) Session::userId());

    if ($result === null) {
        Session::flash('error', 'That quest was not found or is already completed.');
        $this->redirect('quests');
    }

    $msg = 'Quest complete! +' . $result['earned'] . ' XP';
    if ($result['bonus_pct'] > 0) {
        $msg .= ' (includes +' . $result['bonus_pct'] . '% streak bonus)';
    }
    Session::flash('success', $msg);

    if ($result['leveled_up']) {
        Session::flash('success', 'LEVEL UP! You are now level ' . $result['new_level']
            . ' - ' . $result['new_title'] . '!');
    }

    $this->redirect('quests');
}

    // ------------------------------------------------------------------

    /** Input filtering: returns [cleanData, errors] */
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

        if (!array_key_exists($difficulty, Quest::XP)) {
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
            'difficulties' => Quest::XP,
        ]);
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