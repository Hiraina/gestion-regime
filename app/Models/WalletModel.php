<?php

namespace App\Models;

use CodeIgniter\Model;

class WalletModel extends Model
{
    protected $table = 'wallet';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'balance'
    ];
}