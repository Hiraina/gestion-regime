<?php

namespace App\Controllers;

class TestController extends BaseController
{
    public function index(): string
    {
        return view('testing_view');
    }

    public function createRecommendation()
    {
        // Ensure a user exists in session
        $userModel = new \App\Models\UsersModel();
        $categoryModel = new \App\Models\FoodCategoriesModel();
        $itemModel = new \App\Models\FoodItemsModel();
        $activityModel = new \App\Models\ActivitiesModel();

        // find or create user
        $user = $userModel->first();
        if (!$user) {
            $userId = $userModel->insert([
                'name' => 'Test User',
                'email' => 'test+' . time() . '@example.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'gender_id' => null,
                'is_gold' => 0
            ]);
            $user = $userModel->find($userId);
        }

        // set session user id for auth
        session()->set('user_id', $user['id']);
        session()->set('user_name', $user['name'] ?? $user['email']);

        // collect categories that have items
        $categories = $categoryModel->findAll();
        $activeCategories = [];
        foreach ($categories as $cat) {
            $items = $itemModel->where('category_id', $cat['id'])->findAll();
            if (!empty($items)) {
                $activeCategories[] = ['category' => $cat, 'items' => $items];
            }
        }

        if (empty($activeCategories)) {
            return $this->response->setJSON(['error' => 'No food categories with items available. Seed data first.']);
        }

        // build draft: use up to 2 categories
        $draft = ['distributions' => [], 'items' => [], 'activities' => []];
        $count = min(2, count($activeCategories));
        $percent = (int) floor(100 / $count);
        for ($i = 0; $i < $count; $i++) {
            $cat = $activeCategories[$i];
            $draft['distributions'][$cat['category']['id']] = $percent;
            // pick first item in category
            $draft['items'][$cat['category']['id']] = [(int) $cat['items'][0]['id']];
        }

        // activities: pick up to 2
        $activities = $activityModel->findAll();
        if (!empty($activities)) {
            $draft['activities'] = array_map(fn($a) => (int) $a['id'], array_slice($activities, 0, 2));
        }

        // call service
        $recService = new \App\Services\RecommendationService();
        try {
            $result = $recService->createRecommendation((int)$user['id'], $draft);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }

        return $this->response->setJSON(['result' => $result, 'draft' => $draft]);
    }
}
