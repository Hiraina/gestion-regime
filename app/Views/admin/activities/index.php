<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Activites</title>
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
                <h1>Gestion des activites sportives</h1>
                <p>Ajoutez, modifiez ou supprimez les activites proposees.</p>
            </div>
            <a class="button primary" href="<?= base_url('admin/activities/create') ?>">Nouvelle activite</a>
        </header>

        <?php $errors = session()->getFlashdata('errors') ?? ($errors ?? []); ?>
        <?php $success = session()->getFlashdata('success'); ?>

        <?php if (! empty($errors)): ?>
            <div class="alert error">
                <?= esc(is_array($errors) ? implode(' ', $errors) : $errors) ?>
            </div>
        <?php endif; ?>

        <?php if (! empty($success)): ?>
            <div class="alert success">
                <?= esc($success) ?>
            </div>
        <?php endif; ?>

        <section class="card">
            <div class="card-header">
                <h2>Activites disponibles</h2>
                <span class="badge"><?= count($activities ?? []) ?> activites</span>
            </div>

            <?php if (empty($activities)): ?>
                <p class="empty-state">Aucune activite enregistree pour le moment.</p>
            <?php else: ?>
                <div class="table">
                    <div class="table-row table-head">
                        <span>Nom</span>
                        <span>Description</span>
                        <span>MET</span>
                        <span>Actions</span>
                    </div>
                    <?php foreach ($activities as $activity): ?>
                        <div class="table-row">
                            <div>
                                <strong><?= esc($activity['name']) ?></strong>
                            </div>
                            <div class="muted">
                                <?= esc($activity['description'] ?? '') ?>
                            </div>
                            <div>
                                <?= esc($activity['met_value'] ?? '—') ?>
                            </div>
                            <div class="actions">
                                <a class="button ghost" href="<?= base_url('admin/activities/' . $activity['id'] . '/edit') ?>">Modifier</a>
                                <form class="inline" action="<?= base_url('admin/activities/' . $activity['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Supprimer cette activite ?')">
                                    <?= csrf_field() ?>
                                    <button class="button danger" type="submit">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
