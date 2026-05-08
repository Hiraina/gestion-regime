<?php

namespace App\Models;

use CodeIgniter\Model;

class TemplateActivityModel extends Model
{
    protected $table = 'template_activities';
    protected $primaryKey = 'template_id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = [
        'template_id',
        'activity_id',
    ];
}
