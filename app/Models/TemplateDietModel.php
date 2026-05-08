<?php

namespace App\Models;

use CodeIgniter\Model;

class TemplateDietModel extends Model
{
    protected $table = 'template_diets';
    protected $primaryKey = 'template_id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = [
        'template_id',
        'diet_id',
    ];
}
