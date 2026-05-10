<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>

    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
</head>
<body>

<div class="dashboard">

    <div class="sidebar">

        <h2>NutriGoal</h2>

        <a href="#">Dashboard</a>
        <a href="/profile">Mon profil</a>
        <a href="/goals">Choisir un objectif</a>
        <a href="#">Mes objectifs</a>
        <a href="#">Régimes</a>
        <a href="/profile/complete">Compléter mon profil</a>
        <a href="#">Statistiques</a>

    </div>

    <div class="main-content">

        <div class="topbar">
            <h1>Bonjour <?= session()->get('user_name') ?> 👋</h1>
            <p>Suivez votre progression nutritionnelle</p>
        </div>

        <div class="cards">

            <div class="card">
                <h3>Votre IMC</h3>
                <div class="card-value">
                    <?= esc($imc) ?>
                </div>
            </div>

            <div class="card">
                <h3>Objectif</h3>
                <div class="card-value">
                    Fitness
                </div>
            </div>

            <div class="card">
                <h3>Régime conseillé</h3>
                <div class="card-value">
                    Healthy
                </div>
            </div>

        </div>

        <a class="logout-btn" href="/logout">
            Déconnexion
        </a>

    </div>

</div>

</body>
</html>