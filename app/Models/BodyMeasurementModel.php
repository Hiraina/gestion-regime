<?php

namespace App\Models;

use CodeIgniter\Model;

class BodyMeasurementModel extends Model
{
    protected $table = 'bodyMeasurement';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'height',
        'weight',
        'created_at'
    ];
}