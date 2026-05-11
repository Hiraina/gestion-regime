<?php

namespace App\Models;

use CodeIgniter\Model;

class CodesModel extends Model
{
    protected $table            = 'codes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'code_value',
        'amount',
        'used_by_user_id',
        'date_of_use',
    ];

    protected $useTimestamps = false;
    protected $validationRules = [
        'code_value' => 'required|max_length[50]',
        'amount'     => 'required|decimal',
    ];

    public function getCodeUnusedValue(){
        $this->select('code_value, amount')
            ->where('used_by_user_id', null)
            ->first();
    }
}