<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Activite</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/diets.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <main class="admin-page">
        <header class="admin-header">
            <div>
                <p class="eyebrow">Back office</p>
                <h1><?= esc($title ?? 'Activite') ?></h1>
                <p>Definissez les activites pour vos recommandations.</p>
            </div>
            <a class="button ghost" href="<?= base_url('admin/activities') ?>">Retour liste</a>
        </header>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>
        <?php if (! empty($errors)): ?>
            <div class="alert error">
                <?= esc(is_array($errors) ? implode(' ', $errors) : $errors) ?>
            </div>
        <?php endif; ?>

        <form class="card form" action="<?= esc($action) ?>" method="post">
            <?= csrf_field() ?>

            <section class="form-section">
                <h2>Informations principales</h2>
                <div class="field-grid">
                    <div class="field">
                        <label for="activityName">Nom de l'activite</label>
                        <input
                            id="activityName"
                            name="name"
                            type="text"
                            value="<?= esc(old('name', $activity['name'] ?? '')) ?>"
                            placeholder="Ex: Cardio intensif"
                            required
                        >
                    </div>
                    <div class="field">
                        <label for="activityMet">Valeur MET</label>
                        <input
                            id="activityMet"
                            name="met_value"
                            type="number"
                            step="0.01"
                            min="0"
                            value="<?= esc(old('met_value', $activity['met_value'] ?? '')) ?>"
                            placeholder="Ex: 6.5"
                        >
                    </div>
                    <div class="field">
                        <label for="activityDescription">Description</label>
                        <input
                            id="activityDescription"
                            name="description"
                            type="text"
                            value="<?= esc(old('description', $activity['description'] ?? '')) ?>"
                            placeholder="Objectif, intensite, recommandations"
                        >
                    </div>
                </div>
            </section>

            <div class="form-actions">
                <button class="button primary" type="submit">Enregistrer</button>
            </div>
        </form>
    </main>
</body>
</html>
