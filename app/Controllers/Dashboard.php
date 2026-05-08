<?php

namespace App\Controllers;
use App\Controllers\Front\HealthController;
use App\Services\HealthService;
class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }
        $userId = session()->get('user_id');

        $healthController = new HealthController();

        $data = $healthController->getMetricsByUserId($userId);

        return view('dashboard', $data);
        
    }
}