<?php
$pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE site_infos SET adresse = ?, horaires = ?, telephone = ?, email = ? WHERE id = 1");
    $stmt->execute([
        $_POST['adresse'],
        $_POST['horaires'],
        $_POST['telephone'],
        $_POST['email']
    ]);
    $message = "✅ Informations mises à jour.";
}

$site = $pdo->query("SELECT * FROM site_infos WHERE id = 1")->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Infos du site</title>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbaradmin.php'; ?>
<div class="info">
    <h1>Informations du restaurant</h1>
    <p><strong>Adresse :</strong> <?= htmlspecialchars($site['adresse']) ?></p>
    <p><strong>Horaires :</strong> <?= nl2br(htmlspecialchars($site['horaires'])) ?></p>
    <p><strong>Téléphone :</strong> <?= htmlspecialchars($site['telephone']) ?></p>
    <p><strong>Email :</strong> <?= htmlspecialchars($site['email']) ?></p>
<h1>Modifier les informations du restaurant</h1>

<?php if ($message): ?>
    <p style="color: green;"><?= $message ?></p>
<?php endif; ?>

<form method="POST">
    <label>📍 Adresse :</label><br>
    <textarea name="adresse" required><?= htmlspecialchars($site['adresse']) ?></textarea><br><br>

    <label>🕒 Horaires :</label><br>
    <textarea name="horaires" required><?= htmlspecialchars($site['horaires']) ?></textarea><br><br>

    <label>📞 Téléphone :</label><br>
    <input type="text" name="telephone" value="<?= htmlspecialchars($site['telephone']) ?>" required><br><br>

    <label>📧 Email :</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($site['email']) ?>" required><br><br>

    <button type="submit">Enregistrer</button>
</form>
</div>
<style>
    .info {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
}

h1 {
    color: #2c3e50;
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
}

form {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

label {
    font-weight: 600;
    color: #34495e;
    display: block;
    margin-bottom: 5px;
}

textarea, input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 14px;
}

textarea {
    min-height: 100px;
    resize: vertical;
}

button {
    background-color: #3498db;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #2980b9;
}

.message {
    background-color: #dff0d8;
    color: #3c763d;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    text-align: center;
}
</style>
</body>
</html>
