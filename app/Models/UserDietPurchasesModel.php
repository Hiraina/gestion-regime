<?php

namespace App\Models;

use CodeIgniter\Model;

class UserDietPurchasesModel extends Model
{
    protected $table = 'user_diet_purchases';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'diet_id',
        'duration_days',
        'price_paid',
        'discount_applied'
    ];
}