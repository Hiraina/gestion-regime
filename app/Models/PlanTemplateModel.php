<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanTemplateModel extends Model
{
    protected $table = 'plan_templates';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'goal_id',
        'imc_min',
        'imc_max',
        'duration',
    ];
}
