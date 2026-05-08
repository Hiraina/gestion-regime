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

    public function getMockData($userId){
        $data = [
            [
                'id'=> 0,
                'name'=> "Rakoto",
                'email'=> "rakoto@gmail.com",
                'password'=> "1234",
                'birth_date'=> "2008-01-23",
                'gender'=> "Homme",
                'is_gold'=> false
            ],
            [
                'id'=> 1,
                'name'=> "Jean",
                'email'=> "jean@gmail.com",
                'password'=> "1234",
                'birth_date'=> "2005-05-25",
                'gender'=> "Homme",
                'is_gold'=> true
            ],
            [
                'id'=> 2,
                'name'=> "Marie",
                'email'=> "marie@gmail.com",
                'password'=> "1234",
                'birth_date'=> "2000-02-26",
                'gender'=> "Femme",
                'is_gold'=> false
            ],
            [
                'id'=> 3,
                'name'=> "Rose",
                'email'=> "rose@gmail.com",
                'password'=> "1234",
                'birth_date'=> "2006-10-25",
                'gender'=> "Femme",
                'is_gold'=> true
            ]
        ];


        return $data[$userId];
    }

    public function getUserById($userId){

//        return $this->getMockData($userId);

        // a decommenter quand la bdd est utilisable
        return $this -> select('users.*, gender.name as gender') 
                     -> join('gender', 'gender.id = users.gender_id')
                    -> where('users.id' , $userId)
                    -> first();
        //
    }
    //  récupérer user par email
    public function getByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

}