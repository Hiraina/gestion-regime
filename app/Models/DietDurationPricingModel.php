<?php

namespace App\Models;

use CodeIgniter\Model;

class DietDurationPricingModel extends Model
{
    protected $table = 'diet_duration_pricing';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'diet_id',
        'price_per_day'
    ];
}