<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>

    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body>

<div class="auth-container">

    <div class="auth-card">

        <div class="logo">
            <h1>NutriGoal</h1>
            <p>Votre régime adapté à vos objectifs</p>
        </div>

        <div class="auth-title">
            <h2>Connexion</h2>
            <span>Accédez à votre espace santé</span>
        </div>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="error-message">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/login">

            <?= csrf_field() ?>

            <div class="form-group">
                <label>Email</label>
                <input class="form-control" type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input class="form-control" type="password" name="password" required>
            </div>

            <button class="btn" type="submit">
                Se connecter
            </button>

        </form>

    </div>

</div>

</body>
</html>