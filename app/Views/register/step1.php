<h2>Inscription - Étape 1</h2>

<form method="post" action="/register/step1">

    <?= csrf_field() ?> <!-- 🔥 IMPORTANT -->

    <input type="text" name="name" placeholder="Nom" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Mot de passe" required><br>

    <select name="gender_id" required>
        <option value="1">Homme</option>
        <option value="2">Femme</option>
    </select>

    <button type="submit">Suivant</button>
</form>