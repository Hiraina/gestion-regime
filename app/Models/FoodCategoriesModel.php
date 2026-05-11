<?php

namespace App\Models;

use CodeIgniter\Model;

class FoodCategoriesModel extends Model
{
    protected $table = 'food_categories';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name'
    ];
}