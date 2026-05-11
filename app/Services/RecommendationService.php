<?php

namespace App\Services;

use App\Models\RecommendationsModel;
use App\Models\RecommendationActivitiesModel;
use App\Models\BodyMeasurementsModel;
use App\Models\ActivitiesModel;
use App\Models\FoodItemsModel;
use App\Models\UsersModel;
use App\Models\UserGoalsModel;
use App\Models\DietsModel;
use App\Models\FoodDistributionsModel;
use App\Models\DietCompositionsModel;

class RecommendationService
{
    private $recommendationModel;
    private $recommendationActivitiesModel;
    private $bodyMeasurementsModel;
    private $activitiesModel;
    private $foodItemsModel;
    private $usersModel;
    private $userGoalsModel;
    private $dietsModel;
    private $foodDistributionsModel;
    private $dietCompositionsModel;
    private $healthService;

    public function __construct()
    {
        $this->recommendationModel = new RecommendationsModel();
        $this->recommendationActivitiesModel = new RecommendationActivitiesModel();
        $this->bodyMeasurementsModel = new BodyMeasurementsModel();
        $this->activitiesModel = new ActivitiesModel();
        $this->foodItemsModel = new FoodItemsModel();
        $this->usersModel = new UsersModel();
        $this->userGoalsModel = new UserGoalsModel();
        $this->dietsModel = new DietsModel();
        $this->foodDistributionsModel = new FoodDistributionsModel();
        $this->dietCompositionsModel = new DietCompositionsModel();
        $this->healthService = new HealthService();
    }

    public function calculateActivityCalBurn(float $met, float $weightKg, float $durationHours): float
    {
        if ($met < 0 || $weightKg <= 0 || $durationHours < 0) {
            throw new \InvalidArgumentException('Invalid inputs for activity calorie burn calculation.');
        }

        return $met * $weightKg * $durationHours;
    }

    public function calculateTotalCaloriesBurnt(float $bmr, float $activityBurn): float
    {
        return $bmr + $activityBurn;
    }

    public function calculateTotalCaloriesGained(array $foodItemIds, float $portionGrams = 100.0): float
    {
        if ($portionGrams <= 0) {
            throw new \InvalidArgumentException('Portion grams must be greater than 0.');
        }

        return $this->foodItemsModel->calculateCaloriesForItems($foodItemIds, $portionGrams);
    }

    public function calculateNetGain(float $totalGain, float $totalBurnt): float
    {
        return $totalGain - $totalBurnt;
    }

