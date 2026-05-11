<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription | Gestion Regime</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/auth/register-step1.css') ?>">
</head>
<body>
    <main class="auth-page">
        <section class="auth-visual" aria-hidden="true">
            <div class="shape shape-leaf"></div>
            <div class="shape shape-sun"></div>
            <div class="health-card">
                <span class="health-icon"></span>
                <div>
                    <strong>Profil sante</strong>
                    <small>Etape 1 sur 2</small>
                </div>
            </div>
        </section>

        <section class="register-card" aria-labelledby="register-title">
            <div class="brand">
                <span class="brand-mark">G</span>
                <span class="brand-name">Gestion Regime</span>
            </div>

            <div class="step-row" aria-label="Progression inscription">
                <span class="step-pill">Etape 1 sur 2</span>
                <span class="step-line"><span></span></span>
            </div>

            <div class="register-heading">
                <p class="eyebrow">Creation du compte</p>
                <h1 id="register-title">Commencons par vous connaitre</h1>
                <p>Quelques informations suffisent pour preparer un profil adapte a vos objectifs nutritionnels.</p>
            </div>

            <div class="alert" id="registerAlert" role="alert" aria-live="polite">
                Veuillez corriger les champs indiques.
            </div>

            <form
                class="register-form"
                id="registerForm"
                action="<?= base_url('register/step1') ?>"
                data-next-url="<?= base_url('register/health') ?>"
                method="post"
                novalidate
            >
                <?= csrf_field() ?>

                <div class="field">
                    <label for="fullName">Nom complet</label>
                    <input
                        type="text"
                        id="fullName"
                        name="full_name"
                        placeholder="Votre nom complet"
                        autocomplete="name"
                    >
                    <small class="error-message" id="fullNameError"></small>
                </div>

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
                    <label for="gender">Genre</label>
                    <select id="gender" name="gender">
                        <option value="">Choisir un genre</option>
                        <option value="homme">Homme</option>
                        <option value="femme">Femme</option>
                    </select>
                    <small class="error-message" id="genderError"></small>
                </div>

                <div class="field">
                    <label for="dateOfBirth">Date de naissance</label>
                    <input
                        type="date"
                        id="dateOfBirth"
                        name="date_of_birth"
                        autocomplete="bday"
                    >
                    <small class="error-message" id="dateOfBirthError"></small>
                </div>

                <div class="field">
                    <label for="password">Mot de passe</label>
                    <div class="password-field">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Au moins 6 caracteres"
                            autocomplete="new-password"
                        >
                        <button type="button" class="toggle-password" data-target="password" aria-label="Afficher le mot de passe">
                            Voir
                        </button>
                    </div>
                    <small class="error-message" id="passwordError"></small>
                </div>

                <div class="field">
                    <label for="passwordConfirm">Confirmation mot de passe</label>
                    <div class="password-field">
                        <input
                            type="password"
                            id="passwordConfirm"
                            name="password_confirm"
                            placeholder="Confirmer le mot de passe"
                            autocomplete="new-password"
                        >
                        <button type="button" class="toggle-password" data-target="passwordConfirm" aria-label="Afficher la confirmation du mot de passe">
                            Voir
                        </button>
                    </div>
                    <small class="error-message" id="passwordConfirmError"></small>
                </div>

                <button type="submit" class="next-button">Suivant</button>
            </form>

            <p class="login-link">
                Deja un compte ?
                <a href="<?= base_url('login') ?>">Se connecter</a>
            </p>
        </section>
    </main>

    <script src="<?= base_url('assets/js/auth/register-step1.js') ?>"></script>
</body>
</html>
