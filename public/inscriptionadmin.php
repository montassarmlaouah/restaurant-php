<?php
session_start();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Validation des données
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        if (strlen($password) < 6) {
            throw new Exception("Le mot de passe doit contenir au moins 6 caractères");
        }

        if ($password !== $confirm_password) {
            throw new Exception("Les mots de passe ne correspondent pas");
        }

        // Vérifier si le nom d'utilisateur existe déjà
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            throw new Exception("Ce nom d'utilisateur est déjà utilisé");
        }

        // Hasher le mot de passe
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insérer le nouvel admin
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password_hash]);

        $_SESSION['success'] = "Compte admin créé avec succès ! Vous pouvez maintenant vous connecter.";
        header('Location: connexionadmin.php');
        exit();

    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #d18b00ff;
            --secondary-blue: #e09840ff;
            --accent-blue: #d19148ff;
            --dark-blue: #b28120ff;
            --light-blue: #f73500ff;
            --gradient-blue: linear-gradient(135deg, #d16500ff 0%, rgba(252, 151, 0, 1) 100%);
            --gradient-bg: linear-gradient(135deg, #e08d40ff 0%, #d18f00ff 100%);
        }
        body {
            background: var(--gradient-bg);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        .auth-card {
            background: white;
            border-radius: 25px;
            box-shadow: 0 25px 70px rgba(0, 206, 209, 0.3);
            overflow: hidden;
            border: 2px solid rgba(0, 206, 209, 0.2);
            max-width: 900px;
            width: 100%;
            display: flex;
            flex-wrap: wrap;
        }
        .auth-left {
            background: var(--gradient-blue);
            color: white;
            padding: 3rem 2rem;
            flex: 1 1 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .auth-right {
            padding: 3rem 2rem;
            background: rgba(255,255,255,0.98);
            flex: 2 1 400px;
        }
        .logo {
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 1.2rem;
            text-shadow: 0 3px 15px rgba(0,0,0,0.2);
        }
        .logo i {
            color: rgba(255,255,255,0.95);
            margin-right: 0.5rem;
        }
        .features-list {
            list-style: none;
            padding: 0;
            margin-top: 2rem;
        }
        .features-list li {
            padding: 0.7rem 0;
            position: relative;
            padding-left: 2.5rem;
            font-size: 1.1rem;
        }
        .features-list li::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            left: 0;
            color: rgba(255,255,255,0.9);
            background: rgba(255,255,255,0.2);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }
        .form-control {
            border-radius: 15px;
            padding: 1rem 1.25rem;
            border: 2px solid var(--light-blue);
            background: rgba(255,255,255,0.95);
            margin-bottom: 1rem;
            color: #222 !important;
        }
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.3rem rgba(0, 206, 209, 0.25);
            background: white;
            color: #222 !important;
        }
        .input-group-text {
            background: rgba(175,238,238,0.8);
            border: 2px solid var(--light-blue);
            color: var(--primary-blue);
        }
        .btn-primary {
            background: var(--gradient-blue);
            border: none;
            border-radius: 15px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 6px 20px rgba(0, 206, 209, 0.4);
            color: white;
            width: 100%;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, var(--dark-blue), var(--primary-blue));
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 206, 209, 0.6);
            color: white;
        }
        .alert {
            border-radius: 15px;
            border: none;
            font-weight: 500;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .alert-danger {
            background: linear-gradient(45deg, #ff6b6b, #ff5252);
            color: white;
            border: none;
        }
        .alert-success {
            background: linear-gradient(45deg, #4caf50, #45a049);
            color: white;
            border: none;
        }
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }
        .login-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        @media (max-width: 900px) {
            .auth-card {
                flex-direction: column;
                padding: 0;
            }
            .auth-left, .auth-right {
                padding: 2rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-left">
                <div class="logo">
                    <i class="fas fa-user-shield"></i> Administration
                </div>
                <h3 class="mb-4">Bienvenue !</h3>
                <p class="mb-4">Créez votre compte administrateur pour gérer le site.</p>
                <ul class="features-list">
                    <li>Accès sécurisé à l'administration</li>
                    <li>Gestion des commandes et réservations</li>
                    <li>Mise à jour du menu</li>
                    <li>Suivi de l'activité du site</li>
                </ul>
            </div>
            <div class="auth-right">
                <h2 class="mb-3 fw-bold" style="color:var(--primary-blue);">Créer un compte admin</h2>
                <p class="text-muted mb-4">Remplissez les informations pour vous inscrire</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Nom d'utilisateur</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label fw-semibold">Confirmer le mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">
                        <i class="fas fa-user-plus me-2"></i>
                        S'inscrire
                    </button>
                </form>
                <p class="login-link">Déjà inscrit ? <a href="connexionadmin.php">Connectez-vous ici</a></p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>