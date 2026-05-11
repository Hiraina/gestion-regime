<!DOCTYPE html>
<html>
<head>
    <title>Recommandations - Etape 2</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/recommendations.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="recommendation-page">
<div class="recommendation-shell">
    <section class="recommendation-hero">
        <div class="hero-top">
            <div>
                <div class="hero-badge"><i class="fas fa-carrot"></i> Choix des aliments</div>
                <h1>Sélectionnez vos aliments préférés par catégorie</h1>
                <p>Indiquez vos aliments préférés dans chaque catégorie active. Ces aliments seront utilisés comme base pour générer votre recommandation.</p>
            </div>
            <div class="summary-pill">Etape 2 / 4</div>
        </div>

        <div class="progress-trace">
            <div class="progress-step active"><span>1</span><strong>Distributions</strong></div>
            <div class="progress-step active"><span>2</span><strong>Aliments</strong></div>
            <div class="progress-step"><span>3</span><strong>Activités</strong></div>
            <div class="progress-step"><span>4</span><strong>Validation</strong></div>
        </div>
    </section>

    <section class="wizard-panel">
        <div class="wizard-header">
            <div>
                <h2>Aliments préférés</h2>
                <p>Sélectionnez vos préférences alimentaires. Chaque catégorie activée doit avoir au moins un aliment.</p>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="wizard-note error"><?= esc($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('recommendations/step2') ?>" id="items-form">
            <?= csrf_field() ?>

            <?php if (empty($activeCategories)): ?>
                <div class="wizard-note">Aucune catégorie active n'a été trouvée. Revenez à l'étape précédente.</div>
            <?php endif; ?>

            <?php foreach ($activeCategories as $category): ?>
                <div class="category-card" data-category-card>
                    <div class="category-meta">
                        <div>
                            <h3><?= esc($category['name']) ?></h3>
                            <p>Sélectionnez vos aliments préférés dans cette catégorie.</p>
                        </div>
                        <div class="category-badge">
                            <?= esc(number_format($draft['distributions'][$category['id']] ?? 0, 1)) ?>%
                        </div>
                    </div>

                    <div class="items-grid">
                        <?php foreach (($itemsByCategory[$category['id']] ?? []) as $item): ?>
                            <?php $checked = in_array($item['id'], $draft['items'][$category['id']] ?? [], false); ?>
                            <label class="item-check">
                                <input
                                    type="checkbox"
                                    name="items[<?= esc($category['id']) ?>][]"
                                    value="<?= esc($item['id']) ?>"
                                    <?= $checked ? 'checked' : '' ?>
                                >
                                <span>
                                    <strong><?= esc($item['name']) ?></strong>
                                    <?= esc(number_format((float) $item['calories_per_100g'], 0)) ?> kcal / 100 g
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="distribution-footer">
                <p class="wizard-note" id="items-warning" style="margin:0;">Chaque catégorie active doit avoir au moins un aliment préféré sélectionné.</p>
                <div class="button-row">
                    <a href="<?= base_url('recommendations/step1') ?>" class="btn-step-secondary">Retour</a>
                    <button type="submit" class="btn-step" id="items-submit">Continuer</button>
                </div>
            </div>
        </form>
    </section>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('items-form');
    const submitButton = document.getElementById('items-submit');
    const warning = document.getElementById('items-warning');
    const cards = Array.from(document.querySelectorAll('[data-category-card]'));

    const validate = () => {
        const missing = cards.some((card) => !card.querySelector('input[type="checkbox"]:checked'));

        if (missing) {
            warning.textContent = 'Sélectionnez au moins un aliment préféré dans chaque catégorie active.';
            warning.classList.add('error');
            submitButton.disabled = true;
        } else {
            warning.textContent = 'Vos préférences alimentaires sont prêtes. Prêt à continuer.';
            warning.classList.remove('error');
            submitButton.disabled = false;
        }
    };

    cards.forEach((card) => card.addEventListener('change', validate));
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