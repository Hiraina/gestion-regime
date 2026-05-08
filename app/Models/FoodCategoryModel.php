<?php

namespace App\Models;

use CodeIgniter\Model;

class FoodCategoryModel extends Model
{
    protected $table = 'food_categories';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'name',
    ];
}
