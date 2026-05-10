<?php

namespace App\Controllers;

use App\Models\UserProfilesModel;
use App\Models\BodyMeasurementsModel;
use App\Models\UsersModel;
use App\Services\HealthService;

class ProfileController extends BaseController
{
public function index()
{
    $userId = session()->get('user_id');

    $profileModel = new \App\Models\UserProfilesModel();
    $bodyModel = new \App\Models\BodyMeasurementsModel();
    $userModel = new \App\Models\UsersModel();
    $healthService = new \App\Services\HealthService();

    $profile = $profileModel->where('user_id', $userId)->first();
    $user = $userModel->getUserById($userId);
    $measurement = $bodyModel->getLatestByUserId($userId);

    // Calcul de l'âge
    $age = null;
    if (!empty($user['birth_date'])) {
        $birth = new \DateTime($user['birth_date']);
        $today = new \DateTime();
        $age = $today->diff($birth)->y;
    }

    // Calcul de l'IMC si mesures existantes
    $imc = null;
    if ($measurement) {
        $imc = $healthService->calculateIMC($measurement);
        // Formater l'IMC avec 1 décimale pour l'affichage
$imc = number_format($imc, 1);
    }

    return view('profile/index', [
        'profile'     => $profile,
        'user'        => $user,
        'measurement' => $measurement,
        'imc'         => $imc,
        'age'         => $age,
    ]);
}





public function complete()
{
    $userId = session()->get('user_id');
    $userModel = new \App\Models\UsersModel();
    $profileModel = new \App\Models\UserProfilesModel();

    $user = $userModel->getUserById($userId);
    $profile = $profileModel->where('user_id', $userId)->first();

    return view('profile/complete', [
        'user'    => $user,
        'profile' => $profile,
    ]);
}




public function save()
{
    $userId = session()->get('user_id');
    $db = \Config\Database::connect();

    // 1. Récupération de la date de naissance envoyée
    $birthDate = $this->request->getPost('birth_date');

    // 2. Mise à jour de la date de naissance dans la table `users`
    //    On utilise le Query Builder directement pour éviter l'exception DataException
    $db->table('users')
       ->where('id', $userId)
       ->update(['birth_date' => $birthDate]);

    // 3. Calcul de l'âge à partir de la date fournie (0 si vide)
    $age = 0;
    if (!empty($birthDate)) {
        $birth = new \DateTime($birthDate);
        $today = new \DateTime();
        $age = $today->diff($birth)->y;
    }

    // 4. Récupération ou création du profil
    $profileModel = new \App\Models\UserProfilesModel();
    $existingProfile = $profileModel->where('user_id', $userId)->first();

    $profileData = [
        'user_id'       => $userId,
        'age'           => $age,
        'num_telephone' => $this->request->getPost('num_telephone'),
        'adresse'       => $this->request->getPost('adresse'),
    ];

    if ($existingProfile) {
        // Mise à jour via Query Builder (aucune exception si données inchangées)
        $db->table('user_profiles')
           ->where('id', $existingProfile['id'])
           ->update($profileData);
    } else {
        $profileModel->insert($profileData);
    }

    return redirect()->to('/profile')->with('success', 'Profil mis à jour avec succès');
}

}