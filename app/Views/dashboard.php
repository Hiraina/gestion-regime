<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - NutriGoal</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
      <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta name="csrf-header" content="<?= csrf_header() ?>">
</head>
<body>
<div class="dashboard">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-leaf"></i> NutriGoal
        </div>
        <nav class="sidebar-nav">
            <a href="<?= base_url('dashboard') ?>" class="active">
                <i class="fas fa-th-large"></i> Tableau de bord
            </a>
            <a href="<?= base_url('profile') ?>">
                <i class="fas fa-user"></i> Mon profil
            </a>
            <a href="<?= base_url('regimes') ?>">
                <i class="fas fa-utensils"></i> Régimes
            </a>
            <a href="<?= base_url('profile/complete') ?>">
                <i class="fas fa-id-card"></i> Compléter profil
            </a>
            <a href="<?= base_url('statistics') ?>">
                <i class="fas fa-chart-bar"></i> Statistiques
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= base_url('logout') ?>" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">

        <!-- Top bar -->
        <div class="topbar">
            <div class="greeting">
                <h1>Bonjour <?= esc(session()->get('user_name')) ?> 👋</h1>
                <p>Suivez votre progression nutritionnelle</p>
            </div>
            <div class="wallet-zone">
                <div class="wallet-balance" id="wallet-display">
                    <i class="fas fa-wallet"></i>
                    <span class="balance-amount"><?= esc($balance ?? '0.00') ?> €</span>
                </div>
                <button class="btn-icon" id="open-code-modal" title="Entrer un code">
                    <i class="fas fa-ticket-alt"></i> Code
                </button>
            </div>
        </div>

        <!-- Métrique santé -->
        <div class="health-snapshot">
            <div class="health-card">
                <div class="health-icon">
                    <i class="fas fa-weight-scale"></i>
                </div>
                <div class="health-info">
                    <span class="health-label">Votre IMC</span>
                    <span class="health-value"><?= esc(number_format($imc, 1)) ?></span>
                    <span class="health-category">(<?= esc($category ?? '') ?>)</span>
                </div>
            </div>
            <!-- On peut ajouter BMR ici si nécessaire -->
        </div>


        <!-- Objectif actuel -->
        <?php if (isset($current_goal) && $current_goal): ?>
<!-- Objectif actuel + Lien Recommandations -->
<div class="current-goal-wrapper">
    <?php if (isset($current_goal) && $current_goal): ?>
    <div class="current-goal">
        <div class="current-goal-main">
            <div class="current-goal-header">
                <i class="fas fa-bullseye"></i> Votre objectif actuel
            </div>
            <div class="current-goal-body">
                <strong><?= esc($current_goal->goal_name) ?></strong>
                <?php if ($current_goal->min_kg && $current_goal->max_kg): ?>
                    <span class="weight-range">
                        Poids cible : <?= esc($current_goal->min_kg) ?> – <?= esc($current_goal->max_kg) ?> kg
                    </span>
                <?php endif; ?>
                <span class="goal-date">Depuis le <?= esc(date('d/m/Y', strtotime($current_goal->start_date))) ?></span>
            </div>
        </div>
        <a href="<?= base_url('recommendations') ?>" class="recommendations-link">
            <i class="fas fa-clipboard-list"></i> Voir régimes & activités recommandés
        </a>
    </div>
    <?php else: ?>
    <div class="current-goal empty">
        <p>Aucun objectif défini pour le moment.</p>
        <a href="#" class="recommendations-link disabled" onclick="return false;">
            <i class="fas fa-clipboard-list"></i> Voir régimes & activités recommandés
        </a>
    </div>
    <?php endif; ?>
