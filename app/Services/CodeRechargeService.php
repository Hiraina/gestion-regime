<?php

namespace App\Services;

use App\Models\CodesModel;
use App\Models\WalletsModel;
use App\Models\TransactionsModel;
use App\Models\TransactionTypesModel;

class CodeRechargeService
{
    private CodesModel $codesModel;
    private WalletsModel $walletModel;
    private TransactionsModel $transactionModel;
    private TransactionTypesModel $transactionTypeModel;

    public function __construct()
    {
        $this->codesModel = new CodesModel();
        $this->walletModel = new WalletsModel();
        $this->transactionModel = new TransactionsModel();
        $this->transactionTypeModel = new TransactionTypesModel();
    }

    public function redeem(int $userId, string $codeValue): array
    {
        $codeValue = trim($codeValue);
        if ($codeValue === '') {
            throw new \InvalidArgumentException('Veuillez entrer un code.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $code = $this->codesModel
                ->where('code_value', $codeValue)
                ->where('used_by_user_id', null)
                ->first();

            if (!$code) {
                throw new \RuntimeException('Code invalide ou deja utilise.');
            }

            $wallet = $this->walletModel->where('user_id', $userId)->first();
            if (!$wallet) {
                $walletId = $this->walletModel->insert([
                    'user_id' => $userId,
                    'balance' => 0,
                ], true);
                $wallet = $this->walletModel->find($walletId);
            }

            $amount = (float) ($code['amount'] ?? 0);
            if ($amount <= 0) {
                throw new \RuntimeException('Montant du code invalide.');
            }

            $newBalance = (float) ($wallet['balance'] ?? 0) + $amount;
            $updated = $this->walletModel->update($wallet['id'], [
                'balance' => $newBalance,
            ]);

            if (!$updated) {
                throw new \RuntimeException('Mise a jour du portefeuille impossible.');
            }

            $this->codesModel->update($code['id'], [
                'used_by_user_id' => $userId,
                'date_of_use' => date('Y-m-d H:i:s'),
            ]);

            $typeRow = $this->transactionTypeModel->getTypeId('credit');
            $typeId = is_array($typeRow) ? (int) ($typeRow['id'] ?? 0) : (int) $typeRow;
            if ($typeId <= 0) {
                throw new \RuntimeException('Type de transaction credit introuvable.');
            }

            $this->transactionModel->insert([
                'wallet_id' => $wallet['id'],
                'amount' => $amount,
                'transaction_type_id' => $typeId,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $db->transCommit();

            return [
                'amount' => $amount,
                'new_balance' => $newBalance,
            ];
        } catch (\Throwable $th) {
            $db->transRollback();
            throw $th;
        }
    }
}
