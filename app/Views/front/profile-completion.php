<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completer mon profil | Gestion Regime</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/front/profile-completion.css') ?>">
</head>
<body>
    <main class="profile-page">
        <section class="profile-visual" aria-hidden="true">
            <div class="shape shape-green"></div>
            <div class="shape shape-peach"></div>
            <div class="mini-card">
                <span class="mini-icon"></span>
                <div>
                    <strong>Profil personnalise</strong>
                    <small>Objectifs + habitudes</small>
                </div>
            </div>
        </section>

        <section class="profile-card" aria-labelledby="profile-title">
            <div class="brand">
                <span class="brand-mark">G</span>
                <span class="brand-name">Gestion Regime</span>
            </div>

            <div class="profile-heading">
                <p class="eyebrow">Completion du profil</p>
                <h1 id="profile-title">Personnalisez votre parcours</h1>
                <p>Quelques choix simples permettent de mieux adapter vos regimes, activites et recommandations.</p>
            </div>

            <div class="alert" id="profileAlert" role="alert" aria-live="polite">
                Veuillez selectionner les informations obligatoires.
            </div>

            <form class="profile-form" id="profileForm" action="<?= base_url('profile/complete') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <fieldset class="choice-group" data-required="true">
                    <legend>Objectif principal</legend>
                    <div class="choice-grid">
                        <label class="choice-card">
                            <input type="radio" name="main_goal" value="prise_poids">
                            <span class="choice-icon">+</span>
                            <strong>Prise de poids</strong>
                            <small>Construire une progression saine.</small>
                        </label>

                        <label class="choice-card">
                            <input type="radio" name="main_goal" value="perte_poids">
                            <span class="choice-icon">-</span>
                            <strong>Perte de poids</strong>
                            <small>Avancer avec un rythme durable.</small>
                        </label>

                        <label class="choice-card">
                            <input type="radio" name="main_goal" value="imc_ideal">
                            <span class="choice-icon">=</span>
                            <strong>IMC ideal</strong>
                            <small>Se rapprocher de votre zone cible.</small>
                        </label>
                    </div>
                    <small class="error-message" id="mainGoalError"></small>
                </fieldset>

                <fieldset class="choice-group" data-required="true">
                    <legend>Niveau d'activite physique</legend>
                    <div class="choice-grid compact">
                        <label class="choice-card">
                            <input type="radio" name="activity_level" value="faible">
                            <span class="choice-icon">1</span>
                            <strong>Faible</strong>
                        </label>

                        <label class="choice-card">
                            <input type="radio" name="activity_level" value="modere">
                            <span class="choice-icon">2</span>
                            <strong>Modere</strong>
                        </label>

                        <label class="choice-card">
                            <input type="radio" name="activity_level" value="intense">
                            <span class="choice-icon">3</span>
                            <strong>Intense</strong>
                        </label>
                    </div>
                    <small class="error-message" id="activityLevelError"></small>
                </fieldset>

                <div class="field">
                    <label for="foodHabit">Habitudes alimentaires</label>
                    <select id="foodHabit" name="food_habit">
                        <option value="">Choisir une habitude</option>
                        <option value="equilibre">Equilibre</option>
                        <option value="riche_viande">Riche en viande</option>
                        <option value="riche_poisson">Riche en poisson</option>
                        <option value="vegetarien">Vegetarien</option>
                        <option value="fast_food">Fast-food frequent</option>
                    </select>
                    <small class="error-message" id="foodHabitError"></small>
                </div>

                <div class="field">
                    <label for="targetWeight">Poids cible <span>optionnel</span></label>
                    <div class="unit-field">
                        <input
                            type="number"
                            id="targetWeight"
                            name="target_weight"
                            min="20"
                            max="350"
                            step="0.1"
                            placeholder="Ex: 68"
                            inputmode="decimal"
                        >
                        <span>kg</span>
                    </div>
                    <small class="error-message" id="targetWeightError"></small>
                </div>

                <button type="submit" class="finish-button">Terminer mon profil</button>
            </form>
        </section>
    </main>

    <script src="<?= base_url('assets/js/front/profile-completion.js') ?>"></script>
</body>
</html>