</div>
        <?php else: ?>
        <div class="current-goal empty">
            <p>Aucun objectif défini pour le moment.</p>
        </div>
        <?php endif; ?>








        <!-- Section Objectifs -->
        <div class="goals-section">
            <h2 class="section-title">Définir mon objectif</h2>

            <!-- Message de retour (succès/erreur) -->
            <div id="goal-message" class="goal-message" style="display:none;"></div>

            <!-- Les trois cartes d'objectif -->
            <div class="goals-cards">
                <!-- 1. Augmenter son poids -->
                <div class="goal-card" data-goal="1" data-goal-name="Augmenter son poids">
                    <div class="goal-card-icon" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <h3>Augmenter son poids</h3>
                    <p>Prenez du volume et développez votre masse musculaire.</p>
                    <div class="goal-card-extra" style="display:none;">
                        <div class="input-group">
                            <label>Poids minimum (kg)</label>
                            <input type="number" step="0.1" name="min_kg" placeholder="Ex: 70">
                        </div>
                        <div class="input-group">
                            <label>Poids maximum (kg)</label>
                            <input type="number" step="0.1" name="max_kg" placeholder="Ex: 75">
                        </div>
                        <button class="btn btn-primary btn-validate">Valider</button>
                    </div>
                </div>

                <!-- 2. Réduire son poids -->
                <div class="goal-card" data-goal="2" data-goal-name="Réduire son poids">
                    <div class="goal-card-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <h3>Réduire son poids</h3>
                    <p>Perdez de la masse grasse et affinez votre silhouette.</p>
                    <div class="goal-card-extra" style="display:none;">
                        <div class="input-group">
                            <label>Poids minimum (kg)</label>
                            <input type="number" step="0.1" name="min_kg" placeholder="Ex: 60">
                        </div>
                        <div class="input-group">
                            <label>Poids maximum (kg)</label>
                            <input type="number" step="0.1" name="max_kg" placeholder="Ex: 65">
                        </div>
                        <button class="btn btn-primary btn-validate">Valider</button>
                    </div>
                </div>

                <!-- 3. Atteindre IMC idéal -->
                <div class="goal-card" data-goal="3" data-goal-name="Atteindre IMC idéal">
                    <div class="goal-card-icon" style="background: linear-gradient(135deg, #22c55e, #16a34a);">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Atteindre IMC idéal</h3>
                    <p>Objectif santé, sans fourchette de poids spécifique.</p>
                    <div class="goal-card-extra" style="display:none;">
                        <p class="imc-info">L’IMC idéal se situe entre 18.5 et 24.9. Nous calculerons la durée nécessaire pour l’atteindre.</p>
                        <button class="btn btn-primary btn-validate">Valider</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section régimes / recommandations (peut être affichée après validation) -->
    </main>
</div>

