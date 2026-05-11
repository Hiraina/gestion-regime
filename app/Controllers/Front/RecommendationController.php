<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Models\ActivitiesModel;
use App\Models\FoodCategoriesModel;
use App\Models\FoodItemsModel;
use App\Models\UsersModel;

class RecommendationController extends BaseController
{
    private const SESSION_KEY = 'recommendation_wizard';
    private const CANDIDATES_SESSION_KEY = 'recommendation_candidates';

    public function index()
    {
        return redirect()->to(base_url('recommendations/step1'));
    }

    public function step1()
    {
        $categoryModel = new FoodCategoriesModel();

        return view('recommendations/step1', [
            'categories' => $categoryModel->orderBy('name', 'ASC')->findAll(),
            'draft' => $this->getDraft(),
            'error' => session()->getFlashdata('error')
        ]);
    }

    public function saveStep1()
    {
        $categories = (new FoodCategoriesModel())->orderBy('name', 'ASC')->findAll();
        $categoryIds = array_column($categories, 'id');
        $postedDistributions = $this->request->getPost('distributions') ?? [];

        $cleanDistributions = [];
        $total = 0.0;

        foreach ($categoryIds as $categoryId) {
            $value = $postedDistributions[$categoryId] ?? 0;
            $value = is_numeric($value) ? (float) $value : 0.0;

            if ($value < 0 || $value > 100) {
                return $this->renderStep1WithError($categories, $postedDistributions, 'Chaque pourcentage doit être compris entre 0 et 100.');
            }

            if ($value > 0) {
                $cleanDistributions[$categoryId] = $value;
                $total += $value;
            }
        }

        if ($total <= 0) {
            return $this->renderStep1WithError($categories, $postedDistributions, 'Choisissez au moins une catégorie de nourriture.');
        }

        if ($total != 100) {
            return $this->renderStep1WithError($categories, $postedDistributions, 'Le total des distributions doit être exactement 100 %.');
        }

        $draft = $this->getDraft();
        $draft['distributions'] = $cleanDistributions;
        unset($draft['items'], $draft['activities']);
        $this->saveDraft($draft);

        return redirect()->to(base_url('recommendations/step2'));
    }

    public function step2()
    {
        $draft = $this->getDraft();
        if (empty($draft['distributions'])) {
            return redirect()->to(base_url('recommendations/step1'));
        }

        $categories = (new FoodCategoriesModel())->orderBy('name', 'ASC')->findAll();
        $items = (new FoodItemsModel())->orderBy('name', 'ASC')->findAll();

        [$activeCategories, $itemsByCategory] = $this->buildActiveCategoriesAndItems($categories, $items, $draft['distributions']);

        return view('recommendations/step2', [
            'activeCategories' => $activeCategories,
            'itemsByCategory' => $itemsByCategory,
            'draft' => $draft,
            'error' => session()->getFlashdata('error')
        ]);
    }

    public function saveStep2()
    {
        $draft = $this->getDraft();
        if (empty($draft['distributions'])) {
            return redirect()->to(base_url('recommendations/step1'));
        }

        $categories = (new FoodCategoriesModel())->orderBy('name', 'ASC')->findAll();
        $items = (new FoodItemsModel())->orderBy('name', 'ASC')->findAll();
        [$activeCategories, $itemsByCategory] = $this->buildActiveCategoriesAndItems($categories, $items, $draft['distributions']);

        $postedItems = $this->request->getPost('items') ?? [];
        $itemLookup = [];

        foreach ($items as $item) {
            $itemLookup[(int) $item['id']] = $item;
        }

        $selectedItems = [];

        foreach ($activeCategories as $category) {
            $categoryId = (int) $category['id'];
            $selection = $postedItems[$categoryId] ?? [];
            $selection = array_values(array_filter(array_map('intval', (array) $selection)));

            if (empty($selection)) {
                return $this->renderStep2WithError($activeCategories, $itemsByCategory, $draft, 'Sélectionnez au moins un aliment préféré pour chaque catégorie activée.');
            }

            foreach ($selection as $itemId) {
                if (!isset($itemLookup[$itemId]) || (int) $itemLookup[$itemId]['category_id'] !== $categoryId) {
                    return $this->renderStep2WithError($activeCategories, $itemsByCategory, $draft, 'Un aliment sélectionné ne correspond pas à sa catégorie.');
                }
            }

            $selectedItems[$categoryId] = $selection;
        }

        $draft['items'] = $selectedItems;
        unset($draft['activities'], $draft['activity_durations_hours']);
        $this->saveDraft($draft);

        return redirect()->to(base_url('recommendations/step3'));
    }

