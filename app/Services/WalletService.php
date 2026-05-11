<?php

namespace App\Services;

use App\Models\TransactionsModel;
use App\Models\TransactionTypesModel;
use App\Models\WalletsModel;

class WalletService{

    private $walletModel;
    private $transactionModel;
    private $transactionTypeModel;
    

    public function __construct(){
        $this->walletModel = new WalletsModel();
        $this->transactionModel = new TransactionsModel();
        $this->transactionTypeModel = new TransactionTypesModel();
    }

    public function credit($userId, $amount){

        if($amount <= 0){
            throw new \Exception("Credit amount must be positive");
        }
        $db = \Config\Database::connect();    
        $db->transBegin();

        try {
            $wallet = $this->walletModel
                        ->where('user_id', $userId)
                        ->first();

            if(!$wallet){
                throw new \Exception("Wallet not found");
            }

            $newBalance = $wallet['balance'] + $amount;
            $walletBalanceUpdate = $this->walletModel->update($wallet['id'], 
                        [
                            'balance' => $newBalance
                        ]);

            if(!$walletBalanceUpdate){
                throw new \Exception("Wallet balance update failed");
            }

            $typeId = $this->transactionTypeModel->getTypeId('credit');
            $transaction = $this->transactionModel->insert([
                'wallet_id' => $wallet['id'],
                'amount' => $amount,
                'transaction_type_id' => $typeId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if(!$transaction){
                throw new \Exception("Transaction failed");
            }

            $db->transCommit();
        } catch (\Throwable $th) {   
            $db->transRollback();
            throw $th;
        }
            
    }

    public function debit($userId, $amount){
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $wallet = $this->walletModel
                ->where('user_id', $userId)
                ->first();

            if (!$wallet) {
                throw new \Exception("Wallet not found");
            }

            if ($wallet['balance'] < $amount) {
                throw new \Exception("Insufficient balance");
            }

            $newBalance = $wallet['balance'] - $amount;

            $walletBalanceUpdate = $this->walletModel->update($wallet['id'], [
                'balance' => $newBalance
            ]);

            if (!$walletBalanceUpdate) {
                throw new \Exception("Wallet update failed");
            }

            $typeId = $this->transactionTypeModel->getTypeId('debit');

            $transaction = $this->transactionModel->insert([
                'wallet_id' => $wallet['id'],
                'amount' => $amount,
                'transaction_type_id' => $typeId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if (!$transaction) {
                throw new \Exception("Transaction failed");
            }

            $db->transCommit();

        } catch (\Throwable $th) {
            $db->transRollback();
            throw $th;
        }
    }

    public function getBalance($userId){
        $wallet = $this->getWalletOrFail($userId);

        return $wallet['balance'];
    }

    public function getTransactions($userId, $perPage = 10){
        $wallet = $this->getWalletOrFail($userId);

        return $this->transactionModel->getTransactionsByWalletId($wallet['id'], $perPage);       
    }

    private function getWalletOrFail($userId){
        $wallet = $this->walletModel
            ->where('user_id', $userId)
            ->first();

        if (!$wallet) {
            throw new \Exception("Wallet not found");
        }

        return $wallet;
    }
}