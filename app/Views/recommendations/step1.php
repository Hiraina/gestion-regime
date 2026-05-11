<!DOCTYPE html>
<html>
<head>
    <title>Recommandations - Etape 1</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/recommendations.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="recommendation-page">
<div class="recommendation-shell">
    <section class="recommendation-hero">
        <div class="hero-top">
            <div>
                <div class="hero-badge"><i class="fas fa-clipboard-list"></i> Recommandation guidée</div>
                <h1>Choisissez un regime</h1>
                <p>Sélectionnez un regime existant pour démarrer votre recommandation.</p>
            </div>
            <div class="summary-pill">Etape 1 / 4</div>
        </div>

        <div class="progress-trace">
            <div class="progress-step active"><span>1</span><strong>Regime</strong></div>
            <div class="progress-step"><span>2</span><strong>Aliments</strong></div>
            <div class="progress-step"><span>3</span><strong>Activités</strong></div>
            <div class="progress-step"><span>4</span><strong>Validation</strong></div>
        </div>
    </section>

    <section class="wizard-panel">
        <div class="wizard-header">
            <div>
                <h2>Choisir un regime</h2>
                <p>Chaque regime affiche ses categories non nulles et leurs pourcentages.</p>
            </div>
            <div class="limit-pill">Etape 1 sur 4</div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="wizard-note error"><?= esc($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('recommendations/step1') ?>">
            <?= csrf_field() ?>

            <div class="distribution-grid">
                <?php if (empty($dietOptions)): ?>
                    <div class="wizard-note error">Aucun regime disponible.</div>
                <?php else: ?>
                    <?php foreach ($dietOptions as $diet): ?>
                        <label class="distribution-card" style="cursor: pointer;">
                            <div style="display:flex; align-items:flex-start; gap:12px;">
                                <input
                                    type="radio"
                                    name="diet_id"
                                    value="<?= esc($diet['id']) ?>"
                                    <?= ((int) ($draft['diet_id'] ?? 0) === (int) $diet['id']) ? 'checked' : '' ?>
                                >
                                <div>
                                    <h3 style="margin-bottom:6px;"><?= esc($diet['name']) ?></h3>
                                    <?php if (!empty($diet['categories'])): ?>
                                        <p>
                                            <?php foreach ($diet['categories'] as $index => $category): ?>
                                                <?= esc($category['category']) ?> (<?= esc($category['percentage']) ?>%)<?= $index < count($diet['categories']) - 1 ? ' · ' : '' ?>
                                            <?php endforeach; ?>
                                        </p>
                                    <?php else: ?>
                                        <p>Aucune categorie non nulle definie.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="distribution-footer">
                <p class="wizard-note" style="margin:0;">Le regime selectionne servira de base aux aliments.</p>
                <div class="button-row">
                    <a href="<?= base_url('recommendations/clear') ?>" class="btn-step-secondary">Retour au tableau de bord</a>
                    <button type="submit" class="btn-step">Continuer</button>
                </div>
            </div>
        </form>
    </section>
    </div>
</body>
</html>