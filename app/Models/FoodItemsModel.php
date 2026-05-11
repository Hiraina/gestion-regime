<?php

namespace App\Models;

use CodeIgniter\Model;

class FoodItemsModel extends Model
{
    protected $table = 'food_items';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'category_id',
        'name',
        'calories_per_100g'
    ];

    public function getByIds(array $itemIds): array
    {
        $itemIds = array_values(array_unique(array_map('intval', $itemIds)));

        if (empty($itemIds)) {
            return [];
        }

        return $this->whereIn('id', $itemIds)->findAll();
    }

    public function calculateCaloriesForItems(array $itemIds, float $portionGrams = 100.0): float
    {
        $items = $this->getByIds($itemIds);
        $total = 0.0;

        foreach ($items as $item) {
            $caloriesPer100g = (float) ($item['calories_per_100g'] ?? 0);
            $total += $caloriesPer100g * ($portionGrams / 100.0);
        }

        return $total;
    }
}