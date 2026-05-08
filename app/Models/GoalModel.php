<?php

namespace App\Models;

use CodeIgniter\Model;

class GoalModel extends Model
{
    protected $table = 'goals';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'name',
        'description',
    ];
}
