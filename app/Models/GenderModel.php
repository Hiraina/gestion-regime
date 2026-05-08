<?php
namespace App\Models;

use CodeIgniter\Model;

class GenderModel extends Model
{
    protected $table = 'gender';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name'];
}