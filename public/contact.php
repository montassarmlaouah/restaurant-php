<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=restaurant;charset=utf8', 'root', '');

// Récupération des infos du site
$site = $pdo->query("SELECT * FROM site_infos WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

// Récupération des catégories (images populaires)
$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des informations du site
try {
} catch (Exception $e) {


}

$erreur = '';
$succes = '';

// Traitement du formulaire de contact
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($nom) || empty($email) || empty($message)) {
            throw new Exception("Veuillez remplir tous les champs correctement");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Adresse email invalide");
        }

        $stmt = $pdo->prepare("INSERT INTO contacts (nom, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $email, $message]);
        $succes = "Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.";

        // Vider le formulaire après envoi
        $_POST = [];
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact </title>
    <link rel="icon" href="images/logo1.png" sizes="200x128" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .contact-section {
            padding: 60px 0;
        }

        .contact-info {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .contact-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .contact-icon {
            width: 40px;
            height: 40px;
            background-color: #e7f1ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .contact-icon i {
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .contact-text {
            color: #666;
        }

        .contact-text strong {
            display: block;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .contact-text small {
            color: #888;
            display: block;
            margin-top: 3px;
        }

        .contact-form {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        .form-control {
            border: 1px solid #dee2e6;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .btn-send {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-send:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
        }

        .btn-send i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .contact-section {
                padding: 40px 0;
            }
            .contact-form {
                margin-top: 30px;
            }
        }
           .navbar-brand img {
            height: 50px;
        }
        .navbar {
            padding: 10px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .nav-link i {
            font-size: 1.1rem;
        }
        .nav-link:hover {
            color: rgb(237, 240, 246);
        }
        .btn-rdv {
            background-color: #0d6efd;
            color: white;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 50px;
            border: none;
            transition: all 0.3s;
        }
        .btn-rdv:hover {
            background-color: rgb(254, 254, 255);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px #0d6efd;
        }
        .hero {
            background-color: #f8f9fa;
            padding: 80px 20px;
            text-align: center;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .hero p {
            font-size: 1.25rem;
            color: #555;
        }
        #navbarNav {
            margin-top: 5px;
        }
        .hero-section {
          background-color: #eef5fd;
          padding: 4rem 0;
        }

        .hero-title {
          font-size: 2.5rem;
          font-weight: 800;
          color: #000;
        }

        .hero-description {
          font-size: 1.1rem;
          color: #555;
          margin-bottom: 1.5rem;
        }

        .btn-primary-custom {
          background-color: #007bff;
          border: none;
          font-weight: 600;
        }

        .btn-outline-custom {
          border: 1px solid #ddd;
          background-color: white;
          font-weight: 600;
        }

        .image-box {
          width: 500px;
          height:350px;
          background-color: #eaeaea;
          border-radius: 10px;
          box-shadow: 0 4px 8px rgba(0,0,0,0.1);
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 2rem;
          color: #bbb;
        }

        @media (max-width: 768px) {
          .hero-title {
            font-size: 2rem;
            text-align: center;
          }
          .hero-description, .hero-buttons {
            text-align: center;
          }
          .map-frame {
            height: 400px;
          }
        }

        .map-section {
            padding: 4rem 0;
            background-color: #f8f9fa;
        }
        .map-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .map-title {
            color:orange;
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
        }
        .map-frame {
            width: 100%;
            height: 500px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(8, 8, 8, 0.1);
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="contact-section">
        <div class="container">
            <div class="row">
          
                <div class="col-md-5">
                    <div class="contact-info">
                        <h2 class="contact-title">Nos coordonnées</h2>
                        
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone  text-warning"></i>
                            </div>
                            <div class="contact-text">
                                <strong>Téléphone</strong>
                                <li class="mb-2 d-flex align-items-start">
                        <i class="fa fa-phone me-3 text-warning"></i>
                        <a href="tel:<?= htmlspecialchars($site['telephone']) ?>" class="text-black text-decoration-none">
                            <?= htmlspecialchars($site['telephone']) ?>
                        </a>
                    </li>
                               <ul class="list-unstyled">
                <p><strong>🕒 </strong> <?= nl2br(htmlspecialchars($site['horaires'])) ?></p>
                </ul>
                            </div>
                        </div>

                    
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope  text-warning"></i>
                            </div>
                            <div class="contact-text">
                                <strong>Email</strong>
                              <li class="mb-2 d-flex align-items-start">
                        <i class="fa fa-envelope me-3 text-warning"></i>
                        <a href="mailto:<?= htmlspecialchars($site['email']) ?>" class="text-black text-decoration-none">
                            <?= htmlspecialchars($site['email']) ?>
                        </a>
                    </li>
                 
                                <small>Réponse sous 24h</small>
                            </div>
                        </div>

                        <!-- Adresse -->
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt  text-warning"></i>
                            </div>
                            <div class="contact-text">
                                <strong>Adresse</strong>
                                   <li class="mb-2 d-flex align-items-start">
                        <i class="fa fa-map-marker-alt me-3 text-warning"></i>
                        <span>
                            <?= nl2br(htmlspecialchars($site['adresse'])) ?>
                        </span>
                    </li>
                            </div>
                        </div>

                       
                    </div>
                </div>

                <!-- Formulaire -->
                <div class="col-md-7">
                  <!-- Remplacer la section formulaire existante par: -->
<div class="contact-form">
    <h2 class="form-title">Envoyez-nous un message</h2>
    
    <?php if ($erreur): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= htmlspecialchars($erreur) ?>
        </div>
    <?php endif; ?>

    <?php if ($succes): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            <?= htmlspecialchars($succes) ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label for="nom" class="form-label">Nom complet</label>
            <input type="text" class="form-control" id="nom" name="nom" 
                   value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" 
                   placeholder="Votre nom" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" 
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   placeholder="votre.email@exemple.com" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" 
                      rows="5" placeholder="Votre message" 
                      required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-send">
            <i class="fas fa-paper-plane"></i>
            Envoyer
        </button>
    </form>
</div>
       
    </div>

    <!-- Section Carte -->
    <section class="map-section">
        <div class="map-container">
            <h2 class="map-title">Notre localisation</h2>
            <div class="map-frame">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d53153.82856138729!2d10.982571522919693!3d33.62827861935028!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x13aaebab42ed5d2b%3A0x631fde8b0054b4fb!2sHassi%20El%20Jerbi!5e0!3m2!1sfr!2stn!4v1746010571059!5m2!1sfr!2stn" width="1200" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>                 
                </iframe>
            </div>
        </div>
    </section>
    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    let isValid = true;
    const inputs = this.querySelectorAll('input, textarea');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
        
        if (input.type === 'email' && !isValidEmail(input.value)) {
            isValid = false;
            input.classList.add('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
    }
});

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}
</script>
</html>