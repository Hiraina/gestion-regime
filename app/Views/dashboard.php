<h1>Dashboard</h1>

<p>Bienvenue <?= session()->get('user_name') ?> IMC = <?= esc($imc) ?></p>

<a href="/logout">Déconnexion</a>