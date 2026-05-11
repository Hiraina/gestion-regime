<!DOCTYPE html>
<html>
<head>
    <title>Informations physiques</title>

    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/register.css') ?>">
</head>
<body>

<div class="register-wrapper">

    <div class="register-card">

        <div class="step-badge">
            Étape 2 / 2
        </div>

        <h2>Profil physique</h2>

        <p class="register-subtitle">
            Ces informations serviront à calculer votre IMC
        </p>

        <form method="post" action="/register/step2">

            <?= csrf_field() ?>

            <div class="form-group">
                <label>Taille (cm)</label>
                <input class="form-control"
                       type="number"
                       step="0.01"
                       name="height"
                       required>
            </div>

            <div class="form-group">
                <label>Poids (kg)</label>
                <input class="form-control"
                       type="number"
                       step="0.01"
                       name="weight"
                       required>
            </div>

            <button class="btn-register" type="submit">
                Terminer l'inscription
            </button>

        </form>

    </div>

</div>

</body>
</html>