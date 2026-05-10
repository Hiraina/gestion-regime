<!DOCTYPE html>
<html>
<head>
    <title>Compléter mon profil</title>

    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/register.css') ?>">
</head>
<body>

<div class="register-wrapper">

    <div class="register-card">

        <div class="step-badge">
            Profil nutritionnel
        </div>

        <h2>Complétez votre profil</h2>

        <p class="register-subtitle">
            Ces informations permettront de générer un régime adapté
        </p>

        <form method="post" action="/profile/complete">

            <?= csrf_field() ?>

            <div class="form-group">
                <label>Âge</label>
                <input
                    class="form-control"
                    type="number"
                    name="age"
                    required>
            </div>

            <div class="form-group">
                <label>Niveau d'activité</label>

                <select
                    class="form-control"
                    name="activity_level"
                    required>

                    <option value="sedentaire">
                        Sédentaire
                    </option>

                    <option value="leger">
                        Léger
                    </option>

                    <option value="modere">
                        Modéré
                    </option>

                    <option value="intense">
                        Intense
                    </option>

                </select>
            </div>

            <div class="form-group">
                <label>Objectif</label>

                <select
                    class="form-control"
                    name="objective"
                    required>

                    <option value="perte_poids">
                        Perte de poids
                    </option>

                    <option value="maintien">
                        Maintien
                    </option>

                    <option value="prise_masse">
                        Prise de masse
                    </option>

                </select>
            </div>

            <div class="form-group">
                <label>Type de régime préféré</label>

                <select
                    class="form-control"
                    name="diet_type">

                    <option value="standard">
                        Standard
                    </option>

                    <option value="vegetarien">
                        Végétarien
                    </option>

                    <option value="vegan">
                        Vegan
                    </option>

                    <option value="keto">
                        Keto
                    </option>

                    <option value="sans_gluten">
                        Sans gluten
                    </option>

                </select>
            </div>

            <div class="form-group">
                <label>Allergies alimentaires</label>

                <textarea
                    name="allergies"
                    placeholder="Ex: arachides, lactose..."></textarea>
            </div>

            <button
                class="btn-register"
                type="submit">

                Enregistrer mon profil

            </button>

        </form>

    </div>

</div>

</body>
</html>