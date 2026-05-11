<!DOCTYPE html>
<html>
<head>
    <title>Test - Gestion des regimes</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
</head>
<body>

<div class="profile-container">
    <h2 class="profile-title">Test - Creer un regime</h2>

    <?php if (!empty($error)): ?>
        <p style="color:#dc2626; margin-bottom: 12px;"><?= esc($error) ?></p>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <p style="color:#16a34a; margin-bottom: 12px;"><?= esc($success) ?></p>
    <?php endif; ?>

    <form method="post" action="<?= base_url('diets/create-test') ?>">
        <?= csrf_field() ?>

        <div class="profile-item">
            <span class="profile-label">Nom</span>
            <input type="text" name="name" required style="width:60%; padding:10px; border-radius:8px; border:1px solid #e2e8f0;">
        </div>

        <div class="profile-item">
            <span class="profile-label">Prix par jour</span>
            <input type="number" step="0.01" min="0" name="price_per_day" required style="width:60%; padding:10px; border-radius:8px; border:1px solid #e2e8f0;">
        </div>

        <div style="margin-top: 16px;">
            <h3 style="margin-bottom: 8px;">Distributions par categorie (%)</h3>
            <?php foreach ($categories as $category): ?>
                <div class="profile-item" style="border-bottom: none;">
                    <span class="profile-label"><?= esc($category['name']) ?></span>
                    <input
                        type="number"
                        step="0.1"
                        min="0"
                        max="100"
                        name="distributions[<?= esc($category['id']) ?>]"
                        value="0"
                        style="width:60%; padding:10px; border-radius:8px; border:1px solid #e2e8f0;"
                    >
                </div>
            <?php endforeach; ?>
        </div>

        <div class="profile-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
            <a class="btn btn-secondary" href="<?= base_url('dashboard') ?>">Retour dashboard</a>
        </div>
    </form>
</div>

<div class="profile-container" style="margin-top: 24px;">
    <h2 class="profile-title">Test - Regimes existants</h2>

    <?php if (empty($diets)): ?>
        <p>Aucun regime enregistre.</p>
    <?php else: ?>
        <?php foreach ($diets as $diet): ?>
            <div class="profile-item" style="align-items:flex-start;">
                <div>
                    <strong><?= esc($diet['name']) ?></strong><br>
                    <small>Prix/jour: <?= esc(number_format($diet['price_per_day'], 2)) ?> Ar</small>
                    <?php if (!empty($diet['categories'])): ?>
                        <div style="margin-top:6px; color:#475569;">
                            <?php foreach ($diet['categories'] as $index => $category): ?>
                                <?= esc($category['category']) ?> (<?= esc($category['percentage']) ?>%)<?= $index < count($diet['categories']) - 1 ? ' · ' : '' ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="margin-top:6px; color:#475569;">Aucune distribution.</div>
                    <?php endif; ?>
                </div>
                <form method="post" action="<?= base_url('diets/delete-test/' . $diet['id']) ?>" onsubmit="return confirm('Supprimer ce regime ?');">
                    <?= csrf_field() ?>
                    <button class="btn btn-secondary" type="submit">Supprimer</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>