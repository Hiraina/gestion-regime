<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class GoalController extends BaseController
{
    public function index()
    {
        $db = Database::connect();

        $goals = $db->table('goals')->get()->getResult();

        return view('goals/index', [
            'goals' => $goals
        ]);
    }

    public function save()
    {
        $db = Database::connect();

        $userId = session()->get('user_id');

        $goalId = $this->request->getPost('goal_id');
        $minKg = $this->request->getPost('min_kg');
        $maxKg = $this->request->getPost('max_kg');
        $startDate = date('Y-m-d');

        // validation simple
        if (!$goalId) {
            return redirect()->back()->with('error', 'Objectif obligatoire');
        }

        // Vérifier type objectif
        $goal = $db->table('goals')->where('id', $goalId)->get()->getRow();

        if (!$goal) {
            return redirect()->back()->with('error', 'Objectif invalide');
        }

        $data = [
            'user_id' => $userId,
            'goal_id' => $goalId,
            'start_date' => $startDate,
            'min_kg' => null,
            'max_kg' => null
        ];

        // Si objectif = perte ou prise de poids
        if (in_array($goalId, [1, 2])) {
            if ($minKg === null || $maxKg === null) {
                return redirect()->back()->with('error', 'Veuillez saisir min et max kg');
            }

            if ($minKg > $maxKg) {
                return redirect()->back()->with('error', 'Min kg doit être inférieur à max kg');
            }

            $data['min_kg'] = $minKg;
            $data['max_kg'] = $maxKg;
        }

        $db->table('user_goals')->insert($data);

        return redirect()->to('/dashboard')->with('success', 'Objectif enregistré');
    }
}