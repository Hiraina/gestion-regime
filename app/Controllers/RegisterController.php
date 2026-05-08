<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BodyMeasurementModel;
use App\Models\WalletModel;

class RegisterController extends BaseController
{
    public function step1()
    {
        return view('register/step1');
    }

    public function postStep1()
    {
        session()->set('register_user', [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'gender_id' => $this->request->getPost('gender_id'),
            'is_gold' => 0
        ]);

        return redirect()->to('/register/step2');
    }

    public function step2()
    {
        if (!session()->get('register_user')) {
            return redirect()->to('/register/step1');
        }

        return view('register/step2');
    }

    public function postStep2()
    {
        $userData = session()->get('register_user');

        $userModel = new UserModel();
        $bodyModel = new BodyMeasurementModel();
        $walletModel = new WalletModel();

        // 1. Insert user
        $userId = $userModel->insert($userData);

        // 2. Insert body measurement
        $bodyModel->insert([
            'user_id' => $userId,
            'height' => $this->request->getPost('height'),
            'weight' => $this->request->getPost('weight'),
            'created_at' => date('Y-m-d')
        ]);

        // 3. Create wallet
        $walletModel->insert([
            'user_id' => $userId,
            'balance' => 0
        ]);

        // clear session
        session()->remove('register_user');

        return redirect()->to('/login')->with('success', 'Inscription réussie !');
    }
}