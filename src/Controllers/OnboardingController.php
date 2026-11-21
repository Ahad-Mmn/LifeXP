<?php
declare(strict_types=1);

namespace LifeXP\Controllers;

use LifeXP\Core\Csrf;
use LifeXP\Core\Session;
use LifeXP\Models\Category;
use LifeXP\Models\Interest;
use PDO;
use Twig\Environment;

final class OnboardingController
{
    private const MIN_CATEGORIES = 3;

    private Interest $interests;
    private Category $categories;

    public function __construct(private Environment $twig, PDO $pdo)
    {
        $this->interests  = new Interest($pdo);
        $this->categories = new Category($pdo);
    }

    /** Choose categories */
    public function categories(): void
    {
        Session::requireLogin();
        $this->skipIfDone();

        $userId = (int) Session::userId();
        $all    = $this->categories->all();
        $errors = [];
        $chosen = $this->interests->categoryIds($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $valid  = array_map('intval', array_column($all, 'id'));
            $posted = array_map('intval', (array) ($_POST['categories'] ?? []));
            $chosen = array_values(array_unique(array_intersect($posted, $valid)));

            if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
                $errors[] = 'Your session expired. Please try again.';
            } elseif (count($chosen) < self::MIN_CATEGORIES) {
                $errors[] = 'Please choose at least ' . self::MIN_CATEGORIES . ' categories.';
            }

            if (!$errors) {
                $this->interests->setCategories($userId, $chosen);
                $this->redirect('onboarding_samples');
            }
        }

        echo $this->twig->render('pages/onboarding.twig', [
            'categories' => $all,
            'chosen'     => $chosen,
            'errors'     => $errors,
            'min'        => self::MIN_CATEGORIES,
        ]);
    }

    /** Tick sample quests, then finish */
    public function samples(): void
    {
        Session::requireLogin();
        $this->skipIfDone();

        $userId    = (int) Session::userId();
        $chosenIds = $this->interests->categoryIds($userId);
        if (count($chosenIds) < self::MIN_CATEGORIES) {
            $this->redirect('onboarding');
        }

        $groups  = $this->interests->sampleGroups($chosenIds);
        $offered = [];
        foreach ($groups as $group) {
            foreach ($group['samples'] as $sample) {
                $offered[] = (int) $sample['id'];
            }
        }

        $errors = [];
        $picked = $this->interests->templateIds($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $posted = array_map('intval', (array) ($_POST['templates'] ?? []));
            $picked = array_values(array_unique(array_intersect($posted, $offered)));

            if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
                $errors[] = 'Your session expired. Please try again.';
            }

            if (!$errors) {
                $this->interests->setTemplates($userId, $picked);
                $this->interests->markOnboarded($userId);
                Session::markOnboarded();
                Session::flash('success', 'Your interests are saved. Welcome to LifeXP!');
                $this->redirect('dashboard');
            }
        }

        echo $this->twig->render('pages/onboarding_samples.twig', [
            'groups' => $groups,
            'picked' => $picked,
            'errors' => $errors,
        ]);
    }

    private function skipIfDone(): void
    {
        if (Session::isOnboarded()) {
            $this->redirect('dashboard');
        }
    }

    private function redirect(string $page): never
    {
        header('Location: index.php?page=' . $page);
        exit;
    }
}