    public function step3()
    {
        $draft = $this->getDraft();
        if (empty($draft['items'])) {
            return redirect()->to(base_url('recommendations/step2'));
        }

        $activityModel = new ActivitiesModel();

        return view('recommendations/step3', [
            'activities' => $activityModel->orderBy('name', 'ASC')->findAll(),
            'draft' => $draft,
            'error' => session()->getFlashdata('error')
        ]);
    }

    public function saveStep3()
    {
        $draft = $this->getDraft();
        if (empty($draft['items'])) {
            return redirect()->to(base_url('recommendations/step2'));
        }

        $activities = (new ActivitiesModel())->orderBy('name', 'ASC')->findAll();
        $activityLookup = [];

        foreach ($activities as $activity) {
            $activityLookup[(int) $activity['id']] = $activity;
        }

        $selectedActivities = array_values(array_filter(array_map('intval', (array) ($this->request->getPost('activities') ?? []))));

        if (empty($selectedActivities)) {
            return $this->renderStep3WithError($activities, $draft, 'Choisissez au moins une activité préférée.');
        }

        foreach ($selectedActivities as $activityId) {
            if (!isset($activityLookup[$activityId])) {
                return $this->renderStep3WithError($activities, $draft, 'Une activité sélectionnée est invalide.');
            }
        }

        $draft['activities'] = $selectedActivities;
        unset($draft['activity_durations_hours']);
        $this->saveDraft($draft);

        return redirect()->to(base_url('recommendations/step4'));
    }

