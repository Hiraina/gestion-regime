<?php

namespace App\Models;

use CodeIgniter\Model;

class RecommendationModel extends Model
{
    protected $table = 'recommendations';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'template_id',
        'generated_at',
        'start_date',
        'end_date',
        'status',
        'trigger_measurement_id',
    ];
}
