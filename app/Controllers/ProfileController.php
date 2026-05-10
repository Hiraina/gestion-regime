<?php

namespace App\Controllers;

use App\Models\UserProfilesModel;

class ProfileController extends BaseController
{
    public function index()
    {
        $profileModel = new UserProfilesModel();

        $profile = $profileModel
            ->where('user_id', session()->get('user_id'))
            ->first();

        return view('profile/index', [
            'profile' => $profile
        ]);
    }

    public function complete()
    {
        return view('profile/complete');
    }

    public function save()
    {
        $profileModel = new UserProfilesModel();

        $profile = $profileModel
            ->where('user_id', session()->get('user_id'))
            ->first();

        $data = [
            'user_id' => session()->get('user_id'),
            'age' => $this->request->getPost('age'),
            'activity_level' => $this->request->getPost('activity_level'),
            'objective' => $this->request->getPost('objective'),
            'diet_type' => $this->request->getPost('diet_type'),
            'allergies' => $this->request->getPost('allergies'),
        ];

        if ($profile) {
            $profileModel->update($profile['id'], $data);
        } else {
            $profileModel->insert($data);
        }

        return redirect()->to('/profile')
            ->with('success', 'Profil mis à jour avec succès');
    }
}