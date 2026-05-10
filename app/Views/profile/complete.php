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
            Profil 
        </div>

        <h2>Complétez votre profil</h2>


<form method="post" action="/profile/complete">
    <?= csrf_field() ?>

    <div class="form-group">
        <label>Date de naissance</label>
        <input
            class="form-control"
            type="date"
            name="birth_date"
            value="<?= esc($user['birth_date'] ?? '') ?>"
            required>
    </div>

    <div class="form-group">
        <label>Numéro de téléphone</label>
        <input
            class="form-control"
            type="text"
            name="num_telephone"
            value="<?= esc($profile['num_telephone'] ?? '') ?>"
            required>
    </div>

    <div class="form-group">
        <label>Adresse</label>
        <textarea
            class="form-control"
            name="adresse"
            required><?= esc($profile['adresse'] ?? '') ?></textarea>
    </div>

    <button class="btn-register" type="submit">
        Enregistrer mon profil
    </button>
</form>
    </div>

</div>

</body>
</html>