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
    public function getMockData($userId){

        $data = [
            [
                'id'=>0,
                'user_id'=>0,
                'height'=>170,
                'weight'=>70,
                'created_at'=>"2026-05-2"
            ],
            [
                'id'=>1,
                'user_id'=>1,
                'height'=>165,
                'weight'=>50,
                'created_at'=>"2026-04-25"
            ],
            
            [
                'id'=>2,
                'user_id'=>2,
                'height'=>150,
                'weight'=>75,
                'created_at'=>"2026-03-18"
            ],
            
            [
                'id'=>3,
                'user_id'=>3,
                'height'=>180,
                'weight'=>120,
                'created_at'=>"2026-01-28"
            ],

        ];

        return $data[$userId];
    }

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