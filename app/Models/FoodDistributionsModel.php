<?php

namespace App\Models;

use CodeIgniter\Model;

class FoodDistributionsModel extends Model
{
    protected $table = 'food_distributions';

    protected $primaryKey = 'diet_id';

    protected $useAutoIncrement = false;

    protected $allowedFields = [
        'diet_id',
        'category_id',
        'percentage'
    ];
}