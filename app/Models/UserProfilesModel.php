<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProfilesModel extends Model
{
    protected $table = 'user_profiles';

    protected $allowedFields = [
        'user_id',
        'age',
        'activity_level',
        'objective',
        'diet_type',
        'allergies'
    ];
}