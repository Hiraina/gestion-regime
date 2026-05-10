<!DOCTYPE html>
<html>
<head>
    <title>Choisir un objectif</title>

    <script>
        function toggleFields() {
            const goal = document.getElementById('goal').value;
            const box = document.getElementById('weight-box');

            if (goal == 1 || goal == 2) {
                box.style.display = 'block';
            } else {
                box.style.display = 'none';
            }
        }
    </script>
        <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/register.css') ?>">

</head>
<body>

<div class="goal-page-wrapper">

    <div class="goal-form-container">

        <h2>Choisir votre objectif</h2>

        <form method="POST" action="/goals/save">
            <?= csrf_field() ?>
            <label>Objectif :</label>
            <select name="goal_id" id="goal" onchange="toggleFields()" required>
                <option value="">-- choisir --</option>
                <?php foreach ($goals as $goal): ?>
                    <option value="<?= $goal->id ?>">
                        <?= $goal->name ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div id="weight-box" style="display:none;">
                <label>Poids minimum (kg)</label>
                <input type="number" step="0.1" name="min_kg">

                <label>Poids maximum (kg)</label>
                <input type="number" step="0.1" name="max_kg">
            </div>

            <button type="submit">Valider</button>

        </form>

    </div>

</div></body>
</html>