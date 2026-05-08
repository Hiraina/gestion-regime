<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'email',
        'password',
        'gender_id',
        'is_gold'
    ];

    // 🔐 récupérer user par email
    public function getByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
}