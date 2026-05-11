<?php

namespace App\Models;

use CodeIgniter\Model;

class DietCompositionsModel extends Model
{
    protected $table = 'diet_compositions';

    protected $primaryKey = 'diet_id';

    protected $useAutoIncrement = false;

    protected $allowedFields = [
        'diet_id',
        'food_item_id',
        'quantity'
    ];
}