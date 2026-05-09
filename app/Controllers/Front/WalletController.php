<?php  

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Services\WalletService;
use App\Models\UsersModel;

class WalletController extends BaseController
{
    private $walletService;

    public function __construct()
    {
        $this->walletService = new WalletService();
    }

    public function creditWallet(){        
        $userId = session()->get('user_id');

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
        $userId = session()->get('user_id');

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
        $userId = session()->get('user_id');
        
    }

    public function getTransactions(){
        $userId = session()->get('user_id');
    }
}