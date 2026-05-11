<!DOCTYPE html>
<html>
<head>
    <title>Passer Gold</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-header" content="<?= csrf_header() ?>">
</head>
<body>

<div class="profile-container">
    <h2 class="profile-title">Offre Gold</h2>

    <div class="profile-item">
        <span class="profile-label">Statut actuel</span>
        <span class="profile-value">
            <?= $isGold ? 'Gold actif' : 'Standard' ?>
        </span>
    </div>

    <div class="profile-item">
        <span class="profile-label">Reduction Gold</span>
        <span class="profile-value"><?= esc((int) ($discountRate * 100)) ?>%</span>
    </div>

    <div class="profile-item">
        <span class="profile-label">Prix de l'upgrade</span>
        <span class="profile-value"><?= esc(number_format($upgradePrice, 2)) ?> EUR</span>
    </div>

    <div class="profile-item">
        <span class="profile-label">Solde portefeuille</span>
        <span class="profile-value" id="gold-balance"><?= esc(number_format($balance ?? 0, 2)) ?> EUR</span>
    </div>

    <?php $canUpgrade = !$isGold && (float) ($balance ?? 0) >= (float) $upgradePrice; ?>
    <div class="profile-actions">
        <?php if (!$isGold): ?>
            <button class="btn btn-primary" id="gold-upgrade-btn" type="button" <?= $canUpgrade ? '' : 'disabled' ?>>Passer Gold</button>
        <?php else: ?>
            <button class="btn btn-secondary" type="button" disabled>Gold deja actif</button>
        <?php endif; ?>
        <a class="btn btn-secondary" href="<?= base_url('dashboard') ?>">Retour dashboard</a>
    </div>

    <?php if (!$isGold && !$canUpgrade): ?>
        <p style="color:#dc2626; margin-top: 8px;">Solde insuffisant pour passer Gold.</p>
    <?php endif; ?>

    <p id="gold-message" style="margin-top: 12px;"></p>
</div>

<div class="profile-container" style="margin-top: 20px;">
    <h2 class="profile-title">Recharger avec un code</h2>

    <form id="code-form">
        <?= csrf_field() ?>
        <div class="profile-item" style="border-bottom: none;">
            <span class="profile-label">Code de recharge</span>
            <input type="text" name="code" id="code-input" placeholder="Ex: ABC123" style="width: 60%; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
        </div>
        <div class="profile-actions">
            <button class="btn btn-primary" type="submit">Valider le code</button>
        </div>
    </form>

    <p id="code-message" style="margin-top: 12px;"></p>
</div>

<script>
const csrfHeader = document.querySelector('meta[name="csrf-header"]').getAttribute('content');
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const goldBtn = document.getElementById('gold-upgrade-btn');
const goldMessage = document.getElementById('gold-message');
const balanceEl = document.getElementById('gold-balance');

if (goldBtn) {
    goldBtn.addEventListener('click', async () => {
        goldMessage.textContent = '';
        const response = await fetch("<?= base_url('gold/purchase') ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                [csrfHeader]: csrfToken
            },
            body: JSON.stringify({})
        });

        const data = await response.json();
        if (data.response === 'success') {
            goldMessage.style.color = '#16a34a';
            goldMessage.textContent = 'Upgrade reussi. Bienvenue Gold !';
            if (data.result && balanceEl) {
                balanceEl.textContent = Number(data.result.new_balance).toFixed(2) + ' EUR';
            }
            goldBtn.disabled = true;
            goldBtn.textContent = 'Gold deja actif';
        } else {
            goldMessage.style.color = '#dc2626';
            goldMessage.textContent = data.message || 'Echec de l\'upgrade.';
        }
    });
}

const codeForm = document.getElementById('code-form');
const codeInput = document.getElementById('code-input');
const codeMessage = document.getElementById('code-message');

codeForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    codeMessage.textContent = '';
    const payload = { code: codeInput.value };

    const response = await fetch("<?= base_url('codes/redeem') ?>", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            [csrfHeader]: csrfToken
        },
        body: JSON.stringify(payload)
    });

    const data = await response.json();
    if (data.success) {
        codeMessage.style.color = '#16a34a';
        codeMessage.textContent = data.message || 'Code valide.';
        if (balanceEl && data.new_balance) {
            balanceEl.textContent = data.new_balance + ' EUR';
        }
    } else {
        codeMessage.style.color = '#dc2626';
        codeMessage.textContent = data.message || 'Code invalide.';
    }
});
</script>

</body>
</html>
