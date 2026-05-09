<?php


namespace App\Models;

use CodeIgniter\Model;

class TransactionTypesModel extends Model{
    protected $table = "transaction_types";

    public function getTypeId($name){
        return $this->select('id')
                    ->where('name', $name)
                    ->first();
    }

    public function getName($id){
        return $this->select('name')
                    ->where('id', $id)
                    ->first();
    }
}