<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
</head>
<body>
    <h1>Health</h1>
    <ul>
        <li><a href="/health/metrics">Check metrics</a></li>
    </ul>

    <h1>Transactions </h1>
    <ul>
        <li><a href="/wallet/balance">Check balance</a></li>
        <li><a href="/wallet/transactions">Check transactions</a></li>
    </ul>

    <hr>
    <h1>Recommendations</h1>
    <button id="create-rec-btn">Create sample recommendation</button>
    <pre id="rec-result" style="background:#f4f4f4;padding:12px;border-radius:8px;margin-top:10px;display:none;"></pre>
    <hr>
    <script>
        document.getElementById('create-rec-btn').addEventListener('click', async () => {
            const resEl = document.getElementById('rec-result');
            resEl.style.display = 'block';
            resEl.textContent = 'Running...';

            try {
                const resp = await fetch('/test/create_recommendation');
                const data = await resp.json();
                resEl.textContent = JSON.stringify(data, null, 2);
            } catch (err) {
                resEl.textContent = 'Error: ' + err.message;
            }
        });
    </script>
    <h3>Credit</h3>
    <form action="/wallet/credit" method="post">
        <?= csrf_field() ?>
        <input type="number" name="amount" id="amount" required>
        <input type="submit" value="Crediter">
    </form>

    <hr>

    <h3>Debit</h3>
    <form action="/wallet/debit" method="post">
        <?= csrf_field() ?>
        <input type="number" name="amount" id="amount" required>
        <input type="submit" value="Debiter">
    </form>
</body>
</html>