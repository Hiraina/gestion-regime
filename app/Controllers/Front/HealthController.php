<?php  

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Services\HealthService;
use App\Models\BodyMeasurementsModel;
use App\Models\UsersModel;

class HealthController extends BaseController
{
    public function getMetrics(){
        $userId = 0; // fixed for testing

        $bodyMeasurementModel = new BodyMeasurementsModel();
        $measurement = $bodyMeasurementModel -> getLatestByUserId($userId);

        $userModel = new UsersModel();
        $userInfo = $userModel -> getUserById($userId);

        $healthService = new HealthService();

        $imc = $healthService->calculateIMC($measurement);
        $bmr = $healthService->calculateBMR($measurement, $userInfo);
        $category = $healthService->getIMCCategory($imc);

        return $this->response->setJSON([
            'imc' => $imc,
            'bmr' => $bmr,
            'category' => $category
        ]);
    }
}