    public function step4()
    {
        $draft = $this->getDraft();

        if (empty($draft['activities'])) {
            return redirect()->to(base_url('recommendations/step3'));
        }

        $categoryModel = new FoodCategoriesModel();
        $itemModel = new FoodItemsModel();
        $activityModel = new ActivitiesModel();

        $categories = [];
        foreach ($categoryModel->findAll() as $category) {
            $categories[(int) $category['id']] = $category['name'];
        }

        $items = [];
        foreach ($itemModel->findAll() as $item) {
            $items[(int) $item['id']] = $item;
        }

        $activities = [];
        foreach ($activityModel->findAll() as $activity) {
            $activities[(int) $activity['id']] = $activity['name'];
        }

        $summaryDistributions = [];
        foreach ($draft['distributions'] ?? [] as $categoryId => $percentage) {
            $summaryDistributions[] = [
                'name' => $categories[(int) $categoryId] ?? 'Catégorie inconnue',
                'percentage' => $percentage
            ];
        }

        $summaryItems = [];
        foreach ($draft['items'] ?? [] as $categoryId => $itemIds) {
            $itemLabels = [];
            foreach ($itemIds as $itemId) {
                $itemLabels[] = $items[(int) $itemId]['name'] ?? 'Aliment inconnu';
            }

            $summaryItems[] = [
                'category' => $categories[(int) $categoryId] ?? 'Catégorie inconnue',
                'items' => $itemLabels
            ];
        }

        $summaryActivities = [];
        foreach ($draft['activities'] ?? [] as $activityId) {
            $summaryActivities[] = $activities[(int) $activityId] ?? 'Activité inconnue';
        }

        return view('recommendations/step4', [
            'draft' => $draft,
            'summaryDistributions' => $summaryDistributions,
            'summaryItems' => $summaryItems,
            'summaryActivities' => $summaryActivities,
            'payload' => json_encode($draft, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'error' => session()->getFlashdata('error')
        ]);
    }

    public function submit()
    {
        $payload = $this->request->getPost('payload');
        $draft = json_decode((string) $payload, true);

        if (!is_array($draft) || empty($draft['distributions']) || empty($draft['items']) || empty($draft['activities'])) {
            return redirect()->to(base_url('recommendations/step1'))->with('error', 'Le récapitulatif est incomplet.');
        }

        // Get user ID
        $userId = $this->authService->getUserIdOrFail();

        // Check if user has birth_date filled
        $userModel = new UsersModel();
        $user = $userModel->find($userId);

        if (empty($user['birth_date'])) {
            session()->setFlashdata('error', 'Veuillez compléter votre profil en indiquant votre date de naissance.');
            return redirect()->to(base_url('profile/complete'));
        }

        try {
            $recommendationService = new \App\Services\RecommendationService();
            $result = $recommendationService->generateMultipleRecommendations($userId, $draft, 3);
        } catch (\Throwable $th) {
            return redirect()->to(base_url('recommendations/step4'))->with('error', $th->getMessage());
        }

        // Clear the draft
        session()->remove(self::SESSION_KEY);

        $recommendations = $result['recommendations'] ?? [];
        session()->set(self::CANDIDATES_SESSION_KEY, [
            'draft' => $draft,
            'recommendations' => $recommendations,
        ]);

        return redirect()->to(base_url('recommendations/candidates'));
    }

    public function candidates()
    {
        $userId = $this->authService->getUserIdOrFail();
        $payload = session()->get(self::CANDIDATES_SESSION_KEY) ?? [];
        $generated = $payload['recommendations'] ?? [];
        $draft = $payload['draft'] ?? [];

        $recommendationService = new \App\Services\RecommendationService();
        $candidates = $recommendationService->getCandidatesForUser($userId, $generated);

        if (empty($candidates)) {
            return redirect()->to(base_url('recommendations/step1'))->with('error', 'Aucune recommandation candidate disponible.');
        }

        $dietPreview = $this->buildDietPreviewFromDraft($draft);

        return view('recommendations/candidates', [
            'candidates' => $candidates,
            'dietPreview' => $dietPreview,
            'error' => session()->getFlashdata('error'),
        ]);
    }

    public function chooseCandidate(int $recommendationId)
    {
        $userId = $this->authService->getUserIdOrFail();
        $payload = session()->get(self::CANDIDATES_SESSION_KEY) ?? [];
        $generated = $payload['recommendations'] ?? [];
        $draft = $payload['draft'] ?? [];

        if (empty($generated)) {
            return redirect()->to(base_url('recommendations/candidates'))->with('error', 'Aucun lot de recommandations à sélectionner.');
        }

        $recommendationService = new \App\Services\RecommendationService();

        try {
            $recommendationService->chooseCandidate($userId, $recommendationId, $generated, $draft);
        } catch (\Throwable $th) {
            return redirect()->to(base_url('recommendations/candidates'))->with('error', $th->getMessage());
        }

        session()->remove(self::SESSION_KEY);
        session()->remove(self::CANDIDATES_SESSION_KEY);

        return redirect()->to(base_url('dashboard'))->with('success', 'Recommandation sélectionnée avec succès.');
    }

    /**
     * Clear the draft and redirect to dashboard
     */
    public function clear()
    {
        session()->remove(self::SESSION_KEY);
        session()->remove(self::CANDIDATES_SESSION_KEY);
        return redirect()->to(base_url('dashboard'));
    }

    private function getDraft(): array
    {
        return session()->get(self::SESSION_KEY) ?? [];
    }

    private function saveDraft(array $draft): void
    {
        session()->set(self::SESSION_KEY, $draft);
    }

    private function buildActiveCategoriesAndItems(array $categories, array $items, array $distributions): array
    {
        $activeCategories = [];
        $itemsByCategory = [];

        foreach ($items as $item) {
            $categoryId = (int) $item['category_id'];
            $itemsByCategory[$categoryId][] = $item;
        }

        foreach ($categories as $category) {
            $categoryId = (int) $category['id'];
            if ((float) ($distributions[$categoryId] ?? 0) > 0) {
                $activeCategories[] = $category;
            }
        }

        return [$activeCategories, $itemsByCategory];
    }

    private function renderStep1WithError(array $categories, array $postedDistributions, string $message)
    {
        return view('recommendations/step1', [
            'categories' => $categories,
            'draft' => ['distributions' => $postedDistributions],
            'error' => $message
        ]);
    }

    private function renderStep2WithError(array $activeCategories, array $itemsByCategory, array $draft, string $message)
    {
        return view('recommendations/step2', [
            'activeCategories' => $activeCategories,
            'itemsByCategory' => $itemsByCategory,
            'draft' => $draft,
            'error' => $message
        ]);
    }

    private function renderStep3WithError(array $activities, array $draft, string $message)
    {
        return view('recommendations/step3', [
            'activities' => $activities,
            'draft' => $draft,
            'error' => $message
        ]);
    }

    private function buildDietPreviewFromDraft(array $draft): array
    {
        if (empty($draft['distributions']) || empty($draft['items'])) {
            return [];
        }

        $categoryModel = new FoodCategoriesModel();
        $itemModel = new FoodItemsModel();

        $categories = [];
        foreach ($categoryModel->findAll() as $category) {
            $categories[(int) $category['id']] = $category['name'];
        }

        $items = [];
        foreach ($itemModel->findAll() as $item) {
            $items[(int) $item['id']] = $item;
        }

        $preview = [];
        foreach ($draft['items'] as $categoryId => $itemIds) {
            $preview[] = [
                'category' => $categories[(int) $categoryId] ?? 'Catégorie inconnue',
                'percentage' => $draft['distributions'][$categoryId] ?? 0,
                'items' => array_values(array_filter(array_map(static function ($itemId) use ($items) {
                    return isset($items[(int) $itemId]) ? $items[(int) $itemId]['name'] : null;
                }, (array) $itemIds))),
            ];
        }

        return $preview;
    }
}