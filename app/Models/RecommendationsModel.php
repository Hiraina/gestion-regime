<?php

namespace App\Models;

use CodeIgniter\Model;

class RecommendationsModel extends Model
{
    protected $table = 'recommendations';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'diet_id',
        'generated_at',
        'start_date',
        'end_date',
        'status',
        'trigger_measurement_id'
    ];
}