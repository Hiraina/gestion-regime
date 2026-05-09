<?php  

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Services\WalletService;

class WalletController extends BaseController
{
    private $walletService;

    public function __construct()
    {
        $this->walletService = new WalletService();
    }

    public function creditWallet(){        
        $userId = $this->authService->getUserIdOrFail();

        $amount = $this->request->getPost('amount');

        if($amount === null){
            return $this->response->setJSON([
                'response' => 'failure' 
            ]);
        }

        $this->walletService->credit($userId, $amount);

        return $this->response->setJSON([
            'response' => 'success' 
        ]);
    }

    public function debitWallet(){
        $userId = $this->authService->getUserIdOrFail();

        $amount = $this->request->getPost('amount');

        if($amount === null){
            return $this->response->setJSON([
                'response' => 'failure' 
            ]);
        }

        $this->walletService->debit($userId, $amount);

        return $this->response->setJSON([
            'response' => 'success' 
        ]);
    }

    public function getBalance(){
        $userId = $this->authService->getUserIdOrFail();
        
        $balance = $this->walletService->getBalance($userId);

        return $this->response->setJSON([
            'balance' => $balance
        ]);
    }

    public function getTransactions(){
        $userId = $this->authService->getUserIdOrFail();

        if($userId === null){
            throw new \Exception("User session not initialized");
        }

        $transactions = $this->walletService->getTransactions($userId);
        return $this->response->setJSON([
            'transactions' => $transactions
        ]);
    }
}