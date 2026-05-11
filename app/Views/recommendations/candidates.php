<!DOCTYPE html>
<html>
<head>
    <title>Plans candidats</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/recommendations.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="recommendation-page">
<div class="recommendation-shell">
    <section class="recommendation-hero">
        <div class="hero-top">
            <div>
                <div class="hero-badge"><i class="fas fa-list-check"></i> Plans générés</div>
                <h1>Choisissez le plan qui vous convient</h1>
                <p>Comparez les profils proposés, le gain net calorique estimé, puis sélectionnez votre plan préféré.</p>
            </div>
            <div class="summary-pill">Sélection finale</div>
        </div>
    </section>

    <section class="wizard-panel">
        <div class="wizard-header">
            <div>
                <h2>Plans candidats</h2>
                <p>Un seul plan sera retenu, les autres seront ignorés.</p>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="wizard-note error"><?= esc($error) ?></div>
        <?php endif; ?>

        <div class="summary-list">
            <?php foreach ($candidates as $candidate): ?>
                <div class="summary-card">
                    <h3>
                        Profil <?= esc(ucfirst($candidate['profile'] ?? 'candidate')) ?>
                        <span class="candidate-id">#<?= esc($candidate['id']) ?></span>
                    </h3>

                    <div class="summary-entry">
                        <strong>Plan alimentaire journalier</strong>
                        <p style="margin-top:8px; margin-bottom:12px; color: var(--text-light);">
                            Les quantités indiquées ci-dessous correspondent à la quantité totale à consommer sur une journée.
                            Vous pouvez ensuite les répartir sur vos repas de la journée.
                        </p>
                        <?php if (!empty($candidate['diet_composition'])): ?>
                            <?php foreach ($candidate['diet_composition'] as $section): ?>
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
                            <span>Aucun détail de repas disponible.</span>
                        <?php endif; ?>
                    </div>

                    <div class="summary-entry">
                        <strong>Gain net calorique estimé</strong>
                        <?php if ($candidate['estimated_net_gain'] !== null): ?>
                            <span><?= esc(number_format((float) $candidate['estimated_net_gain'], 2)) ?> kcal</span>
                        <?php else: ?>
                            <span>Non disponible</span>
                        <?php endif; ?>
                    </div>

                    <div class="summary-entry">
                        <strong>Perte / gain de poids estimé</strong>
                        <?php if ($candidate['estimated_weight_change_per_week_kg'] !== null): ?>
                            <?php $delta = (float) $candidate['estimated_weight_change_per_week_kg']; ?>
                            <span>
                                <?= $delta < 0 ? 'Perte' : 'Gain' ?>
                                de <?= esc(number_format(abs($delta), 2)) ?> kg / semaine
                            </span>
                        <?php else: ?>
                            <span>Non disponible</span>
                        <?php endif; ?>
                    </div>

                    <div class="summary-entry">
                        <strong>Durée estimée pour atteindre la variation de poids souhaitée</strong>
                        <?php if ($candidate['estimated_days_to_goal'] === 0): ?>
                            <span>Objectif déjà atteint</span>
                        <?php elseif ($candidate['estimated_days_to_goal'] !== null): ?>
                            <span>
                                <?= esc((int) $candidate['estimated_days_to_goal']) ?> jours
                                <?php if ($candidate['estimated_target_weight_kg'] !== null): ?>
                                    (poids estimé après variation: <?= esc(number_format((float) $candidate['estimated_target_weight_kg'], 1)) ?> kg)
                                <?php endif; ?>
                            </span>
                        <?php else: ?>
                            <span>Non disponible</span>
                        <?php endif; ?>
                    </div>

                    <div class="summary-entry">
                        <strong>Prix total estimé</strong>
                        <?php if (isset($dietPricePerDay) && $dietPricePerDay !== null): ?>
                            <?php if ($candidate['estimated_days_to_goal'] !== null): ?>
                                <?php $totalPrice = $dietPricePerDay * (int) $candidate['estimated_days_to_goal']; ?>
                                <?php if (!empty($isGold)): ?>
                                    <?php $discountedTotal = $totalPrice * (1 - (float) ($goldDiscountRate ?? 0)); ?>
                                    <span>
                                        <?= esc(number_format($totalPrice, 2)) ?> Ar
                                        (Gold: <?= esc(number_format($discountedTotal, 2)) ?> Ar)
                                    </span>
                                <?php else: ?>
                                    <span><?= esc(number_format($totalPrice, 2)) ?> Ar</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if (!empty($isGold)): ?>
                                    <?php $discountedDaily = $dietPricePerDay * (1 - (float) ($goldDiscountRate ?? 0)); ?>
                                    <span>
                                        <?= esc(number_format($dietPricePerDay, 2)) ?> Ar / jour
                                        (Gold: <?= esc(number_format($discountedDaily, 2)) ?> Ar)
                                    </span>
                                <?php else: ?>
                                    <span><?= esc(number_format($dietPricePerDay, 2)) ?> Ar / jour</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <span>Non disponible</span>
                        <?php endif; ?>
                    </div>

                    <div class="summary-entry">
                        <strong>Plan d'activités généré</strong>
                        <?php if (!empty($candidate['activities'])): ?>
                            <ul>
                                <?php foreach ($candidate['activities'] as $activity): ?>
                                    <li>
                                        <?= esc($activity['name']) ?>
                                        - <?= esc((int) ($activity['duration_minutes'] ?? 0)) ?> min
                                        <?php if (!empty($activity['frequency_per_week'])): ?>
                                            / <?= esc((int) $activity['frequency_per_week']) ?> fois/sem
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Aucun détail d'activité disponible.</p>
                        <?php endif; ?>
                    </div>

                    <?php
                        $totalPrice = null;
                        $discountedTotal = null;
                        if (isset($dietPricePerDay) && $dietPricePerDay !== null && $candidate['estimated_days_to_goal'] !== null) {
                            $totalPrice = $dietPricePerDay * (int) $candidate['estimated_days_to_goal'];
                            if (!empty($isGold)) {
                                $discountedTotal = $totalPrice * (1 - (float) ($goldDiscountRate ?? 0));
                            }
                        }
                        $chargeAmount = $discountedTotal ?? $totalPrice;
                        $canPay = true;
                        if ($chargeAmount !== null && isset($walletBalance)) {
                            $canPay = (float) $walletBalance >= (float) $chargeAmount;
                        }
                    ?>
                    <form method="post" action="<?= base_url('recommendations/choose/' . (int) $candidate['id']) ?>" style="margin-top: 16px;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-step" <?= $canPay ? '' : 'disabled' ?>>Choisir ce plan</button>
                        <?php if (!$canPay): ?>
                            <p style="margin-top: 8px; color: #dc2626;">Solde insuffisant pour payer ce plan.</p>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
</body>
</html>