    public function generateMultipleRecommendations(int $userId, array $draft, int $count = 3): array
    {
        if (empty($draft['distributions']) || empty($draft['items']) || empty($draft['activities'])) {
            throw new \InvalidArgumentException('Draft incomplet pour la génération.');
        }

        $measurement = $this->bodyMeasurementsModel->getLatestByUserId($userId);
        if (empty($measurement)) {
            throw new \RuntimeException('Aucune mesure corporelle trouvée pour cet utilisateur.');
        }

        $user = $this->usersModel->getUserById($userId);
        if (empty($user) || empty($user['birth_date'])) {
            throw new \RuntimeException('La date de naissance est obligatoire avant la génération.');
        }

        $goal = $this->userGoalsModel->getLatestWithGoalByUserId($userId);
        if (empty($goal)) {
            throw new \RuntimeException('Aucun objectif utilisateur trouvé.');
        }

        $bmr = $this->healthService->calculateBMR($measurement, $user);
        if ($bmr === null) {
            throw new \RuntimeException('Impossible de calculer le BMR avec les données disponibles.');
        }

        $weightKg = (float) ($measurement['weight'] ?? 0);
        if ($weightKg <= 0) {
            throw new \RuntimeException('Poids invalide pour la génération.');
        }

        $imc = (float) $this->healthService->calculateIMC($measurement);
        $goalConfig = $this->buildGoalConfig($goal, $imc);

        $activityIds = array_values(array_unique(array_map('intval', (array) ($draft['activities'] ?? []))));
        $activities = $this->activitiesModel->getByIds($activityIds);
        if (empty($activities)) {
            throw new \RuntimeException('Aucune activité valide sélectionnée.');
        }

        $foodItemIds = $this->extractUniqueFoodItemIds($draft);
        if (empty($foodItemIds)) {
            throw new \RuntimeException('Aucun aliment valide sélectionné.');
        }

        $profiles = [
            [
                'key' => 'conservative',
                'net_multiplier' => 0.80,
                'duration_multiplier' => 0.90,
                'frequency_per_week' => 4,
            ],
            [
                'key' => 'balanced',
                'net_multiplier' => 1.00,
                'duration_multiplier' => 1.00,
                'frequency_per_week' => 5,
            ],
            [
                'key' => 'intensive',
                'net_multiplier' => 1.15,
                'duration_multiplier' => 1.10,
                'frequency_per_week' => 6,
            ],
        ];

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $recommendations = [];
            $targetCount = max(1, min($count, count($profiles)));
            $goalDirection = $goalConfig['type'] ?? 'maintenance';
            $baseTargetNet = (float) $goalConfig['target_net'];

            for ($idx = 0; $idx < $targetCount; $idx++) {
                $profile = $profiles[$idx];

                $targetNet = $this->scaleGoalNetByProfile($goalDirection, $baseTargetNet, (float) $profile['net_multiplier']);
                $targetActivityBurn = $this->getTargetActivityBurn($goalDirection, (int) $profile['frequency_per_week'], (float) $profile['duration_multiplier']);

                $targetIntakeCalories = (float) $bmr + $targetActivityBurn + $targetNet;
                if ($goalDirection === 'weight_gain' && $targetIntakeCalories <= (float) $bmr) {
                    $targetIntakeCalories = (float) $bmr + abs($targetNet) + $targetActivityBurn;
                }
                if ($goalDirection === 'weight_loss' && $targetIntakeCalories >= (float) $bmr) {
                    $targetIntakeCalories = max(900.0, (float) $bmr - abs($targetNet) - $targetActivityBurn);
                }

                $portionGrams = $this->calculatePortionGramsForTargetCalories($foodItemIds, $targetIntakeCalories, $goalDirection);

                $durationsHours = $this->generateActivityDurationsHours(
                    $activities,
                    $weightKg,
                    $targetActivityBurn,
                    (float) $profile['duration_multiplier'],
                    (int) $profile['frequency_per_week']
                );

                $metrics = $this->calculateGenerationMetrics($userId, $draft, $durationsHours, $portionGrams);

                // Ensure the sign matches the intended goal direction.
                if ($goalDirection === 'weight_gain' && ($metrics['net_gain'] ?? 0) <= 0) {
                    $portionGrams = $this->calculatePortionGramsForTargetCalories($foodItemIds, $targetIntakeCalories + abs((float) ($metrics['net_gain'] ?? 0)) + 250.0, $goalDirection);
                    $metrics = $this->calculateGenerationMetrics($userId, $draft, $durationsHours, $portionGrams);
                } elseif ($goalDirection === 'weight_loss' && ($metrics['net_gain'] ?? 0) >= 0) {
                    $portionGrams = $this->calculatePortionGramsForTargetCalories($foodItemIds, max(900.0, $targetIntakeCalories - abs((float) ($metrics['net_gain'] ?? 0)) - 250.0), $goalDirection);
                    $metrics = $this->calculateGenerationMetrics($userId, $draft, $durationsHours, $portionGrams);
                }

                $compositionPreview = $this->buildDietCompositionPreview($draft, $portionGrams, $goalDirection);

                $recData = [
                    'user_id' => $userId,
                    'diet_id' => null,
                    'generated_at' => date('Y-m-d H:i:s'),
                    'start_date' => null,
                    'end_date' => null,
                    'status' => 'generated_' . $profile['key'],
                    'trigger_measurement_id' => $measurement['id'] ?? null,
                ];

                $this->recommendationModel->insert($recData);
                $recId = $this->recommendationModel->getInsertID();
                if (!$recId) {
                    throw new \RuntimeException('Echec de création d\'une recommandation candidate.');
                }

                foreach ($activityIds as $activityId) {
                    $hours = (float) ($durationsHours[$activityId] ?? 0.0);
                    $this->recommendationActivitiesModel->insert([
                        'recommendation_id' => $recId,
                        'activity_id' => $activityId,
                        'frequency_per_week' => (int) $profile['frequency_per_week'],
                        'duration_minutes' => $hours > 0 ? (int) round($hours * 60) : 0,
                    ]);
                }

                $recommendations[] = [
                    'id' => $recId,
                    'profile' => $profile['key'],
                    'goal_type' => $goalConfig['type'],
                    'target_net' => $targetNet,
                    'target_intake_calories' => $targetIntakeCalories,
                    'metrics' => $metrics,
                    'diet_composition' => $compositionPreview,
                ];
            }

            $db->transCommit();

            return [
                'count' => count($recommendations),
                'recommendations' => $recommendations,
            ];
        } catch (\Throwable $th) {
            $db->transRollback();
            throw $th;
        }
    }

    public function calculateGenerationMetrics(
        int $userId,
        array $draft,
        array $activityDurationsHours = [],
        float $portionGrams = 100.0
    ): array {
        $measurement = $this->bodyMeasurementsModel->getLatestByUserId($userId);
        if (empty($measurement)) {
            throw new \RuntimeException('No body measurement found for user.');
        }

        $user = $this->usersModel->getUserById($userId);
        if (empty($user) || empty($user['birth_date'])) {
            throw new \RuntimeException('User birth date is required before generation.');
        }

        $bmr = $this->healthService->calculateBMR($measurement, $user);
        if ($bmr === null) {
            throw new \RuntimeException('Unable to calculate BMR with current profile and measurements.');
        }

        $weightKg = (float) $measurement['weight'];
        $activityIds = array_values(array_unique(array_map('intval', $draft['activities'] ?? [])));
        $activities = $this->activitiesModel->getByIds($activityIds);

        $totalActivityBurn = 0.0;
        foreach ($activities as $activity) {
            $activityId = (int) $activity['id'];
            $met = (float) ($activity['met_value'] ?? 0);
            $durationHours = (float) ($activityDurationsHours[$activityId] ?? 0);
            $totalActivityBurn += $this->calculateActivityCalBurn($met, $weightKg, $durationHours);
        }

        $foodItemIds = [];
        foreach (($draft['items'] ?? []) as $itemIds) {
            foreach ((array) $itemIds as $itemId) {
                $foodItemIds[] = (int) $itemId;
            }
        }

        $foodItemIds = array_values(array_unique($foodItemIds));
        $totalGain = $this->calculateTotalCaloriesGained($foodItemIds, $portionGrams);
        $totalBurnt = $this->calculateTotalCaloriesBurnt((float) $bmr, $totalActivityBurn);
        $netGain = $this->calculateNetGain($totalGain, $totalBurnt);

        return [
            'bmr' => (float) $bmr,
            'activity_burn' => $totalActivityBurn,
            'total_burnt' => $totalBurnt,
            'total_gain' => $totalGain,
            'net_gain' => $netGain,
            'weight_kg' => $weightKg,
            'portion_grams' => $portionGrams,
            'activity_durations_hours' => $activityDurationsHours,
        ];
    }
    
    public function createRecommendation(int $userId, array $draft): array
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Find latest measurement to use as trigger if available
            $measurement = $this->bodyMeasurementsModel->getLatestByUserId($userId);
            $triggerId = $measurement['id'] ?? null;

            $recData = [
                'user_id' => $userId,
                'diet_id' => null,
                'generated_at' => date('Y-m-d H:i:s'),
                'start_date' => null,
                'end_date' => null,
                'status' => 'pending',
                'trigger_measurement_id' => $triggerId
            ];

            $this->recommendationModel->insert($recData);
            $recId = $this->recommendationModel->getInsertID();

            if (!$recId) {
                throw new \Exception('Failed to create recommendation');
            }

            // Persist activities
            $activities = $draft['activities'] ?? [];
            foreach ($activities as $activityId) {
                $this->recommendationActivitiesModel->insert([
                    'recommendation_id' => $recId,
                    'activity_id' => $activityId,
                    'frequency_per_week' => null,
                    'duration_minutes' => null
                ]);
            }

            $db->transCommit();

            return [
                'id' => $recId,
                'status' => 'created'
            ];
        } catch (\Throwable $th) {
            $db->transRollback();
            throw $th;
        }
    }

    private function extractUniqueFoodItemIds(array $draft): array
    {
        $foodItemIds = [];
        foreach (($draft['items'] ?? []) as $itemIds) {
            foreach ((array) $itemIds as $itemId) {
                $foodItemIds[] = (int) $itemId;
            }
        }

        return array_values(array_unique(array_filter($foodItemIds)));
    }

    private function buildGoalConfig(array $goal, float $imc): array
    {
        $goalName = strtolower((string) ($goal['goal_name'] ?? $goal['name'] ?? ''));

        if (strpos($goalName, 'perte') !== false || strpos($goalName, 'perdre') !== false) {
            return ['type' => 'weight_loss', 'target_net' => -550.0];
        }

        if (strpos($goalName, 'prise') !== false || strpos($goalName, 'prendre') !== false) {
            return ['type' => 'weight_gain', 'target_net' => 550.0];
        }

        if ($imc >= 25.0) {
            return ['type' => 'imc_rebalance', 'target_net' => -350.0];
        }

        if ($imc < 18.5) {
            return ['type' => 'imc_rebalance', 'target_net' => 350.0];
        }

        return ['type' => 'maintenance', 'target_net' => -100.0];
    }

    private function generateActivityDurationsHours(
        array $activities,
        float $weightKg,
        float $activityBurnNeeded,
        float $durationMultiplier,
        int $frequencyPerWeek
    ): array {
        $frequencyPerWeek = max(1, $frequencyPerWeek);
        $durationMultiplier = max(0.5, $durationMultiplier);

        $coeff = 0.0;
        foreach ($activities as $activity) {
            $coeff += max(0.0, (float) ($activity['met_value'] ?? 0.0)) * $weightKg;
        }

        $baseHoursPerSession = 0.5;
        if ($coeff > 0 && $activityBurnNeeded > 0) {
            $baseHoursPerSession = ($activityBurnNeeded / $coeff) / $frequencyPerWeek;
        }

        $baseHoursPerSession *= $durationMultiplier;

        $durations = [];
        foreach ($activities as $activity) {
            $hours = min(2.5, max(0.25, $baseHoursPerSession));
            $durations[(int) $activity['id']] = round($hours, 2);
        }

        return $durations;
    }

    private function getTargetActivityBurn(string $goalType, int $frequencyPerWeek, float $durationMultiplier): float
    {
        $frequencyPerWeek = max(1, $frequencyPerWeek);
        $durationMultiplier = max(0.5, $durationMultiplier);

        $baseBurn = match ($goalType) {
            'weight_gain' => 90.0,
            'weight_loss' => 450.0,
            'imc_rebalance' => 280.0,
            default => 220.0,
        };

        return $baseBurn * $durationMultiplier;
    }

    private function scaleGoalNetByProfile(string $goalType, float $baseTargetNet, float $profileMultiplier): float
    {
        $scaled = abs($baseTargetNet) * max(0.5, $profileMultiplier);

        return match ($goalType) {
            'weight_gain' => max(250.0, $scaled),
            'weight_loss' => -max(250.0, $scaled),
            'imc_rebalance' => $baseTargetNet >= 0 ? max(150.0, $scaled) : -max(150.0, $scaled),
            default => $baseTargetNet,
        };
    }

    private function calculatePortionGramsForTargetCalories(array $foodItemIds, float $targetCalories, string $goalType = 'maintenance'): float
    {
        $items = $this->foodItemsModel->getByIds($foodItemIds);
        $caloriesPer100gSum = 0.0;

        foreach ($items as $item) {
            $caloriesPer100gSum += max(0.0, (float) ($item['calories_per_100g'] ?? 0));
        }

        if ($caloriesPer100gSum <= 0) {
            return 100.0;
        }

        $portionGrams = ($targetCalories / $caloriesPer100gSum) * 100.0;

        $min = 25.0;
        $max = 300.0;
        if ($goalType === 'weight_gain') {
            $min = 100.0;
            $max = 700.0;
        } elseif ($goalType === 'weight_loss') {
            $min = 25.0;
            $max = 250.0;
        }

        return round(max($min, min($max, $portionGrams)), 2);
    }

    public function getCandidatesForUser(int $userId, array $generated = []): array
    {
        $generatedById = [];
        foreach ($generated as $entry) {
            $id = (int) ($entry['id'] ?? 0);
            if ($id > 0) {
                $generatedById[$id] = $entry;
            }
        }

        $candidateIds = array_keys($generatedById);
        if (!empty($candidateIds)) {
            $recommendations = $this->recommendationModel
                ->where('user_id', $userId)
                ->whereIn('id', $candidateIds)
                ->findAll();
        } else {
            $recommendations = $this->recommendationModel
                ->where('user_id', $userId)
                ->like('status', 'generated_', 'after')
                ->orderBy('generated_at', 'DESC')
                ->findAll(5);
        }

        if (empty($recommendations)) {
            return [];
        }

        $latestMeasurement = $this->bodyMeasurementsModel->getLatestByUserId($userId);
        $currentWeight = isset($latestMeasurement['weight']) ? (float) $latestMeasurement['weight'] : null;
        $latestGoal = $this->userGoalsModel->getLatestWithGoalByUserId($userId);

        $recommendationIds = array_map(static fn ($row) => (int) $row['id'], $recommendations);

        $activityRows = $this->recommendationActivitiesModel
            ->select('recommendation_activities.recommendation_id, recommendation_activities.frequency_per_week, recommendation_activities.duration_minutes, activities.name AS activity_name')
            ->join('activities', 'activities.id = recommendation_activities.activity_id', 'left')
            ->whereIn('recommendation_activities.recommendation_id', $recommendationIds)
            ->orderBy('activities.name', 'ASC')
            ->findAll();

        $activitiesByRecommendation = [];
        foreach ($activityRows as $row) {
            $recId = (int) $row['recommendation_id'];
            $activitiesByRecommendation[$recId][] = [
                'name' => $row['activity_name'] ?? 'Activité',
                'duration_minutes' => (int) ($row['duration_minutes'] ?? 0),
                'frequency_per_week' => (int) ($row['frequency_per_week'] ?? 0),
            ];
        }

        $result = [];
        foreach ($recommendations as $recommendation) {
            $id = (int) $recommendation['id'];
            $generatedEntry = $generatedById[$id] ?? [];

            $profile = $generatedEntry['profile'] ?? $this->extractProfileFromStatus((string) ($recommendation['status'] ?? ''));
            $netGain = $generatedEntry['metrics']['net_gain'] ?? null;
            $projection = $this->estimateGoalProjection($latestGoal, $currentWeight, is_numeric($netGain) ? (float) $netGain : null);

            $result[] = [
                'id' => $id,
                'profile' => $profile,
                'status' => $recommendation['status'] ?? 'generated',
                'generated_at' => $recommendation['generated_at'] ?? null,
                'estimated_net_gain' => is_numeric($netGain) ? (float) $netGain : null,
                'estimated_weight_change_per_week_kg' => $projection['weight_change_per_week_kg'],
                'estimated_days_to_goal' => $projection['days_to_goal'],
                'estimated_target_weight_kg' => $projection['target_weight_kg'],
                'activities' => $activitiesByRecommendation[$id] ?? [],
                'diet_composition' => $generatedEntry['diet_composition'] ?? [],
                'diet_name' => $generatedEntry['diet_name'] ?? null,
                'diet_description' => $generatedEntry['diet_description'] ?? null,
            ];
        }

        return $result;
    }

    public function chooseCandidate(int $userId, int $selectedRecommendationId, array $generated, array $draft): array
    {
        $candidateIds = [];
        $selectedCandidate = null;
        foreach ($generated as $entry) {
            $id = (int) ($entry['id'] ?? 0);
            if ($id > 0) {
                $candidateIds[] = $id;
                if ($id === $selectedRecommendationId) {
                    $selectedCandidate = $entry;
                }
            }
        }

        $candidateIds = array_values(array_unique($candidateIds));
        if (empty($candidateIds) || !in_array($selectedRecommendationId, $candidateIds, true) || empty($selectedCandidate)) {
            throw new \RuntimeException('Recommandation candidate invalide.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $dietResult = $this->generateDietCompositionForCandidate($userId, $draft, $selectedCandidate);

            foreach ($candidateIds as $candidateId) {
                $status = $candidateId === $selectedRecommendationId ? 'selected' : 'discarded';

                $updateData = ['status' => $status];
                if ($candidateId === $selectedRecommendationId) {
                    $updateData['diet_id'] = $dietResult['diet_id'];
                }

                $updated = $this->recommendationModel
                    ->where('id', $candidateId)
                    ->where('user_id', $userId)
                    ->set($updateData)
                    ->update();

                if (!$updated) {
                    throw new \RuntimeException('Impossible de mettre à jour le statut de la recommandation ' . $candidateId . '.');
                }
            }

            $db->transCommit();

            return $dietResult;
        } catch (\Throwable $th) {
            $db->transRollback();
            throw $th;
        }
    }

    public function generateDietCompositionForCandidate(int $userId, array $draft, array $candidate): array
    {
        $measurement = $this->bodyMeasurementsModel->getLatestByUserId($userId);
        if (empty($measurement)) {
            throw new \RuntimeException('Aucune mesure corporelle trouvée pour cet utilisateur.');
        }

        $foodItemsById = [];
        foreach ($this->foodItemsModel->getByIds($this->extractUniqueFoodItemIds($draft)) as $item) {
            $foodItemsById[(int) $item['id']] = $item;
        }

        $targetCalories = (float) ($candidate['metrics']['total_gain'] ?? 0.0);
        if ($targetCalories <= 0) {
            throw new \RuntimeException('Impossible de déterminer l\'apport calorique cible du plan.');
        }

        $goalType = (string) ($candidate['goal_type'] ?? 'candidate');
        $profile = (string) ($candidate['profile'] ?? 'balanced');
        $targetNet = (float) ($candidate['target_net'] ?? 0.0);

        $dietName = sprintf('Plan %s - %s', ucfirst($profile), ucfirst(str_replace('_', ' ', $goalType)));
        $dietDescription = sprintf(
            'Plan journalier généré pour %s avec un apport cible de %.0f kcal et des quantités à consommer sur une journée entière.',
            $goalType,
            $targetCalories,
            $targetNet
        );

        $dietId = $this->dietsModel->insert([
            'name' => $dietName,
            'description' => $dietDescription,
        ], true);

        if (!$dietId) {
            throw new \RuntimeException('Impossible de créer le plan alimentaire.');
        }

        $composition = $this->buildDietCompositionPreview($draft, $targetCalories, $goalType);

        foreach ($draft['distributions'] as $categoryId => $percentage) {
            $this->foodDistributionsModel->insert([
                'diet_id' => $dietId,
                'category_id' => (int) $categoryId,
                'percentage' => (float) $percentage,
            ]);
        }

        foreach ($composition as $section) {
            foreach ($section['items'] as $itemRow) {
                $this->dietCompositionsModel->insert([
                    'diet_id' => $dietId,
                    'food_item_id' => (int) $itemRow['food_item_id'],
                    'quantity' => (float) $itemRow['quantity_grams'],
                ]);
            }
        }

        return [
            'diet_id' => (int) $dietId,
            'diet_name' => $dietName,
            'diet_description' => $dietDescription,
            'target_calories' => $targetCalories,
            'composition' => $composition,
        ];
    }

    private function extractProfileFromStatus(string $status): string
    {
        if (strpos($status, 'generated_') === 0) {
            return substr($status, strlen('generated_'));
        }

        return 'candidate';
    }

    private function buildDietCompositionPreview(array $draft, float $targetCalories, string $goalType): array
    {
        $foodItemsById = [];
        foreach ($this->foodItemsModel->getByIds($this->extractUniqueFoodItemIds($draft)) as $item) {
            $foodItemsById[(int) $item['id']] = $item;
        }

        $itemsByCategory = $this->groupDraftItemsByCategory($draft);
        $preview = [];

        foreach (($draft['distributions'] ?? []) as $categoryId => $percentage) {
            $categoryId = (int) $categoryId;
            $categoryBudget = $targetCalories * ((float) $percentage / 100.0);
            $itemIds = $itemsByCategory[$categoryId] ?? [];
            if (empty($itemIds)) {
                continue;
            }

            $allocated = $this->allocateCategoryCalories($itemIds, $foodItemsById, $categoryBudget, $goalType);
            $items = [];

            foreach ($allocated as $foodItemId => $categoryCalories) {
                $foodItem = $foodItemsById[$foodItemId] ?? null;
                if (!$foodItem) {
                    continue;
                }

                $caloriesPer100g = (float) ($foodItem['calories_per_100g'] ?? 0.0);
                if ($caloriesPer100g <= 0) {
                    continue;
                }

                $quantityGrams = round(($categoryCalories / $caloriesPer100g) * 100.0, 2);
                if ($quantityGrams <= 0) {
                    continue;
                }

                $items[] = [
                    'food_item_id' => $foodItemId,
                    'name' => $foodItem['name'] ?? 'Aliment',
                    'quantity_grams' => $quantityGrams,
                    'allocated_calories' => round($categoryCalories, 2),
                    'calories_per_100g' => $caloriesPer100g,
                ];
            }

            $preview[] = [
                'category_id' => $categoryId,
                'category' => $this->getFoodCategoryName($categoryId),
                'percentage' => (float) $percentage,
                'items' => $items,
            ];
        }

        return $preview;
    }

    private function groupDraftItemsByCategory(array $draft): array
    {
        $grouped = [];
        foreach (($draft['items'] ?? []) as $categoryId => $itemIds) {
            $grouped[(int) $categoryId] = array_values(array_unique(array_map('intval', (array) $itemIds)));
        }

        return $grouped;
    }

    private function allocateCategoryCalories(array $itemIds, array $foodItemsById, float $categoryBudget, string $goalDirection): array
    {
        $items = [];
        foreach ($itemIds as $itemId) {
            $item = $foodItemsById[$itemId] ?? null;
            if (!$item) {
                continue;
            }

            $calories = max(1.0, (float) ($item['calories_per_100g'] ?? 0.0));
            $weight = 1.0;

            if ($goalDirection === 'weight_loss') {
                $weight = 1.0 / $calories;
            } elseif ($goalDirection === 'weight_gain') {
                $weight = $calories;
            }

            $items[] = [
                'id' => $itemId,
                'weight' => $weight,
                'calories' => $calories,
            ];
        }

        if (empty($items)) {
            return [];
        }

        $weightSum = array_sum(array_column($items, 'weight'));
        if ($weightSum <= 0) {
            $weightSum = count($items);
        }

        $allocated = [];
        foreach ($items as $item) {
            $share = $item['weight'] / $weightSum;
            $allocated[$item['id']] = round($categoryBudget * $share, 2);
        }

        return $allocated;
    }

    private function getFoodCategoryName(int $categoryId): string
    {
        $category = (new \App\Models\FoodCategoriesModel())
            ->where('id', $categoryId)
            ->first();

        return $category['name'] ?? 'Catégorie inconnue';
    }

    private function estimateGoalProjection(?array $goal, ?float $currentWeight, ?float $netGain): array
    {
        $weightChangePerWeek = null;
        if ($netGain !== null) {
            $weightChangePerWeek = ($netGain * 7.0) / 7700.0;
        }

        if (empty($goal) || $netGain === null) {
            return [
                'weight_change_per_week_kg' => $weightChangePerWeek,
                'days_to_goal' => null,
                'target_weight_kg' => null,
            ];
        }

        $minDelta = isset($goal['min_kg']) ? (float) $goal['min_kg'] : null;
        $maxDelta = isset($goal['max_kg']) ? (float) $goal['max_kg'] : null;

        $deltaMagnitude = null;
        if ($minDelta !== null && $maxDelta !== null && $minDelta > 0 && $maxDelta > 0) {
            $deltaMagnitude = ($minDelta + $maxDelta) / 2.0;
        } elseif ($minDelta !== null && $minDelta > 0) {
            $deltaMagnitude = $minDelta;
        } elseif ($maxDelta !== null && $maxDelta > 0) {
            $deltaMagnitude = $maxDelta;
        }

        if ($deltaMagnitude === null) {
            return [
                'weight_change_per_week_kg' => $weightChangePerWeek,
                'days_to_goal' => null,
                'target_weight_kg' => null,
            ];
        }

        $goalName = strtolower((string) ($goal['goal_name'] ?? $goal['name'] ?? ''));
        if (strpos($goalName, 'perte') !== false || strpos($goalName, 'perdre') !== false) {
            $desiredDelta = -abs($deltaMagnitude);
        } elseif (strpos($goalName, 'prise') !== false || strpos($goalName, 'prendre') !== false) {
            $desiredDelta = abs($deltaMagnitude);
        } else {
            // For neutral goals (e.g. IMC ideal), follow the sign implied by net gain.
            $desiredDelta = $netGain < 0 ? -abs($deltaMagnitude) : abs($deltaMagnitude);
        }

        if (abs($desiredDelta) < 0.01) {
            return [
                'weight_change_per_week_kg' => $weightChangePerWeek,
                'days_to_goal' => 0,
                'target_weight_kg' => $currentWeight,
            ];
        }

        if (abs($netGain) < 0.01) {
            return [
                'weight_change_per_week_kg' => $weightChangePerWeek,
                'days_to_goal' => null,
                'target_weight_kg' => $currentWeight !== null ? $currentWeight + $desiredDelta : null,
            ];
        }

        $isDirectionMismatch = ($desiredDelta > 0 && $netGain < 0) || ($desiredDelta < 0 && $netGain > 0);
        if ($isDirectionMismatch) {
            return [
                'weight_change_per_week_kg' => $weightChangePerWeek,
                'days_to_goal' => null,
                'target_weight_kg' => $currentWeight !== null ? $currentWeight + $desiredDelta : null,
            ];
        }

        $daysToGoal = (int) ceil((abs($desiredDelta) * 7700.0) / abs($netGain));

        return [
            'weight_change_per_week_kg' => $weightChangePerWeek,
            'days_to_goal' => $daysToGoal,
            'target_weight_kg' => $currentWeight !== null ? $currentWeight + $desiredDelta : null,
        ];
    }
}
