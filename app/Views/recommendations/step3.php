<!DOCTYPE html>
<html>
<head>
    <title>Recommandations - Etape 3</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/recommendations.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="recommendation-page">
<div class="recommendation-shell">
    <section class="recommendation-hero">
        <div class="hero-top">
            <div>
                <div class="hero-badge"><i class="fas fa-dumbbell"></i> Activités préférées</div>
                <h1>Choisissez les activités à recommander</h1>
                <p>Sélectionnez les activités que vous préférez pratiquer avec votre futur programme.</p>
            </div>
            <div class="summary-pill">Etape 3 / 4</div>
        </div>

        <div class="progress-trace">
            <div class="progress-step active"><span>1</span><strong>Distributions</strong></div>
            <div class="progress-step active"><span>2</span><strong>Aliments</strong></div>
            <div class="progress-step active"><span>3</span><strong>Activités</strong></div>
            <div class="progress-step"><span>4</span><strong>Validation</strong></div>
        </div>
    </section>

    <section class="wizard-panel">
        <div class="wizard-header">
            <div>
                <h2>Activités à privilégier</h2>
                <p>Vous pouvez sélectionner plusieurs activités selon vos préférences.</p>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="wizard-note error"><?= esc($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('recommendations/step3') ?>" id="activities-form">
            <?= csrf_field() ?>

            <div class="activity-grid">
                <?php foreach ($activities as $activity): ?>
                    <label class="activity-card">
                        <div class="activity-badge">
                            <input
                                type="checkbox"
                                name="activities[]"
                                value="<?= esc($activity['id']) ?>"
                                <?= in_array($activity['id'], $draft['activities'] ?? [], false) ? 'checked' : '' ?>
                            >
                            Activité recommandée
                        </div>
                        <h3><?= esc($activity['name']) ?></h3>
                        <p><?= esc($activity['description'] ?: 'Aucune description disponible.') ?></p>
                        <div style="margin-top:12px; color: var(--text-light); font-size: 14px;">
                            MET: <?= esc(number_format((float) $activity['met_value'], 2)) ?>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="distribution-footer">
                <p class="wizard-note" id="activities-warning" style="margin:0;">Choisissez au moins une activité préférée.</p>
                <div class="button-row">
                    <a href="<?= base_url('recommendations/step2') ?>" class="btn-step-secondary">Retour</a>
                    <button type="submit" class="btn-step" id="activities-submit">Continuer</button>
                </div>
            </div>
        </form>
    </section>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('activities-form');
    const submitButton = document.getElementById('activities-submit');
    const warning = document.getElementById('activities-warning');
    const validate = () => {
        const checked = form.querySelectorAll('input[type="checkbox"]:checked').length;

        if (!checked) {
            warning.textContent = 'Choisissez au moins une activité préférée.';
            warning.classList.add('error');
            submitButton.disabled = true;
        } else {
            warning.textContent = 'Vous pouvez maintenant passer au récapitulatif.';
            warning.classList.remove('error');
            submitButton.disabled = false;
        }
    };

    form.addEventListener('change', validate);
    form.addEventListener('submit', (event) => {
        validate();
        if (submitButton.disabled) {
            event.preventDefault();
        }
    });

    validate();
});
</script>
</body>
</html>