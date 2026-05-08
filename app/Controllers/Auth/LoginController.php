<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

class LoginController extends BaseController
{
    public function index(): string
    {
        helper('url');

        return view('auth/login');
    }
}
