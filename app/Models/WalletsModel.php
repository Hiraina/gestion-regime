<?php

namespace App\Models;

use CodeIgniter\Model;

class WalletsModel extends Model
{
    protected $table = 'wallets';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'balance'
    ];

    public function getWalletById($id){
        return $this->where('id', $id)
                    ->first();
    }

    public function getWalletByUserId($userId){
        return $this->where('user_id', $userId)
                    ->first();
    }

    public function updateBalance($userId, $amount){
        return $this->where('user_id', $userId)
                    ->set('balance', $amount)
                    ->update();
    }

    public function getBalance($userId){
        return $this->select('balance')
                    ->where('user_id', $userId)
                    ->first();
    }
}