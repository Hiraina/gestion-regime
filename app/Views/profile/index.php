<!DOCTYPE html>
<html>
<head>
    <title>Mon profil</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
</head>
<body>

<div class="profile-container">

    <h2 class="profile-title">
        👤 Profil de <?= esc($user['name'] ?? session()->get('user_name')) ?>
    </h2>

    <!-- Nom -->
    <div class="profile-item">
        <span class="profile-label">Nom</span>
        <span class="profile-value"><?= esc($user['name'] ?? session()->get('user_name')) ?></span>
    </div>

    <!-- Âge -->
    <div class="profile-item">
        <span class="profile-label">Âge</span>
        <span class="profile-value">
            <?= isset($age) ? esc($age) . ' ans' : 'Non renseigné' ?>
        </span>
    </div>

    <!-- Date de naissance -->
    <div class="profile-item">
        <span class="profile-label">Date de naissance</span>
        <span class="profile-value">
            <?= !empty($user['birth_date']) ? esc($user['birth_date']) : 'Non renseignée' ?>
        </span>
    </div>

    <!-- Taille -->
    <div class="profile-item">
        <span class="profile-label">Taille</span>
        <span class="profile-value">
            <?= isset($measurement['height']) ? esc($measurement['height']) . ' cm' : 'Non renseignée' ?>
        </span>
    </div>

    <!-- Poids -->
    <div class="profile-item">
        <span class="profile-label">Poids</span>
        <span class="profile-value">
            <?= isset($measurement['weight']) ? esc($measurement['weight']) . ' kg' : 'Non renseigné' ?>
        </span>
    </div>

    <!-- IMC -->
    <div class="profile-item">
        <span class="profile-label">IMC</span>
        <span class="profile-value"><?= esc($imc ?? 'Non calculé') ?></span>
    </div>

    <!-- Téléphone -->
    <div class="profile-item">
        <span class="profile-label">Numéro de téléphone</span>
        <span class="profile-value">
            <?= !empty($profile['num_telephone']) ? esc($profile['num_telephone']) : 'Non renseigné' ?>
        </span>
    </div>

    <!-- Adresse -->
    <div class="profile-item">
        <span class="profile-label">Adresse</span>
        <span class="profile-value">
            <?= !empty($profile['adresse']) ? esc($profile['adresse']) : 'Non renseignée' ?>
        </span>
    </div>

    <!-- Message si le profil (téléphone + adresse) n'est pas complet -->
    <?php if (!$profile): ?>
        <p style="color:#f59e0b;">
            ⚠️ Certaines informations (téléphone, adresse) ne sont pas encore renseignées.
        </p>
    <?php endif; ?>

    <!-- Boutons d'action -->
    <div class="profile-actions">
        <?php if ($profile): ?>
            <a class="btn btn-secondary" href="/profile/complete">Modifier</a>
        <?php else: ?>
            <a class="btn btn-primary" href="/profile/complete">Compléter mon profil</a>
        <?php endif; ?>
        <a class="btn btn-primary" href="/dashboard">Retour dashboard</a>
    </div>

</div>

</body>
</html>