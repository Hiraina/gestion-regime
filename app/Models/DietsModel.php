<?php

namespace App\Models;

use CodeIgniter\Model;

class DietsModel extends Model
{
    protected $table = 'diets';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'description'
    ];
}