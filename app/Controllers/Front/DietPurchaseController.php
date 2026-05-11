<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Services\DietPurchaseService;

class DietPurchaseController extends BaseController
{
    private DietPurchaseService $dietPurchaseService;

    public function __construct()
    {
        $this->dietPurchaseService = new DietPurchaseService();
    }

    public function purchase()
    {
        $userId = $this->authService->getUserIdOrFail();
        $dietId = (int) $this->request->getPost('diet_id');
        $durationDays = (int) $this->request->getPost('duration_days');

        if ($dietId <= 0 || $durationDays <= 0) {
            return $this->response->setJSON([
                'response' => 'failure',
                'message' => 'Parametres invalides.',
            ]);
        }

        try {
            $result = $this->dietPurchaseService->purchase($userId, $dietId, $durationDays);
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
}
