<?php

namespace App\Controllers;

use App\Services\CodeRechargeService;

class CodesController extends BaseController
{
    public function redeem()
    {
        $json = $this->request->getJSON();
        $codeValue = trim($json->code ?? '');
        if ($codeValue === '') {
            $codeValue = trim((string) $this->request->getPost('code'));
        }

        $userId = $this->authService->getUserIdOrFail();
        $service = new CodeRechargeService();

        try {
            $result = $service->redeem($userId, $codeValue);
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Code valide ! +' . number_format($result['amount'], 2) . ' EUR ajoutes.',
            'new_balance' => number_format($result['new_balance'], 2),
        ]);
    }
}