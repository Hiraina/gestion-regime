<!DOCTYPE html>
<html>
<head>
    <title>Recommandations - Validation</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/recommendations.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="recommendation-page">
<div class="recommendation-shell">
    <section class="recommendation-hero">
        <div class="hero-top">
            <div>
                <div class="hero-badge"><i class="fas fa-circle-check"></i> Récapitulatif final</div>
                <h1>Vérifiez vos choix avant la génération</h1>
                <p>Relisez votre sélection avant de générer votre recommandation personnalisée.</p>
            </div>
            <div class="summary-pill">Etape 4 / 4</div>
        </div>

        <div class="progress-trace">
            <div class="progress-step active"><span>1</span><strong>Distributions</strong></div>
            <div class="progress-step active"><span>2</span><strong>Aliments</strong></div>
            <div class="progress-step active"><span>3</span><strong>Activités</strong></div>
            <div class="progress-step active"><span>4</span><strong>Validation</strong></div>
        </div>
    </section>

    <section class="wizard-panel">
        <div class="wizard-header">
            <div>
                <h2>Résumé complet</h2>
                <p>Relisez chaque bloc avant l'envoi final.</p>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="wizard-note error"><?= esc($error) ?></div>
        <?php endif; ?>

        <div class="summary-list">
            <div class="summary-card">
                <h3>1. Distributions de catégories</h3>
                <div class="summary-list">
                    <?php foreach ($summaryDistributions as $distribution): ?>
                        <div class="summary-entry">
                            <strong><?= esc($distribution['name']) ?></strong>
                            <span><?= esc(number_format((float) $distribution['percentage'], 1)) ?> %</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="summary-card">
                <h3>2. Aliments préférés</h3>
                <div class="summary-list">
                    <?php foreach ($summaryItems as $entry): ?>
                        <div class="summary-entry">
                            <strong><?= esc($entry['category']) ?></strong>
                            <ul>
                                <?php foreach ($entry['items'] as $item): ?>
                                    <li><?= esc($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="summary-card">
                <h3>3. Activités préférées</h3>
                <div class="summary-list">
                    <div class="summary-entry">
                        <strong>Activités choisies</strong>
                        <ul>
                            <?php foreach ($summaryActivities as $activity): ?>
                                <li><?= esc($activity) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <form method="post" action="<?= base_url('recommendations/submit') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="payload" value="<?= esc($payload, 'attr') ?>">

            <div class="wizard-footer">
                <p class="wizard-note" style="margin:0;">Cliquez sur "Générer" pour créer votre recommandation personnalisée.</p>
                <div class="button-row">
                    <a href="<?= base_url('recommendations/step3') ?>" class="btn-step-secondary">Retour</a>
                    <button type="submit" class="btn-step">Générer ma recommandation</button>
                </div>
            </div>
        </form>
    </section>
    </div>
</body>
</html>