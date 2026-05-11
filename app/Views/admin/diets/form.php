<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Regime</title>
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
                <h1><?= esc($title ?? 'Regime') ?></h1>
                <p>Gerez la composition, les durees et les prix de chaque regime.</p>
            </div>
            <a class="button ghost" href="<?= base_url('admin/diets') ?>">Retour liste</a>
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
                        <label for="dietName">Nom du regime</label>
                        <input
                            id="dietName"
                            name="name"
                            type="text"
                            value="<?= esc(old('name', $diet['name'] ?? '')) ?>"
                            placeholder="Ex: Regime energie"
                            required
                        >
                    </div>
                    <div class="field">
                        <label for="dietDescription">Description</label>
                        <input
                            id="dietDescription"
                            name="description"
                            type="text"
                            value="<?= esc(old('description', $diet['description'] ?? '')) ?>"
                            placeholder="Objectif, benefices, notes"
                        >
                    </div>
                </div>
            </section>

            <section class="form-section">
                <div class="section-header">
                    <h2>Prix selon la duree</h2>
                    <button class="button ghost" type="button" id="addPriceRow">Ajouter une duree</button>
                </div>

                <div class="table" id="priceTable">
                    <div class="table-row table-head">
                        <span>Duree (jours)</span>
                        <span>Prix (Ar)</span>
                        <span></span>
                    </div>
                    <?php foreach ($pricingRows as $row): ?>
                        <div class="table-row" data-row>
                            <div>
                                <input
                                    type="number"
                                    name="duration_days[]"
                                    min="1"
                                    value="<?= esc($row['duration_days']) ?>"
                                    placeholder="30"
                                >
                            </div>
                            <div>
                                <input
                                    type="number"
                                    name="price[]"
                                    min="1"
                                    step="0.01"
                                    value="<?= esc($row['price']) ?>"
                                    placeholder="15000"
                                >
                            </div>
                            <div class="row-actions">
                                <button class="button danger" type="button" data-remove>Retirer</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="form-section">
                <h2>Composition du regime</h2>
                <p class="muted">Les pourcentages doivent totaliser 100%.</p>
                <div class="table">
                    <div class="table-row table-head">
                        <span>Categorie</span>
                        <span>Pourcentage</span>
                    </div>
                    <?php foreach ($compositionRows as $row): ?>
                        <div class="table-row">
                            <div>
                                <input
                                    type="text"
                                    name="category_name[]"
                                    value="<?= esc($row['name']) ?>"
                                    readonly
                                >
                            </div>
                            <div>
                                <input
                                    type="number"
                                    name="category_percentage[]"
                                    min="0"
                                    max="100"
                                    step="0.1"
                                    value="<?= esc($row['percentage']) ?>"
                                >
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="form-actions">
                <button class="button primary" type="submit">Enregistrer</button>
            </div>
        </form>
    </main>

    <script src="<?= base_url('assets/js/admin/diets.js') ?>"></script>
</body>
</html>
