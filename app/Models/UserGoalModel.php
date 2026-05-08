<?php

namespace App\Models;

use CodeIgniter\Model;

class UserGoalModel extends Model
{
    protected $table = 'user_goals';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'goal_id',
        'start_date',
    ];
}
