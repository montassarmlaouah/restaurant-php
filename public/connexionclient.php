<?php

session_start();


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        $client = $stmt->fetch();

        // Correction ici : mot_de_passe
        if ($client && password_verify($_POST['password'], $client['mot_de_passe'])) {
            $_SESSION['client_id'] = $client['id'];
            $_SESSION['client_nom'] = $client['nom'];
            $_SESSION['client_prenom'] = $client['prenom'];
            
            header('Location:index.php');
            exit();
        } else {
            $error = 'Email ou mot de passe incorrect';
        }
    } catch(PDOException $e) {
        $error = "Erreur de connexion à la base de données";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Client</title>
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
                    <i class="fas fa-utensils"></i> Elwad
                </div>
                <h3 class="mb-4">Bienvenue chez Elwad !</h3>
                <p class="mb-4">Connectez-vous pour accéder à votre espace client.</p>
                <ul class="features-list">
                    <li>Connexion rapide et sécurisée</li>
                    <li>Accès à la commande en ligne</li>
                    <li>Réservations simplifiées</li>
                    <li>Support client dédié</li>
                </ul>
            </div>
            <div class="auth-right">
                <h2 class="mb-3 fw-bold" style="color:var(--primary-blue);">Connexion Client</h2>
                <p class="text-muted mb-4">Entrez vos identifiants pour continuer</p>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="votre@email.com" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Se connecter
                    </button>
                </form>
                <p class="login-link">Pas encore de compte ? <a href="inscriptionclient.php">Inscrivez-vous ici</a></p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.querySelector('.fa-eye, .fa-eye-slash');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>