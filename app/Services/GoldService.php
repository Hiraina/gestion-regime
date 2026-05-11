<?php

namespace App\Services;

use App\Models\UsersModel;
use App\Models\WalletsModel;
use App\Models\TransactionsModel;
use App\Models\TransactionTypesModel;

class GoldService
{
    private const DISCOUNT_RATE = 0.15;
    private const UPGRADE_PRICE = 100.0;

    private UsersModel $usersModel;
    private WalletsModel $walletModel;
    private TransactionsModel $transactionModel;
    private TransactionTypesModel $transactionTypeModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->walletModel = new WalletsModel();
        $this->transactionModel = new TransactionsModel();
        $this->transactionTypeModel = new TransactionTypesModel();
    }

    public function isGold(int $userId): bool
    {
        $user = $this->usersModel->find($userId);
        return !empty($user) && (int) ($user['is_gold'] ?? 0) === 1;
    }

    public function activateGold(int $userId): void
    {
        $this->updateGoldStatus($userId, 1);
    }

    public function deactivateGold(int $userId): void
    {
        $this->updateGoldStatus($userId, 0);
    }

    public function getDiscountRate(): float
    {
        return self::DISCOUNT_RATE;
    }

    public function getUpgradePrice(): float
    {
        return self::UPGRADE_PRICE;
    }

    public function purchaseGold(int $userId): array
    {
        if ($this->isGold($userId)) {
            throw new \RuntimeException('Vous etes deja membre Gold.');
        }

        $price = self::UPGRADE_PRICE;

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $wallet = $this->walletModel->where('user_id', $userId)->first();
            if (!$wallet) {
                throw new \RuntimeException('Portefeuille introuvable.');
            }

            $balance = (float) ($wallet['balance'] ?? 0);
            if ($balance < $price) {
                throw new \RuntimeException('Solde insuffisant pour passer Gold.');
            }

            $newBalance = $balance - $price;
            $updated = $this->walletModel->update($wallet['id'], [
                'balance' => $newBalance,
            ]);

            if (!$updated) {
                throw new \RuntimeException('Mise a jour du portefeuille impossible.');
            }

            $typeRow = $this->transactionTypeModel->getTypeId('debit');
            $typeId = is_array($typeRow) ? (int) ($typeRow['id'] ?? 0) : (int) $typeRow;
            if ($typeId <= 0) {
                throw new \RuntimeException('Type de transaction debit introuvable.');
            }

            $this->transactionModel->insert([
                'wallet_id' => $wallet['id'],
                'amount' => $price,
                'transaction_type_id' => $typeId,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $this->updateGoldStatus($userId, 1);

            $db->transCommit();

            return [
                'price' => $price,
                'new_balance' => $newBalance,
            ];
        } catch (\Throwable $th) {
            $db->transRollback();
            throw $th;
        }
    }

    private function updateGoldStatus(int $userId, int $status): void
    {
        $updated = $this->usersModel->update($userId, [
            'is_gold' => $status,
        ]);

        if (!$updated) {
            throw new \RuntimeException('Impossible de mettre a jour le statut Gold.');
        }
    }
}
