<!DOCTYPE html>
<html>
<head>
    <title>Plan validé</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/recommendations.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="recommendation-page">
<div class="recommendation-shell">
    <section class="recommendation-hero">
        <div class="hero-top">
            <div>
                <div class="hero-badge"><i class="fas fa-check"></i> Plan validé</div>
                <h1>Votre plan recommandé</h1>
                <p>Voici le plan que vous avez validé avec toutes les informations.</p>
            </div>
            <div class="summary-pill">Plan actif</div>
        </div>
    </section>

    <section class="wizard-panel">
        <div class="wizard-header">
            <div>
                <h2><?= esc($diet['name'] ?? 'Régime sélectionné') ?></h2>
                <?php if (!empty($diet['description'])): ?>
                    <p><?= esc($diet['description']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="summary-list">
            <div class="summary-card">
                <h3>Plan alimentaire journalier</h3>
                <?php if (!empty($composition)): ?>
                    <?php foreach ($composition as $section): ?>
                        <div style="margin-top: 12px;">
                            <strong><?= esc($section['category']) ?></strong>
                            <ul>
                                <?php foreach ($section['items'] as $item): ?>
                                    <li>
                                        <?= esc($item['name']) ?>
                                        - <?= esc(number_format((float) $item['quantity_grams'], 0)) ?> g / jour
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune composition disponible.</p>
                <?php endif; ?>
            </div>

            <div class="summary-card">
                <h3>Plan d'activités</h3>
                <?php if (!empty($activities)): ?>
                    <ul>
                        <?php foreach ($activities as $activity): ?>
                            <li>
                                <?= esc($activity['activity_name'] ?? 'Activité') ?>
                                - <?= esc((int) ($activity['duration_minutes'] ?? 0)) ?> min
                                <?php if (!empty($activity['frequency_per_week'])): ?>
                                    / <?= esc((int) $activity['frequency_per_week']) ?> fois/sem
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucune activité disponible.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="distribution-footer">
            <div class="button-row">
                <a href="<?= base_url('dashboard') ?>" class="btn-step-secondary">Retour au tableau de bord</a>
            </div>
        </div>
    </section>
</div>
</body>
</html>
