<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Connexion</h2>

<?php if (session()->getFlashdata('error')) : ?>
    <p style="color:red;">
        <?= session()->getFlashdata('error') ?>
    </p>
<?php endif; ?>

<form method="post" action="/login">

    <?= csrf_field() ?> <!-- 🔥 obligatoire CI4 -->

    <input type="email" name="email" placeholder="Email" required><br><br>

    <input type="password" name="password" placeholder="Mot de passe" required><br><br>

    <button type="submit">Se connecter</button>

</form>

</body>
</html>