<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | Gestion Regime</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/auth/login.css') ?>">
</head>
<body>
    <main class="auth-page">
        <section class="auth-visual" aria-hidden="true">
            <div class="orb orb-green"></div>
            <div class="orb orb-coral"></div>
            <div class="nutrition-plate">
                <div class="plate-ring">
                    <span class="food food-leaf"></span>
                    <span class="food food-grain"></span>
                    <span class="food food-protein"></span>
                    <span class="food food-fruit"></span>
                </div>
            </div>
        </section>

        <section class="login-card" aria-labelledby="login-title">
            <div class="brand">
                <span class="brand-mark">G</span>
                <span class="brand-name">Gestion Regime</span>
            </div>

            <div class="login-heading">
                <p class="eyebrow">Espace utilisateur</p>
                <h1 id="login-title">Connexion</h1>
                <p>Accedez a vos recommandations alimentaires et suivez votre objectif sante.</p>
            </div>

            <div class="alert" id="loginAlert" role="alert" aria-live="polite">
                Veuillez corriger les champs indiques.
            </div>

            <form class="login-form" id="loginForm" action="#" method="post" novalidate>
                <div class="field">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="exemple@email.com"
                        autocomplete="email"
                    >
                    <small class="error-message" id="emailError"></small>
                </div>

                <div class="field">
                    <label for="password">Mot de passe</label>
                    <div class="password-field">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Votre mot de passe"
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password" id="togglePassword" aria-label="Afficher le mot de passe">
                            Voir
                        </button>
                    </div>
                    <small class="error-message" id="passwordError"></small>
                </div>

                <button type="submit" class="login-button">Connexion</button>
            </form>

            <p class="register-link">
                Pas encore de compte ?
                <a href="<?= base_url('register') ?>">Creer un compte</a>
            </p>
        </section>
    </main>

    <script src="<?= base_url('assets/js/auth/login.js') ?>"></script>
</body>
</html>
        </div>

        <div class="auth-title">
            <h2>Connexion</h2>
            <span>Accédez à votre espace santé</span>
        </div>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="error-message">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/login">

            <?= csrf_field() ?>

            <div class="form-group">
                <label>Email</label>
                <input class="form-control" type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input class="form-control" type="password" name="password" required>
            </div>

            <button class="btn" type="submit">
                Se connecter
            </button>

        </form>

    </div>

</div>

</body>
</html>
>>>>>>> a7a2f66c47bcc0f6dd053a94ebb859d5db647e31
