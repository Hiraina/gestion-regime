<?php

namespace App\Services;

use App\Models\DietDurationPricingModel;
use App\Models\UserDietPurchasesModel;
use App\Models\WalletsModel;
use App\Models\TransactionsModel;
use App\Models\TransactionTypesModel;

class DietPurchaseService
{
    private DietDurationPricingModel $pricingModel;
    private UserDietPurchasesModel $purchasesModel;
    private WalletsModel $walletModel;
    private TransactionsModel $transactionModel;
    private TransactionTypesModel $transactionTypeModel;
    private GoldService $goldService;

    public function __construct()
    {
        $this->pricingModel = new DietDurationPricingModel();
        $this->purchasesModel = new UserDietPurchasesModel();
        $this->walletModel = new WalletsModel();
        $this->transactionModel = new TransactionsModel();
        $this->transactionTypeModel = new TransactionTypesModel();
        $this->goldService = new GoldService();
    }

    public function purchase(int $userId, int $dietId, int $durationDays): array
    {
        if ($dietId <= 0 || $durationDays <= 0) {
            throw new \InvalidArgumentException('Parametres de paiement invalides.');
        }

        $pricing = $this->pricingModel
            ->where('diet_id', $dietId)
            ->where('duration_days', $durationDays)
            ->first();

        if (!$pricing) {
            throw new \RuntimeException('Tarif introuvable pour ce regime.');
        }

        $price = (float) ($pricing['price'] ?? 0);
        if ($price <= 0) {
            throw new \RuntimeException('Prix invalide pour ce regime.');
        }

        $isGold = $this->goldService->isGold($userId);
        $discount = $isGold ? round($price * $this->goldService->getDiscountRate(), 4) : 0.0;
        $pricePaid = max(0.0, round($price - $discount, 4));

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $wallet = $this->walletModel->where('user_id', $userId)->first();
            if (!$wallet) {
                throw new \RuntimeException('Portefeuille introuvable.');
            }

            $balance = (float) ($wallet['balance'] ?? 0);
            if ($balance < $pricePaid) {
                throw new \RuntimeException('Solde insuffisant.');
            }

            $newBalance = $balance - $pricePaid;
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
                'amount' => $pricePaid,
                'transaction_type_id' => $typeId,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $this->purchasesModel->insert([
                'user_id' => $userId,
                'diet_id' => $dietId,
                'duration_days' => $durationDays,
                'price_paid' => $pricePaid,
                'discount_applied' => $discount,
            ]);

            $db->transCommit();

            return [
                'price' => $price,
                'discount' => $discount,
                'price_paid' => $pricePaid,
                'new_balance' => $newBalance,
                'is_gold' => $isGold,
            ];
        } catch (\Throwable $th) {
            $db->transRollback();
            throw $th;
        }
    }
}
