<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class ProfileController extends BaseController
{
    public function complete(): string
    {
        helper('url');

        return view('front/profile-completion');
    }

    public function saveCompletion(): RedirectResponse
    {
        helper('url');

        session()->set('profile_completion', [
            'main_goal' => (string) $this->request->getPost('main_goal'),
            'activity_level' => (string) $this->request->getPost('activity_level'),
            'food_habit' => (string) $this->request->getPost('food_habit'),
            'target_weight' => $this->request->getPost('target_weight') !== ''
                ? (float) $this->request->getPost('target_weight')
                : null,
            'completed_at' => date('Y-m-d H:i:s'),
        ]);

        // Later, this can redirect to the real user dashboard after database save.
        return redirect()->to(base_url('dashboard'));
    }
}