<!-- MODALE CODE PROMO (inchangée) -->
...
<?php
$securityConfig = config('Security');
$csrfCookieName = $securityConfig->cookieName;   // "csrf_cookie_name"
$csrfHeaderName = $securityConfig->headerName;   // "X-CSRF-TOKEN"
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Helper pour lire un cookie par son nom
    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? decodeURIComponent(match[2]) : null;
    }

    const modal = document.getElementById('code-modal');
    const openBtn = document.getElementById('open-code-modal');
    const closeBtn = document.getElementById('close-modal');
    const form = document.getElementById('code-form');
    const codeInput = document.getElementById('code-input');
    const messageDiv = document.getElementById('code-message');
    const balanceDisplay = document.querySelector('.balance-amount');

    // Ouvrir / fermer modale
    openBtn.addEventListener('click', () => {
        modal.classList.add('active');
        codeInput.value = '';
        messageDiv.innerHTML = '';
        codeInput.focus();
    });

    closeBtn.addEventListener('click', () => modal.classList.remove('active'));
    window.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('active');
    });

    // Soumission AJAX
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const code = codeInput.value.trim();
        if (!code) return;

        messageDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validation...';
        messageDiv.className = 'message info';

        // Récupère le token frais depuis le cookie CSRF
        const csrfHash = getCookie('<?= $csrfCookieName ?>');
        const csrfHeader = '<?= $csrfHeaderName ?>';

        try {
            const response = await fetch('<?= base_url('codes/redeem') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeader]: csrfHash
                },
                body: JSON.stringify({ code: code })
            });

            const data = await response.json();

            if (data.success) {
                balanceDisplay.textContent = `${data.new_balance} €`;
                messageDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${data.message}`;
                messageDiv.className = 'message success';
                codeInput.value = '';
            } else {
                messageDiv.innerHTML = `<i class="fas fa-times-circle"></i> ${data.message}`;
                messageDiv.className = 'message error';
            }
        } catch (error) {
            messageDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Erreur réseau.';
            messageDiv.className = 'message error';
        }
    });
});



// Gestion de la sélection d'objectif
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.goal-card');
    const goalMessage = document.getElementById('goal-message');

    // Helper pour lire un cookie
    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? decodeURIComponent(match[2]) : null;
    }

    // Gestion clic cartes
    cards.forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
            cards.forEach(c => {
                if (c !== this) {
                    c.classList.remove('selected');
                    c.querySelector('.goal-card-extra').style.display = 'none';
                }
            });
            this.classList.toggle('selected');
            const extra = this.querySelector('.goal-card-extra');
            extra.style.display = this.classList.contains('selected') ? 'block' : 'none';
        });
    });

    // Validation objectif
    document.querySelectorAll('.btn-validate').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.stopPropagation();
            const card = this.closest('.goal-card');
            const goalId = card.getAttribute('data-goal');
            const minKg = card.querySelector('[name="min_kg"]')?.value || null;
            const maxKg = card.querySelector('[name="max_kg"]')?.value || null;

            if ((goalId == 1 || goalId == 2) && (!minKg || !maxKg)) {
                showMessage('Veuillez remplir les deux champs de poids.', 'error');
                return;
            }

            try {
                // Récupération du token CSRF depuis le cookie
                const csrfCookieName = '<?= $csrfCookieName ?>';
                const csrfHash = getCookie(csrfCookieName);
                const csrfHeader = '<?= $csrfHeaderName ?>';

                const response = await fetch('<?= base_url('goals/save') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        [csrfHeader]: csrfHash
                    },
                    body: JSON.stringify({ goal_id: goalId, min_kg: minKg, max_kg: maxKg })
                });

                const data = await response.json();
 if (data.success) {
    showMessage(data.message, 'success');
    if (data.goal) updateCurrentGoalUI(data.goal);
} else {                   showMessage(data.message, 'error');
                }
            } catch (error) {
                showMessage('Erreur réseau.', 'error');
            }
        });
    });

    function showMessage(msg, type) {
        goalMessage.textContent = msg;
        goalMessage.className = `goal-message ${type}`;
        goalMessage.style.display = 'block';
        setTimeout(() => { goalMessage.style.display = 'none'; }, 4000);
    }
});





function updateCurrentGoalUI(goal) {
    const container = document.querySelector('.current-goal-wrapper');
    if (!container) return;

    container.innerHTML = `
        <div class="current-goal">
            <div class="current-goal-main">
                <div class="current-goal-header">
                    <i class="fas fa-bullseye"></i> Votre objectif actuel
                </div>
                <div class="current-goal-body">
                    <strong>${goal.goal_name}</strong>
                    ${goal.min_kg && goal.max_kg ? 
                        `<span class="weight-range">Poids cible : ${goal.min_kg} – ${goal.max_kg} kg</span>` : ''}
                    <span class="goal-date">Depuis le ${new Date(goal.start_date).toLocaleDateString('fr-FR')}</span>
                </div>
            </div>
            <a href="<?= base_url('recommendations') ?>" class="recommendations-link">
                <i class="fas fa-clipboard-list"></i> Voir régimes & activités recommandés
            </a>
        </div>
    `;
}
</script>





</body>
</html>