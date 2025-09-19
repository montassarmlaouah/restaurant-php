<?php
session_start();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $telephone = trim($_POST['telephone']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        if (strlen($password) < 8) {
            throw new Exception("Le mot de passe doit contenir au moins 8 caractères");
        }
        if ($password !== $confirm_password) {
            throw new Exception("Les mots de passe ne correspondent pas");
        }
        $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception("Cette adresse email est déjà utilisée");
        }
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Correction ici : le champ est mot_de_passe dans la base
        $stmt = $pdo->prepare("INSERT INTO clients (nom, prenom, email, telephone, mot_de_passe) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $telephone, $password_hash]);
        $_SESSION['success'] = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
        header('Location: connexionclient.php');
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
    <title>Inscription Client</title>
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
            color: #222 !important; /* Texte en noir */
        }
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.3rem rgba(0, 206, 209, 0.25);
            background: white;
            color: #222 !important; /* Texte en noir même au focus */
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
        .step {
            display: none;
        }
        .step.active {
            display: block;
        }
        .step-btns {
            display: flex;
            gap: 10px;
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
                <p class="mb-4">Créez votre compte client et profitez de nos services.</p>
                <ul class="features-list">
                    <li>Inscription rapide et sécurisée</li>
                    <li>Accès à la commande en ligne</li>
                    <li>Réservations simplifiées</li>
                    <li>Support client dédié</li>
                </ul>
            </div>
            <div class="auth-right">
                <h2 class="mb-3 fw-bold" style="color:var(--primary-blue);">Créer un compte</h2>
                <p class="text-muted mb-4">Remplissez vos informations pour commencer</p>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST" autocomplete="off" id="inscriptionForm">
                    <!-- Étape 1 -->
                    <div class="step active" id="step1">
                        <div class="mb-3">
                            <label for="nom" class="form-label fw-semibold">Nom</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label fw-semibold">Prénom</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="step-btns">
                            <button type="button" class="btn btn-primary" onclick="nextStep()">Continuer</button>
                        </div>
                    </div>
                    <!-- Étape 2 -->
                    <div class="step" id="step2">
                        <div class="mb-3">
                            <label for="telephone" class="form-label fw-semibold">Téléphone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="telephone" name="telephone" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label fw-semibold">Confirmer le mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        <div class="step-btns">
                            <button type="button" class="btn btn-outline-secondary" onclick="prevStep()">Retour</button>
                            <button type="submit" class="btn btn-primary">S'inscrire</button>
                        </div>
                    </div>
                </form>
                <p class="login-link">Déjà inscrit ? <a href="connexionclient.php">Connectez-vous ici</a></p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        function nextStep() {
            let nom = document.getElementById('nom').value.trim();
            let prenom = document.getElementById('prenom').value.trim();
            let email = document.getElementById('email').value.trim();
            if (!nom || !prenom || !email ) {
                alert('Veuillez remplir tous les champs.');
                return;
            }
            document.getElementById('step1').classList.remove('active');
            document.getElementById('step2').classList.add('active');
        }
        function prevStep() {
            document.getElementById('step2').classList.remove('active');
            document.getElementById('step1').classList.add('active');
        }
    </script>
</body>
</html>