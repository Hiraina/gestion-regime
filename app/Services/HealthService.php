<?php

namespace App\Services;

class HealthService {
    public function calculateIMC($measurements){
        /**
         * $weight en kg
         * $height en m
         */

        $height = $measurements['height'];
        $weight = $measurements['weight'];

        // convertir height en m
        $height_meter = $height / 100;

        return $weight / ($height_meter * $height_meter);
    }

    public function calculateBMR($measurements, $userInfo){
        // Mifflin-St Jeor Equation
        /**
         * weight en Kg
         * height en cm
         * age en année
         */

        $height = $measurements['height'];
        $weight = $measurements['weight'];


        $age = date_diff(
            date_create($userInfo['birth_date']),
            date_create('today')
        )->y;

        $gender_variable = str_replace(' ', '', strtolower($userInfo['gender'])) == "homme" ? 5 : -161;

        return (10 * $weight) + (6.25 * $height) - (5 * $age) + $gender_variable;
    }

    public function getIMCCategory($imc){
        /**
         * adulte
         * 
         * IMC < 18.5 : Poids insuffisant (maigreur)
         * 18.5 <= IMC < 25 : Poids normal (corpulence saine)
         * 25 <= IMC < 30 : Surpoids
         * IMC >= 30 : obésité
         */

        if($imc <= 0){
            return "L'IMC doit-être supérieur à 0 -> IMC = " . $imc;
        }

        if($imc < 18.5){
            // maigreur
            return "maigreur";
        } elseif ($imc < 25){
            // corpulence saine
            return "corpulence saine";
        } elseif ($imc < 30){
            // surpoids
            return "surpoids";
        } else {
            // obésité
            return "obésité";
        }
    }

}
