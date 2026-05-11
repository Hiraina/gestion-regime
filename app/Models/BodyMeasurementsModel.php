<?php

namespace App\Models;

use CodeIgniter\Model;
use ValueError;

class BodyMeasurementsModel extends Model{

    protected $table = "body_measurements";

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'height',
        'weight',
        'created_at'
    ];

    public function getLatestByUserId($userId){
        return $this -> where('user_id', $userId)
                     -> orderBy('created_at', 'DESC')
                     -> first();
    }

    // return the bodyMeasurement closest to the date
    public function getClosestToDateByUserId($userId, $date){        

        return $this->where('user_id', $userId)
                    ->orderBy("ABS(DATEDIFF(created_at, '$date'))", 'ASC')
                    ->first();
    }


}