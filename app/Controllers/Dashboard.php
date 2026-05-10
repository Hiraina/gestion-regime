<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Front\HealthController;
use App\Models\WalletsModel;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        // Récupération des métriques santé (IMC, BMR, catégorie)
        $healthController = new HealthController();
        $data = $healthController->getMetricsByUserId($userId);

        // Récupération du solde du portefeuille
        $walletModel = new WalletsModel();
        $wallet = $walletModel->where('user_id', $userId)->first();
        $data['balance'] = $wallet ? number_format($wallet['balance'], 2, '.', '') : '0.00';

        // Ajout du nom pour le message d'accueil
        $data['user_name'] = session()->get('user_name');
// Formater l'IMC avec 1 décimale pour l'affichage
$data['imc'] = number_format($data['imc'], 1);







$db = \Config\Database::connect();

$lastGoal = $db->table('user_goals')
    ->select('user_goals.*, goals.name as goal_name')
    ->join('goals', 'goals.id = user_goals.goal_id')
    ->where('user_goals.user_id', $userId)
    ->orderBy('user_goals.id', 'DESC')
    ->limit(1)
    ->get()
    ->getRow();

$data['current_goal'] = $lastGoal;




        return view('dashboard', $data);
    }
}