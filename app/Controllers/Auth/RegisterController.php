<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class RegisterController extends BaseController
{
    public function step1(): string
    {
        helper('url');

        return view('auth/register-step1');
    }

    public function saveStep1(): RedirectResponse
    {
        helper('url');

        session()->set('register_step1', [
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'email' => trim((string) $this->request->getPost('email')),
            'gender' => (string) $this->request->getPost('gender'),
            'date_of_birth' => (string) $this->request->getPost('date_of_birth'),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('register/health'));
    }

    public function health(): string
    {
        helper('url');

        $step1 = session()->get('register_step1');

        if (! $step1) {
            return view('auth/register-health', [
                'missingStep1' => true,
            ]);
        }

        return view('auth/register-health', [
            'missingStep1' => false,
            'fullName' => $step1['full_name'],
        ]);
    }

    public function saveHealth(): RedirectResponse
    {
        helper('url');

        $height = (float) $this->request->getPost('height');
        $weight = (float) $this->request->getPost('weight');
        $heightInMeters = $height / 100;
        $imc = $heightInMeters > 0 ? round($weight / ($heightInMeters * $heightInMeters), 2) : null;

        session()->set('register_health', [
            'height' => $height,
            'weight' => $weight,
            'imc' => $imc,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Later, register_step1 + register_health can be saved into users and body_measurements.
        return redirect()->to(base_url('profile/complete'));
    }
}
