<?php

namespace App\Services;

use App\Models\DietsModel;
use App\Models\DietDurationPricingModel;
use App\Models\FoodCategoriesModel;
use App\Models\FoodDistributionsModel;
use App\Models\DietCompositionsModel;

class DietCrudServiceTest
{
    private DietsModel $dietsModel;
    private DietDurationPricingModel $pricingModel;
    private FoodCategoriesModel $categoriesModel;
    private FoodDistributionsModel $distributionsModel;
    private DietCompositionsModel $compositionsModel;

    public function __construct()
    {
        $this->dietsModel = new DietsModel();
        $this->pricingModel = new DietDurationPricingModel();
        $this->categoriesModel = new FoodCategoriesModel();
        $this->distributionsModel = new FoodDistributionsModel();
        $this->compositionsModel = new DietCompositionsModel();
    }

    public function getFormData(): array
    {
        $categories = $this->categoriesModel->orderBy('name', 'ASC')->findAll();
        $diets = $this->dietsModel->orderBy('name', 'ASC')->findAll();

        $pricingRows = $this->pricingModel->findAll();
        $pricingByDiet = [];
        foreach ($pricingRows as $row) {
            $price = 0.0;
            if (array_key_exists('price_per_day', $row) && $row['price_per_day'] !== null) {
                $price = (float) $row['price_per_day'];
            } elseif (array_key_exists('price', $row) && $row['price'] !== null) {
                $price = (float) $row['price'];
            }
            $pricingByDiet[(int) $row['diet_id']] = $price;
        }

        $categoryNames = [];
        foreach ($categories as $category) {
            $categoryNames[(int) $category['id']] = $category['name'];
        }

        $distributionRows = $this->distributionsModel->findAll();
        $distributionsByDiet = [];
        foreach ($distributionRows as $row) {
            $dietId = (int) $row['diet_id'];
            $percentage = (float) ($row['percentage'] ?? 0);
            if ($percentage <= 0) {
                continue;
            }

            $distributionsByDiet[$dietId][] = [
                'category' => $categoryNames[(int) $row['category_id']] ?? 'Categorie',
                'percentage' => $percentage,
            ];
        }

        $dietSummaries = [];
        foreach ($diets as $diet) {
            $dietId = (int) $diet['id'];
            $dietSummaries[] = [
                'id' => $dietId,
                'name' => $diet['name'],
                'price_per_day' => $pricingByDiet[$dietId] ?? 0,
                'categories' => $distributionsByDiet[$dietId] ?? [],
            ];
        }

        return [
            'categories' => $categories,
            'diets' => $dietSummaries,
        ];
    }

    public function createDiet(string $name, float $pricePerDay, array $distributions): int
    {
        $name = trim($name);
        if ($name === '') {
            throw new \InvalidArgumentException('Le nom du regime est obligatoire.');
        }

        if ($pricePerDay <= 0) {
            throw new \InvalidArgumentException('Le prix par jour doit etre positif.');
        }

        $cleanDistributions = [];
        $total = 0.0;
        foreach ($distributions as $categoryId => $percentage) {
            $value = is_numeric($percentage) ? (float) $percentage : 0.0;
            if ($value < 0 || $value > 100) {
                throw new \InvalidArgumentException('Chaque pourcentage doit etre entre 0 et 100.');
            }
            if ($value > 0) {
                $cleanDistributions[(int) $categoryId] = $value;
                $total += $value;
            }
        }

        if ($total <= 0) {
            throw new \InvalidArgumentException('Ajoutez au moins une categorie avec un pourcentage non nul.');
        }

        if (abs($total - 100.0) > 0.001) {
            throw new \InvalidArgumentException('Le total des pourcentages doit etre 100.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $dietId = $this->dietsModel->insert([
                'name' => $name,
                'description' => null,
            ], true);

            if (!$dietId) {
                throw new \RuntimeException('Creation du regime impossible.');
            }

            $pricingInserted = $this->pricingModel->insert([
                'diet_id' => $dietId,
                'price_per_day' => $pricePerDay,
            ]);

            if (!$pricingInserted) {
                throw new \RuntimeException('Creation du prix par jour impossible.');
            }

            foreach ($cleanDistributions as $categoryId => $percentage) {
                $this->distributionsModel->insert([
                    'diet_id' => $dietId,
                    'category_id' => $categoryId,
                    'percentage' => $percentage,
                ]);
            }

            $db->transCommit();

            return (int) $dietId;
        } catch (\Throwable $th) {
            $db->transRollback();
            throw $th;
        }
    }

    public function deleteDiet(int $dietId): void
    {
        if ($dietId <= 0) {
            throw new \InvalidArgumentException('Regime invalide.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $this->distributionsModel->where('diet_id', $dietId)->delete();
            $this->compositionsModel->where('diet_id', $dietId)->delete();
            $this->pricingModel->where('diet_id', $dietId)->delete();
            $this->dietsModel->delete($dietId);

            $db->transCommit();
        } catch (\Throwable $th) {
            $db->transRollback();
            throw $th;
        }
    }
}