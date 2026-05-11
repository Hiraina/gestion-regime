<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Services\GoldService;
use App\Services\WalletService;

class GoldController extends BaseController
{
    private GoldService $goldService;
    private WalletService $walletService;

    public function __construct()
    {
        $this->goldService = new GoldService();
        $this->walletService = new WalletService();
    }

    public function index()
    {
        $userId = $this->authService->getUserIdOrFail();
        $isGold = $this->goldService->isGold($userId);

        return view('gold/index', [
            'isGold' => $isGold,
            'discountRate' => $this->goldService->getDiscountRate(),
            'upgradePrice' => $this->goldService->getUpgradePrice(),
            'balance' => $this->walletService->getBalance($userId),
        ]);
    }

    public function status()
    {
        $userId = $this->authService->getUserIdOrFail();
        $isGold = $this->goldService->isGold($userId);

        return $this->response->setJSON([
            'is_gold' => $isGold,
            'discount_rate' => $this->goldService->getDiscountRate(),
        ]);
    }

    public function activate()
    {
        $userId = $this->authService->getUserIdOrFail();
        $this->goldService->activateGold($userId);

        return $this->response->setJSON([
            'response' => 'success',
            'is_gold' => true,
        ]);
    }

    public function purchase()
    {
        $userId = $this->authService->getUserIdOrFail();

        try {
            $result = $this->goldService->purchaseGold($userId);
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'response' => 'failure',
                'message' => $th->getMessage(),
            ]);
        }

        return $this->response->setJSON([
            'response' => 'success',
            'result' => $result,
        ]);
    }

    public function deactivate()
    {
        $userId = $this->authService->getUserIdOrFail();
        $this->goldService->deactivateGold($userId);

        return $this->response->setJSON([
            'response' => 'success',
            'is_gold' => false,
        ]);
    }
}
