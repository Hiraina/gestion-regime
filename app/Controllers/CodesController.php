<?php

namespace App\Controllers;

use App\Models\CodesModel;
use App\Models\WalletsModel;
use App\Models\TransactionsModel;

class CodesController extends BaseController
{
    public function redeem()
    {
        $json = $this->request->getJSON();
        $codeValue = trim($json->code ?? '');

        if (empty($codeValue)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Veuillez entrer un code.'
            ]);
        }

        $codeModel = new CodesModel();
        $code = $codeModel->where('code_value', $codeValue)
                          ->where('used_by_user_id', null)
                          ->first();

        if (!$code) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Code invalide ou déjà utilisé.'
            ]);
        }

        $userId = session()->get('user_id');

        // Créditer le portefeuille
        $walletModel = new WalletsModel();
        $wallet = $walletModel->where('user_id', $userId)->first();
        if (!$wallet) {
            // Créer un wallet si inexistant
            $walletId = $walletModel->insert(['user_id' => $userId, 'balance' => 0], true);
            $wallet = $walletModel->find($walletId);
        }

        $newBalance = $wallet['balance'] + $code['amount'];
        $walletModel->update($wallet['id'], ['balance' => $newBalance]);

        // Marquer le code comme utilisé
        $codeModel->update($code['id'], [
            'used_by_user_id' => $userId,
            'date_of_use' => date('Y-m-d H:i:s')
        ]);

        // Enregistrer la transaction
        $transactionModel = new TransactionsModel();
        $transactionModel->insert([
            'wallet_id' => $wallet['id'],
            'amount' => $code['amount'],
            'transaction_type_id' => 1, // ID pour "Ajout code"
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Code validé ! +' . number_format($code['amount'], 2) . ' € ajoutés.',
            'new_balance' => number_format($newBalance, 2)
        ]);
    }
}