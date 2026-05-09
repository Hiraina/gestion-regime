<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model {
    protected $table = "users";

    protected $allowedFields = [
        'name',
        'email',
        'password',
        'gender_id',
        'is_gold'
    ];

    public function getUserById($userId){    
        return $this -> select('users.*, gender.name as gender') 
                     -> join('gender', 'gender.id = users.gender_id')
                    -> where('users.id' , $userId)
                    -> first();
        
    }
    
    //  récupérer user par email
    public function getByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

}