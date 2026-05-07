<h2>Inscription - Étape 2</h2>

<form method="post" action="/register/step2">
     <?= csrf_field() ?> 
    <input type="number" step="0.01" name="height" placeholder="Taille (cm)" required><br>
    <input type="number" step="0.01" name="weight" placeholder="Poids (kg)" required><br>

    <button type="submit">Terminer inscription</button>
</form>