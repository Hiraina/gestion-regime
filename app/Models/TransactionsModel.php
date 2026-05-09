<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionsModel extends Model
{
    protected $table = 'transactions';

    protected $allowedFields = [
        'wallet_id',
        'amount',
        'transaction_type_id',
        'created_at'
    ];

    public function getTransactionById($id){
        return $this->where('id', $id)
                    ->first();
    }

    public function getTransactionsByWalletId($walletId, $perPage = 10){
        return $this->where('wallet_id', $walletId)
                    ->orderBy('created_at', 'DESC')
                    ->paginate($perPage);
    }

    public function getLatestTransactionsByType($typeId, $perPage = 10){
        return $this->where('transaction_type_id', $typeId)
                    ->orderBy('created_at', 'DESC')
                    ->paginate($perPage);
    }

    public function createTransaction($transactionData){
        /**
         * $transactionData doit contenir wallet_id, amount, transaction_type_id
         */

         $this->insert([
            'wallet_id' => $transactionData['wallet_id'],
            'amount' => $transactionData['amount'],
            'transaction_type_id' => $transactionData['transaction_type_id'],
            'created_at' => date_create()
         ]);

    }

}