<?php

namespace App\Models;

use CodeIgniter\Model;

class DietDurationPricingModel extends Model
{
    protected $table = 'diet_duration_pricings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'diet_id',
        'duration_days',
        'price',
    ];
}
