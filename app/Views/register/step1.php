<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>

    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/register.css') ?>">
</head>
<body>

<div class="register-wrapper">

    <div class="register-card">

        <div class="step-badge">
            Étape 1 / 2
        </div>

        <h2>Créer un compte</h2>

        <p class="register-subtitle">
            Commencez votre suivi nutritionnel personnalisé
        </p>

        <form method="post" action="/register/step1">

            <?= csrf_field() ?>

            <div class="form-group">
                <label>Nom complet</label>
                <input class="form-control" type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input class="form-control" type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input class="form-control" type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Genre</label>

                <select class="form-control" name="gender_id" required>
                    <?php foreach ($genders as $gender): ?>
                        <option value="<?= $gender['id'] ?>">
                            <?= esc($gender['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="btn-register" type="submit">
                Continuer
            </button>

        </form>

    </div>

</div>

</body>
</html>