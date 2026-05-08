<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations sante | Gestion Regime</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/auth/register-health.css') ?>">
</head>
<body>
    <main class="auth-page">
        <section class="auth-visual" aria-hidden="true">
            <div class="shape shape-pulse"></div>
            <div class="shape shape-balance"></div>
            <div class="metric-card">
                <span class="metric-icon"></span>
                <div>
                    <strong>IMC estime</strong>
                    <small>Calcul instantane</small>
                </div>
            </div>
        </section>

        <section class="health-card" aria-labelledby="health-title">
            <div class="brand">
                <span class="brand-mark">G</span>
                <span class="brand-name">Gestion Regime</span>
            </div>

            <div class="step-row" aria-label="Progression inscription">
                <span class="step-pill">Etape 2 sur 2</span>
                <span class="step-line"><span></span></span>
            </div>

            <div class="health-heading">
                <p class="eyebrow">Profil sante</p>
                <h1 id="health-title">Votre point de depart</h1>
                <p>Ces mesures nous aideront a proposer un accompagnement plus juste et progressif.</p>
            </div>

            <?php if (! empty($missingStep1)): ?>
                <div class="alert is-visible" role="alert">
                    Commencez par l'etape 1 avant de remplir vos informations de sante.
                    <a href="<?= base_url('register') ?>">Retour a l'inscription</a>
                </div>
            <?php else: ?>
                <div class="welcome-note">
                    Bonjour <?= esc($fullName ?? '') ?>, plus qu'une etape.
                </div>
            <?php endif; ?>

            <div class="alert" id="healthAlert" role="alert" aria-live="polite">
                Veuillez corriger les champs indiques.
            </div>

            <form class="health-form" id="healthForm" action="<?= base_url('register/health') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <div class="field-grid">
                    <div class="field">
                        <label for="height">Taille</label>
                        <div class="unit-field">
                            <input
                                type="number"
                                id="height"
                                name="height"
                                min="80"
                                max="250"
                                step="0.1"
                                placeholder="170"
                                inputmode="decimal"
                            >
                            <span>cm</span>
                        </div>
                        <small class="error-message" id="heightError"></small>
                    </div>

                    <div class="field">
                        <label for="weight">Poids</label>
                        <div class="unit-field">
                            <input
                                type="number"
                                id="weight"
                                name="weight"
                                min="20"
                                max="350"
                                step="0.1"
                                placeholder="65"
                                inputmode="decimal"
                            >
                            <span>kg</span>
                        </div>
                        <small class="error-message" id="weightError"></small>
                    </div>
                </div>

                <section class="imc-panel" aria-live="polite">
                    <div>
                        <span class="imc-label">IMC estime</span>
                        <strong id="imcValue">--</strong>
                    </div>
                    <div>
                        <span class="imc-label">Categorie</span>
                        <strong id="imcCategory">En attente</strong>
                    </div>
                </section>

                <div class="actions">
                    <a class="back-button" href="<?= base_url('register') ?>">Retour</a>
                    <button type="submit" class="finish-button" <?= ! empty($missingStep1) ? 'disabled' : '' ?>>
                        Terminer l'inscription
                    </button>
                </div>
            </form>
        </section>
    </main>

    <script src="<?= base_url('assets/js/auth/register-health.js') ?>"></script>
</body>
</html>
