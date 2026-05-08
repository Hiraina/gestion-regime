<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $session = session();
        $userModel = new UserModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // 🔎 find user
        $user = $userModel->getByEmail($email);

        // ❌ user not found
        if (!$user) {
            return redirect()->back()->with('error', 'Email incorrect');
        }

        // 🔐 verify password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Mot de passe incorrect');
        }

        // ✅ set session
        $session->set([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'is_logged_in' => true
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}