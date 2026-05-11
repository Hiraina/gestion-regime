<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BodyMeasurementsModel;
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

// Dans GoalController.php

public function save()
{
    $db = Database::connect();

    $userId = session()->get('user_id');

    // Détection de la requête AJAX (JSON)
    if ($this->request->isAJAX()) {
        $json = $this->request->getJSON();
        $goalId = $json->goal_id ?? null;
        $minKg = $json->min_kg ?? null;
        $maxKg = $json->max_kg ?? null;
    } else {
        // Fallback pour requête classique (formulaire POST)
        $goalId = $this->request->getPost('goal_id');
        $minKg = $this->request->getPost('min_kg');
        $maxKg = $this->request->getPost('max_kg');
    }

    $startDate = date('Y-m-d');

    // Validation de l'objectif
    if (!$goalId) {
        return $this->respondWithMessage(false, 'Objectif obligatoire');
    }

    $goal = $db->table('goals')->where('id', $goalId)->get()->getRow();
    if (!$goal) {
        return $this->respondWithMessage(false, 'Objectif invalide');
    }

    // Use the goal ID sent by the client as-is.

    $data = [
        'user_id'    => $userId,
        'goal_id'    => $goalId,
        'start_date' => $startDate,
        'min_kg'     => null,
        'max_kg'     => null
    ];

    $goalDirectionLabel = null;

    // Objectifs nécessitant une fourchette de poids
    if (in_array((int) $goal->id, [1, 2], true) || in_array($goal->name, ['Prise de poids', 'Perte de poids'], true)) {
        if ($minKg === null || $maxKg === null || $minKg === '' || $maxKg === '') {
            return $this->respondWithMessage(false, 'Veuillez saisir le poids minimum et maximum');
        }

        if ($minKg > $maxKg) {
            return $this->respondWithMessage(false, 'Le poids minimum doit être inférieur au maximum');
        }

        $data['min_kg'] = $minKg;
        $data['max_kg'] = $maxKg;
    }

    if (stripos((string) $goal->name, 'imc') !== false) {
        $measurement = (new BodyMeasurementsModel())->getLatestByUserId($userId);
        if (empty($measurement) || empty($measurement['height']) || empty($measurement['weight'])) {
            return $this->respondWithMessage(false, 'Veuillez compléter votre taille et poids avant de fixer cet objectif.');
        }

        $heightMeters = ((float) $measurement['height']) / 100.0;
        if ($heightMeters <= 0) {
            return $this->respondWithMessage(false, 'Taille invalide pour le calcul IMC.');
        }

        $minIdealWeight = 18.5 * $heightMeters * $heightMeters;
        $maxIdealWeight = 24.9 * $heightMeters * $heightMeters;
        $currentWeight = (float) $measurement['weight'];

        if ($currentWeight < $minIdealWeight) {
            $data['min_kg'] = round($minIdealWeight - $currentWeight, 1);
            $data['max_kg'] = round($maxIdealWeight - $currentWeight, 1);
            $goalDirectionLabel = 'Prise de poids';
        } elseif ($currentWeight > $maxIdealWeight) {
            $data['min_kg'] = round($currentWeight - $maxIdealWeight, 1);
            $data['max_kg'] = round($currentWeight - $minIdealWeight, 1);
            $goalDirectionLabel = 'Perte de poids';
        } else {
            $data['min_kg'] = 0.0;
            $data['max_kg'] = 0.0;
            $goalDirectionLabel = 'Stabilisation';
        }
    }

    // Insertion
    $db->table('user_goals')->insert($data);
    $insertId = $db->insertID();

    // Récupération de l'objectif fraîchement inséré avec le nom
    $insertedGoal = $db->table('user_goals')
        ->select('user_goals.*, goals.name as goal_name')
        ->join('goals', 'goals.id = user_goals.goal_id')
        ->where('user_goals.id', $insertId)
        ->get()
        ->getRow();

    if ($insertedGoal && $goalDirectionLabel !== null) {
        $insertedGoal->goal_direction_label = $goalDirectionLabel;
    }

    return $this->respondWithMessage(true, 'Objectif enregistré avec succès !', $insertedGoal);
}

/**
 * Renvoie une réponse JSON si AJAX, sinon une redirection avec flashdata.
 */
private function respondWithMessage(bool $success, string $message, $goal = null)
{
    if ($this->request->isAJAX()) {
        $response = [
            'success' => $success,
            'message' => $message
        ];
        if ($goal) {
            $response['goal'] = $goal;
        }
        return $this->response->setJSON($response);
    } else {
        $type = $success ? 'success' : 'error';
        return redirect()->back()->with($type, $message);
    }
}
}