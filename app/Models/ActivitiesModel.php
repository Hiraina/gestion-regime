<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivitiesModel extends Model
{
    protected $table = 'activities';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'description',
        'met_value'
    ];

    public function getByIds(array $activityIds): array
    {
        $activityIds = array_values(array_unique(array_map('intval', $activityIds)));

        if (empty($activityIds)) {
            return [];
        }

        return $this->whereIn('id', $activityIds)->findAll();
    }
}