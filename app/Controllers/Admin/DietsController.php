<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DietCompositionModel;
use App\Models\DietDurationPricingModel;
use App\Models\DietModel;
use App\Models\FoodCategoryModel;
use CodeIgniter\HTTP\RedirectResponse;

class DietsController extends BaseController
{
    private DietModel $dietModel;
    private DietDurationPricingModel $pricingModel;
    private DietCompositionModel $compositionModel;
    private FoodCategoryModel $categoryModel;

    private array $defaultCategories = ['viande', 'poisson', 'volaille'];

    public function __construct()
    {
        $this->dietModel = new DietModel();
        $this->pricingModel = new DietDurationPricingModel();
        $this->compositionModel = new DietCompositionModel();
        $this->categoryModel = new FoodCategoryModel();
    }

    public function index(): string
    {
        helper('url');

        return view('admin/diets/index', [
            'diets' => $this->dietModel->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function create(): string
    {
        helper(['url', 'form']);

        $categories = $this->ensureDefaultCategories();

        return view('admin/diets/form', [
            'title' => 'Nouveau regime',
            'action' => base_url('admin/diets'),
            'diet' => [
                'name' => '',
                'description' => '',
            ],
            'pricingRows' => $this->buildPricingRows(),
            'compositionRows' => $this->buildCompositionRows($categories),
        ]);
    }

    public function store(): RedirectResponse
    {
        helper('url');

        $payload = $this->collectPayload();
        $errors = $this->validatePayload($payload);

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $dietId = $this->dietModel->insert([
            'name' => $payload['name'],
            'description' => $payload['description'],
        ], true);

        $this->savePricing($dietId, $payload['duration_days'], $payload['prices']);
        $this->saveComposition($dietId, $payload['categories']);

        return redirect()->to(base_url('admin/diets'))->with('success', 'Regime cree.');
    }

    public function edit(int $id): string
    {
        helper(['url', 'form']);

        $diet = $this->dietModel->find($id);

        if (! $diet) {
            return view('admin/diets/index', [
                'diets' => $this->dietModel->orderBy('id', 'DESC')->findAll(),
                'errors' => ['Regime introuvable.'],
            ]);
        }

        $categories = $this->ensureDefaultCategories();
        $pricingRows = $this->pricingModel->where('diet_id', $id)->findAll();
        $compositionRows = $this->compositionModel->where('diet_id', $id)->findAll();

        return view('admin/diets/form', [
            'title' => 'Modifier un regime',
            'action' => base_url('admin/diets/' . $id),
            'diet' => $diet,
            'pricingRows' => $this->buildPricingRows($pricingRows),
            'compositionRows' => $this->buildCompositionRows($categories, $compositionRows),
            'isEdit' => true,
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        helper('url');

        $diet = $this->dietModel->find($id);

        if (! $diet) {
            return redirect()->to(base_url('admin/diets'))->with('errors', ['Regime introuvable.']);
        }

        $payload = $this->collectPayload();
        $errors = $this->validatePayload($payload);

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $this->dietModel->update($id, [
            'name' => $payload['name'],
            'description' => $payload['description'],
        ]);

        $this->pricingModel->where('diet_id', $id)->delete();
        $this->compositionModel->where('diet_id', $id)->delete();

        $this->savePricing($id, $payload['duration_days'], $payload['prices']);
        $this->saveComposition($id, $payload['categories']);

        return redirect()->to(base_url('admin/diets'))->with('success', 'Regime mis a jour.');
    }

    public function delete(int $id): RedirectResponse
    {
        helper('url');

        $this->pricingModel->where('diet_id', $id)->delete();
        $this->compositionModel->where('diet_id', $id)->delete();
        $this->dietModel->delete($id);

        return redirect()->to(base_url('admin/diets'))->with('success', 'Regime supprime.');
    }

    private function collectPayload(): array
    {
        $durationDays = $this->request->getPost('duration_days');
        $prices = $this->request->getPost('price');
        $categoryNames = $this->request->getPost('category_name');
        $categoryPercentages = $this->request->getPost('category_percentage');

        return [
            'name' => trim((string) $this->request->getPost('name')),
            'description' => trim((string) $this->request->getPost('description')),
            'duration_days' => is_array($durationDays) ? $durationDays : [],
            'prices' => is_array($prices) ? $prices : [],
            'categories' => $this->buildCategoryPayload(
                is_array($categoryNames) ? $categoryNames : [],
                is_array($categoryPercentages) ? $categoryPercentages : []
            ),
        ];
    }

    private function validatePayload(array $payload): array
    {
        $errors = [];

        if ($payload['name'] === '') {
            $errors[] = 'Le nom du regime est obligatoire.';
        }

        $durationErrors = $this->validatePricing($payload['duration_days'], $payload['prices']);
        $categoryErrors = $this->validateCategories($payload['categories']);

        return array_merge($errors, $durationErrors, $categoryErrors);
    }

    private function validatePricing(array $durations, array $prices): array
    {
        $errors = [];
        $validRows = 0;

        foreach ($durations as $index => $duration) {
            $duration = (int) $duration;
            $price = isset($prices[$index]) ? (float) $prices[$index] : 0;

            if ($duration <= 0 && $price <= 0) {
                continue;
            }

            if ($duration <= 0) {
                $errors[] = 'Chaque duree doit etre superieure a 0.';
                break;
            }

            if ($price <= 0) {
                $errors[] = 'Chaque prix doit etre superieur a 0.';
                break;
            }

            $validRows++;
        }

        if ($validRows === 0) {
            $errors[] = 'Ajoutez au moins une duree avec son prix.';
        }

        return $errors;
    }

    private function validateCategories(array $categories): array
    {
        $errors = [];
        $sum = 0.0;

        foreach ($categories as $category) {
            $sum += (float) $category['percentage'];
        }

        if (abs($sum - 100) > 0.01) {
            $errors[] = 'La somme des pourcentages doit faire 100%.';
        }

        return $errors;
    }

    private function ensureDefaultCategories(): array
    {
        $categories = [];

        foreach ($this->defaultCategories as $name) {
            $existing = $this->categoryModel->where('name', $name)->first();

            if (! $existing) {
                $id = $this->categoryModel->insert(['name' => $name], true);
                $categories[] = ['id' => $id, 'name' => $name];
                continue;
            }

            $categories[] = $existing;
        }

        return $categories;
    }

    private function buildPricingRows(?array $existingRows = null): array
    {
        $oldDurations = old('duration_days');
        $oldPrices = old('price');

        if (is_array($oldDurations) && is_array($oldPrices)) {
            $rows = [];
            foreach ($oldDurations as $index => $duration) {
                $rows[] = [
                    'duration_days' => $duration,
                    'price' => $oldPrices[$index] ?? '',
                ];
            }

            return $rows !== [] ? $rows : [['duration_days' => '', 'price' => '']];
        }

        if (is_array($existingRows) && $existingRows !== []) {
            return array_map(static function (array $row): array {
                return [
                    'duration_days' => $row['duration_days'] ?? '',
                    'price' => $row['price'] ?? '',
                ];
            }, $existingRows);
        }

        return [['duration_days' => '', 'price' => '']];
    }

    private function buildCompositionRows(array $categories, ?array $existingRows = null): array
    {
        $oldNames = old('category_name');
        $oldPercentages = old('category_percentage');

        if (is_array($oldNames) && is_array($oldPercentages)) {
            $rows = [];
            foreach ($oldNames as $index => $name) {
                $rows[] = [
                    'name' => $name,
                    'percentage' => $oldPercentages[$index] ?? '',
                ];
            }

            return $rows;
        }

        $percentByCategory = [];

        if (is_array($existingRows)) {
            foreach ($existingRows as $row) {
                $percentByCategory[(int) $row['category_id']] = (float) $row['percentage'] * 100;
            }
        }

        return array_map(static function (array $category) use ($percentByCategory): array {
            $id = (int) ($category['id'] ?? 0);
            return [
                'name' => $category['name'] ?? '',
                'percentage' => $percentByCategory[$id] ?? 0,
            ];
        }, $categories);
    }

    private function buildCategoryPayload(array $names, array $percentages): array
    {
        $payload = [];

        foreach ($names as $index => $name) {
            $name = trim((string) $name);
            $percentage = isset($percentages[$index]) ? (float) $percentages[$index] : 0;

            if ($name === '') {
                continue;
            }

            $payload[] = [
                'name' => $name,
                'percentage' => $percentage,
            ];
        }

        return $payload;
    }

    private function savePricing(int $dietId, array $durations, array $prices): void
    {
        foreach ($durations as $index => $duration) {
            $duration = (int) $duration;
            $price = isset($prices[$index]) ? (float) $prices[$index] : 0;

            if ($duration <= 0 || $price <= 0) {
                continue;
            }

            $this->pricingModel->insert([
                'diet_id' => $dietId,
                'duration_days' => $duration,
                'price' => $price,
            ]);
        }
    }

    private function saveComposition(int $dietId, array $categories): void
    {
        foreach ($categories as $category) {
            $name = $category['name'];
            $percentage = (float) $category['percentage'];

            $existing = $this->categoryModel->where('name', $name)->first();

            if (! $existing) {
                $categoryId = $this->categoryModel->insert(['name' => $name], true);
            } else {
                $categoryId = $existing['id'];
            }

            $this->compositionModel->insert([
                'diet_id' => $dietId,
                'category_id' => $categoryId,
                'percentage' => $percentage / 100,
            ]);
        }
    }
}
