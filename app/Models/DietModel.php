<?php

namespace App\Models;

use CodeIgniter\Model;

class DietModel extends Model
{
    protected $table = 'diets';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'name',
        'description',
    ];
}
