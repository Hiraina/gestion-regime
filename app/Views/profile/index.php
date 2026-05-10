<!DOCTYPE html>
<html>
<head>
    <title>Mon profil</title>

    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
</head>
<body>

<div class="profile-container">

    <h2 class="profile-title">👤 Mon profil nutritionnel</h2>

    <?php if (!$profile): ?>

        <p style="color:#ef4444;">
            Aucun profil complété.
        </p>

        <div class="profile-actions">
            <a class="btn btn-primary" href="/profile/complete">
                Compléter mon profil
            </a>
        </div>

    <?php else: ?>

        <div class="profile-item">
            <span class="profile-label">Âge</span>
            <span class="profile-value"><?= esc($profile['age']) ?></span>
        </div>

        <div class="profile-item">
            <span class="profile-label">Activité</span>
            <span class="profile-value"><?= esc($profile['activity_level']) ?></span>
        </div>

        <div class="profile-item">
            <span class="profile-label">Objectif</span>
            <span class="profile-value"><?= esc($profile['objective']) ?></span>
        </div>

        <div class="profile-item">
            <span class="profile-label">Régime</span>
            <span class="profile-value"><?= esc($profile['diet_type']) ?></span>
        </div>

        <div class="profile-item">
            <span class="profile-label">Allergies</span>
            <span class="profile-value"><?= esc($profile['allergies'] ?: 'Aucune') ?></span>
        </div>

        <div class="profile-actions">
            <a class="btn btn-secondary" href="/profile/complete">
                Modifier
            </a>

            <a class="btn btn-primary" href="/dashboard">
                Retour dashboard
            </a>
        </div>

    <?php endif; ?>

</div>

</body>
</html>