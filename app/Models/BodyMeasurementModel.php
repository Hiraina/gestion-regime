<?php

namespace App\Models;

use CodeIgniter\Model;

class BodyMeasurementModel extends Model
{
    protected $table = 'body_measurements';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'height',
        'weight',
        'created_at',
    ];
}
