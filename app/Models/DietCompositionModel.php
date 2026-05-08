<?php

namespace App\Models;

use CodeIgniter\Model;

class DietCompositionModel extends Model
{
    protected $table = 'diet_compositions';
    protected $primaryKey = 'diet_id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = [
        'diet_id',
        'category_id',
        'percentage',
    ];
}
