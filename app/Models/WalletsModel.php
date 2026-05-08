<?php

namespace App\Models;

use CodeIgniter\Model;

class WalletsModel extends Model
{
    protected $table = 'wallets';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'balance'
    ];
}