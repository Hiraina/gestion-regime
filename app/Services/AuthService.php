<?php

namespace App\Services;

class AuthService{
    public function getUserIdOrFail(){
        if (!session()->has('user_id')) {
            throw new \Exception("User not authenticated");
        }

        return session()->get('user_id');
    }
}