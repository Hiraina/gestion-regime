<?php

namespace App\Models;

use CodeIgniter\Model;

class UserGoalsModel extends Model
{
    protected $table = 'user_goals';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'goal_id',
        'start_date',
        'min_kg',
        'max_kg'
    ];

    public function getLatestWithGoalByUserId(int $userId): ?array
    {
        return $this->select('user_goals.*, goals.name AS goal_name, goals.description AS goal_description')
            ->join('goals', 'goals.id = user_goals.goal_id', 'left')
            ->where('user_goals.user_id', $userId)
            ->orderBy('user_goals.start_date', 'DESC')
            ->orderBy('user_goals.id', 'DESC')
            ->first();
    }
}