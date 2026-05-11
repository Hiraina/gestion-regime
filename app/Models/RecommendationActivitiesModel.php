<?php

namespace App\Models;

use CodeIgniter\Model;

class RecommendationActivitiesModel extends Model
{
    protected $table = 'recommendation_activities';

    protected $primaryKey = 'recommendation_id';

    protected $useAutoIncrement = false;

    protected $allowedFields = [
        'recommendation_id',
        'activity_id',
        'frequency_per_week',
        'duration_minutes'
    ];
}