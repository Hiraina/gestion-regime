<?php

namespace App\Models;

use CodeIgniter\Model;

class UserDietPurchaseModel extends Model
{
    protected $table = 'user_diet_purchases';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'diet_id',
        'duration_days',
        'price_paid',
        'discount_applied',
    ];
}
