
<?php
session_start();

// Vérification si le client est connecté
if (!isset($_SESSION['client_id'])) {
    header('Location: connexionclient.php');
    exit();
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Récupération des informations du client
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$_SESSION['client_id']]);
    $client = $stmt->fetch();

    $message = '';
    $error = '';

    // Traitement du formulaire de mise à jour
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $nom = trim($_POST['nom']);
            $prenom = trim($_POST['prenom']);
            $email = trim($_POST['email']);
            $telephone = trim($_POST['telephone']);
            $current_password = trim($_POST['current_password']);
            $new_password = trim($_POST['new_password']);
            
            // Vérification si l'email existe déjà pour un autre client
            if ($email !== $client['email']) {
                $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = ? AND id != ?");
                $stmt->execute([$email, $_SESSION['client_id']]);
                if ($stmt->fetch()) {
                    throw new Exception("Cette adresse email est déjà utilisée");
                }
            }

            // Si un nouveau mot de passe est fourni
            if (!empty($new_password)) {
                if (!password_verify($current_password, $client['password'])) {
                    throw new Exception("Le mot de passe actuel est incorrect");
                }
                if (strlen($new_password) < 6) {
                    throw new Exception("Le nouveau mot de passe doit contenir au moins 6 caractères");
                }
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("UPDATE clients SET nom = ?, prenom = ?, email = ?, telephone = ?, password = ? WHERE id = ?");
                $stmt->execute([$nom, $prenom, $email, $telephone, $password_hash, $_SESSION['client_id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE clients SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE id = ?");
                $stmt->execute([$nom, $prenom, $email, $telephone, $_SESSION['client_id']]);
            }

            $_SESSION['client_nom'] = $nom;
            $_SESSION['client_prenom'] = $prenom;
            $message = "✅ Vos informations ont été mises à jour avec succès";
            
            // Rafraîchir les informations du client
            $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
            $stmt->execute([$_SESSION['client_id']]);
            $client = $stmt->fetch();

        } catch(Exception $e) {
            $error = $e->getMessage();
        }
    }

} catch(PDOException $e) {
    $error = "Erreur de connexion à la base de données";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte</title>
    <link rel="stylesheet" href="css/mon-compte.css">
</head>

<body>

  <?php include __DIR__ . '/../includes/navbar.php'; ?>
   
    <div class="container">
        <div class="account-header">
            <h1>Mon Compte</h1>
            <p>Bienvenue, <?= htmlspecialchars($client['prenom']) ?> <?= htmlspecialchars($client['nom']) ?></p>
        </div>

        <?php if ($message): ?>
            <div class="success-message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="account-form">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($client['nom']) ?>" required>
            </div>

            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($client['prenom']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($client['telephone']) ?>" required>
            </div>

            <div class="password-section">
                <h3>Modifier le mot de passe</h3>
                <p class="hint">Laissez vide pour conserver le mot de passe actuel</p>

                <div class="form-group">
                    <label for="current_password">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password">
                </div>

                <div class="form-group">
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" minlength="6">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit">Enregistrer les modifications</button>
                <a href="logout.php" class="btn-logout">Se déconnecter</a>
            </div>
        </form>
    </div>
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f5f5;
    margin: 0;
    padding: 20px;
}



.account-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #3498db;
}

.account-header h1 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.account-header p {
    color: #7f8c8d;
    margin: 0;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #34495e;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    box-sizing: border-box;
}

.form-group input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.password-section {
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.password-section h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.hint {
    color: #7f8c8d;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

button {
    flex: 1;
    padding: 1rem;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #2980b9;
}

.btn-logout {
    flex: 1;
    padding: 1rem;
    background-color: #e74c3c;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-logout:hover {
    background-color: #c0392b;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
    text-align: center;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
    text-align: center;
}



</style>
  <?php include __DIR__ . '/../includes/footer.php'; ?>


  
</body>
</html>