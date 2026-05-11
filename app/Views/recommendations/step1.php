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
                <h1>Choisissez la distribution de vos catégories alimentaires</h1>
                <p>Répartissez les pourcentages par catégorie. Le total ne doit jamais dépasser 100 %.</p>
            </div>
            <div class="summary-pill">Etape 1 / 4</div>
        </div>

        <div class="progress-trace">
            <div class="progress-step active"><span>1</span><strong>Distributions</strong></div>
            <div class="progress-step"><span>2</span><strong>Aliments</strong></div>
            <div class="progress-step"><span>3</span><strong>Activités</strong></div>
            <div class="progress-step"><span>4</span><strong>Validation</strong></div>
        </div>
    </section>

    <section class="wizard-panel">
        <div class="wizard-header">
            <div>
                <h2>Répartir les catégories</h2>
                <p>Ne saisissez que les catégories que vous voulez inclure dans la recommandation.</p>
            </div>
            <div class="limit-pill">Total: <span id="distribution-total">0</span>% / 100%</div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="wizard-note error"><?= esc($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('recommendations/step1') ?>" id="distribution-form">
            <?= csrf_field() ?>

            <div class="distribution-grid">
                <?php foreach ($categories as $category): ?>
                    <?php $value = $draft['distributions'][$category['id']] ?? ''; ?>
                    <div class="distribution-card">
                        <h3><?= esc($category['name']) ?></h3>
                        <p>Attribuez le pourcentage de cette catégorie dans la future recommandation.</p>
                        <div class="distribution-input">
                            <label for="distribution-<?= esc($category['id']) ?>">Pourcentage</label>
                            <input
                                id="distribution-<?= esc($category['id']) ?>"
                                type="number"
                                min="0"
                                max="100"
                                step="0.1"
                                name="distributions[<?= esc($category['id']) ?>]"
                                value="<?= esc($value) ?>"
                                data-distribution-input
                            >
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="distribution-footer">
                <p class="wizard-note" id="distribution-warning" style="margin:0;">Le total doit rester inférieur ou égal à 100 %.</p>
                <div class="button-row">
                    <a href="<?= base_url('recommendations/clear') ?>" class="btn-step-secondary">Retour au tableau de bord</a>
                    <button type="submit" class="btn-step" id="distribution-submit">Continuer</button>
                </div>
            </div>
        </form>
    </section>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputs = Array.from(document.querySelectorAll('[data-distribution-input]'));
    const totalLabel = document.getElementById('distribution-total');
    const warning = document.getElementById('distribution-warning');
    const submitButton = document.getElementById('distribution-submit');
    const form = document.getElementById('distribution-form');

    const updateTotal = () => {
        const total = inputs.reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
        totalLabel.textContent = total.toFixed(1).replace(/\.0$/, '');

        if (total !== 100) {
            if (total > 100) {
                warning.textContent = 'Le total dépasse 100 %. Le total doit être exactement 100 %.';
            } else if (total < 100) {
                warning.textContent = 'Le total est inférieur à 100 %. Il doit être exactement 100 %.';
            } else {
                warning.textContent = 'Le total doit être exactement 100 %.';
            }
            warning.classList.add('error');
            submitButton.disabled = true;
        } else {
            warning.textContent = 'Total exact: 100 %. Prêt à continuer.';
            warning.classList.remove('error');
            submitButton.disabled = false;
        }
    };

    inputs.forEach((input) => input.addEventListener('input', updateTotal));
    form.addEventListener('submit', (event) => {
        updateTotal();
        if (submitButton.disabled) {
            event.preventDefault();
        }
    });

    updateTotal();
});
</script>
</body>
</html>