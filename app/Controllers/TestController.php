<?php

namespace App\Controllers;

class TestController extends BaseController
{
    public function index(): string
    {
        return view('testing_view');
    }
}
