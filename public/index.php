<?php
$pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '');
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
session_start();

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
    $plats = [];
    if (!empty($_SESSION['cart'])) {
        $plat_ids = array_keys($_SESSION['cart']);
        $placeholders = str_repeat('?,', count($plat_ids) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM plats WHERE id IN ($placeholders)");
        $stmt->execute($plat_ids);
        $plats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $total = 0;
    foreach ($plats as $plat) {
        $total += $plat['prix'] * $_SESSION['cart'][$plat['id']];
    }
    $categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("
        SELECT p.*, c.nom as categorie_nom 
        FROM plats p 
        JOIN categories c ON p.categorie_id = c.id 
        ORDER BY c.nom, p.nom
    ");
    $plats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stats = [
        'categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
        'plats' => $pdo->query("SELECT COUNT(*) FROM plats")->fetchColumn(),
        'clients' => $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn(),
        'commandes' => $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn()
    ];
    
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}


?>
   



<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accueil - Restaurant</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
/* ===== BACKGROUND ===== */
body {
  background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
  min-height: 100vh;
  position: relative;
  overflow-x: hidden;
}
body::before,
body::after {
  content: '';
  position: absolute;
  border-radius: 50%;
  filter: blur(120px);
  opacity: 0.6;
  z-index: -1;
}
body::before {
  width: 400px;
  height: 400px;
  background: #ff9a9e;
  top: -100px;
  left: -150px;
}

/* ===== HERO ===== */
.hero-section {
  background: rgba(255,255,255,0.15);
  backdrop-filter: blur(8px);
  border-radius: 30px;
  box-shadow: 0 8px 32px rgba(31,38,135,0.18);
  padding: 60px;
  max-width: 1000px;
  margin: 60px auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 40px;
}
.hero-img {
  width: 280px;
  height: 280px;
  border-radius: 50%;
  object-fit: cover;
  border: 8px solid #fff;
  box-shadow: 0 4px 24px rgba(0,0,0,0.18);
}
.hero-text h1 {
  font-size: 2.8rem;
  font-weight: bold;
}
.hero-text p {
  font-size: 1.1rem;
  color: #555;
}
.hero-btns {
  display: flex;
  gap: 18px;
}
.hero-btns .btn {
  border-radius: 20px;
  font-weight: 600;
  padding: 10px 28px;
  font-size: 1.1rem;
}

/* ===== TITRES ===== */
.section-title {
  text-align: center;
  margin: 60px 0 40px;
  color: #2c3e50;
  font-size: 2.1rem;
  font-weight: 700;
  letter-spacing: 1px;
}

/* ===== TOP LIST ===== */
.top-list {
  display: flex;
  justify-content: center;
  gap: 30px;
  flex-wrap: wrap;
}
.top-card {
  background: rgba(255,255,255,0.85);
  border-radius: 22px;
  box-shadow: 0 4px 18px rgba(0,0,0,0.10);
  padding: 20px;
  width: 240px;
  text-align: center;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.top-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}
.top-card img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 15px;
  border: 5px solid #fff;
}
.stars {
  font-size: 1.1em;
  color: #ffc107;
  margin-bottom: 6px;
}
.price {
  font-weight: bold;
  font-size: 1.1rem;
  color: #ff8000;
}

/* ===== POTATOES ===== */
.best-potatoes {
  display: flex;
  align-items: center;
  gap: 30px;
  background: rgba(255,255,255,0.85);
  border-radius: 22px;
  box-shadow: 0 4px 18px rgba(0,0,0,0.10);
  padding: 25px;
  max-width: 800px;
  margin: 50px auto;
}
.best-potatoes img {
  width: 250px;
  border-radius: 18px;
}
.best-potatoes-text h4 {
  font-size: 1.6rem;
  font-weight: 700;
}

/* ===== SERVICES ===== */
.services-section {
  text-align: center;
  margin-bottom: 50px;
}
.services-icons {
  display: flex;
  justify-content: center;
  gap: 40px;
  margin: 20px 0;
  flex-wrap: wrap;
}
.services-icons .icon {
  font-size: 2rem;
  color: #2c3e50;
  background: #fff;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: transform 0.2s ease;
}
.services-icons .icon:hover {
  transform: scale(1.1);
  color: #ff8000;
}
.vietnam-img {
        box-shadow: 0 8px 32px rgba(31,38,135,0.18);
        border: 6px solid #fff;
        object-fit: cover;
        transition: transform 0.2s;
      }
      .vietnam-img:hover {
        transform: scale(1.04) rotate(-2deg);
        box-shadow: 0 12px 36px rgba(31,38,135,0.22);
      }
      .services-we-offer {
  padding: 5rem 0;
}
.services-we-offer h2 {
  font-size: 2.5rem;
  color: #f19d28; /* Orange color from the image */
  font-weight: bold;
  margin-bottom: 3rem;
}
.service-card {
  background: #fff;
  border-radius: 1.5rem;
  padding: 2rem;
  box-shadow: 0 8px 32px rgba(31,38,135,0.1);
  text-align: center;
  height: 100%; /* Ensures all cards are the same height */
}
.orange-card {
  background: #f19d28;
  color: #fff;
}
.service-icon-container {
  font-size: 4rem;
  margin-bottom: 1rem;
}
.orange-card .service-icon-container {
  color: #fff;
}
.service-icon-container i {
  color: #f19d28;
}
.orange-card i {
  color: #fff !important;
}
.service-card h5 {
  font-weight: bold;
  margin-bottom: 1rem;
}
.service-card p {
  color: #666;
  font-size: 0.9rem;
  line-height: 1.6;
}
.orange-card p {
  color: #fff;
}

  </style>
</head>
<body>
  <?php include __DIR__ . '/../includes/navbar.php'; ?>

  <!-- HERO -->
  <div class="hero-section">
    <div class="hero-text">
      <h1>De la nourriture délicieuse vous attend</h1>
      <p>Notre équipe d'infirmières diplômées et de professionnels de la santé qualifiés fournit des soins infirmiers à domicile</p>
      <div class="hero-btns">
<a href="http://localhost/hassijerbi/menu/menu.html" class="btn btn-warning">Menu Alimentaire</a>        <a href="reservation.php" class="btn btn-outline-dark">Réserver une table</a>
      </div>
    </div>
    <img src="images/image1.jpg" alt="Bowl" class="hero-img">
  </div>

  <div class="top-list">
    <?php foreach ($categories as $categorie): ?>
      <div class="top-card">
        <img src="uploads/categories/<?= htmlspecialchars($categorie['image']) ?>" alt="<?= htmlspecialchars($categorie['nom']) ?>">
        <div class="fw-bold"><?= htmlspecialchars($categorie['nom']) ?></div>
<a  class="btn btn-warning" href="menuclient.php?categorie=<?= $categorie['id'] ?>" class="view-menu-btn">
                                    Voir le menu
                                </a>
       

      </div>
    <?php endforeach; ?>
</div>

  <!-- POTATOES -->
  <div class="best-potatoes">
    <img src="images/French-Fries.jpeg" alt="French Fries">
    <div class="best-potatoes-text">
      <h4>Meilleures pommes de terre pour frites</h4>
      <p>Les pommes de terre russet sont idéales. Comme elles sont denses, elles ne contiennent pas autant d'eau à l'intérieur, ce qui leur permet de devenir extra croustillantes.</p>
    </div>
  </div>

  <!-- SERVICES -->
  <div class="services-section">
    <h4>Our services</h4>
    <div class="services-icons">
      <div class="icon"><i class="bi bi-calendar-check"></i></div>
      <div class="icon"><i class="bi bi-shop"></i></div>
      <div class="icon"><i class="bi bi-person-badge"></i></div>
      <div class="icon"><i class="bi bi-truck"></i></div>
    </div>
    <div style="font-size:1em;color:#2c3e50;">
      Réservation en ligne &nbsp; | &nbsp; Service traiteur &nbsp; | &nbsp; Adhésion &nbsp; | &nbsp; Service de livraison
    </div>
  </div>
   

  <div class="services-we-offer container">
    <h2 class="text-center">Services que nous offrons</h2>
    <div class="row justify-content-center">
      <div class="col-lg-4 mb-4">
        <div class="service-card d-flex flex-column align-items-center">
          <div class="service-icon-container">
            <i class="fa-solid fa-gift"></i>
          </div>
          <h5 class="fw-bold">Bon de réduction</h5>
          <p class="text-muted">
            Les aliments frais sont des aliments qui n'ont pas été conservés et qui ne se sont pas encore gâtés. Pour les légumes et les fruits, cela signifie qu'ils ont été récemment récoltés et correctement traités après récolte ; pour la viande, elle a été récemment abattue et découpée ; pour le poisson.
          </p>
        </div>
      </div>
      <div class="col-lg-4 mb-4">
        <div class="service-card orange-card d-flex flex-column align-items-center">
          <div class="service-icon-container">
            <i class="fa-solid fa-utensils"></i>
          </div>
          <h5 class="fw-bold text-white">Offre de réduction</h5>
          <p class="text-white">
            Les aliments frais sont des aliments qui n'ont pas été conservés et qui ne se sont pas encore gâtés. Pour les légumes et les fruits, cela signifie qu'ils ont été récemment récoltés et correctement traités après récolte ; pour la viande, elle a été récemment abattue et découpée ; pour le poisson.
          </p>
        </div>
      </div>
      <div class="col-lg-4 mb-4">
        <div class="service-card d-flex flex-column align-items-center">
          <div class="service-icon-container">
            <i class="fa-solid fa-truck"></i>
          </div>
          <h5 class="fw-bold">Livraison à domicile gratuite et rapide</h5>
          <p class="text-muted">
            Les aliments frais sont des aliments qui n'ont pas été conservés et qui ne se sont pas encore gâtés. Pour les légumes et les fruits, cela signifie qu'ils ont été récemment récoltés et correctement traités après récolte ; pour la viande, elle a été récemment abattue et découpée ; pour le poisson.
          </p>
        </div>
      </div>
    </div>
  </div>


<!-- FEATURES SECTION -->
<div class="container my-5">
  <div class="row align-items-center">
    
    <!-- Texte -->
    <div class="col-lg-6">
      <h2 class="fw-bold mb-4">Les caractéristiques clés de la cuisine vietnamienne incluent</h2>
      <p class="text-muted mb-4">
        La cuisine vietnamienne fait référence à la cuisine diversifiée et savoureuse originaire du Vietnam,
        un pays d'Asie du Sud-Est avec un riche patrimoine culinaire.
      </p>
      <ul class="list-unstyled mb-4">
        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Ingrédients frais</li>
        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Légère et saine</li>
        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Riz et nouilles</li>
        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Aromatic Herbs and Spices</li>
        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Street Food Culture</li>
      </ul>
      <a href="apropos.php" class="btn btn-success rounded-pill px-4">Read more</a>
    </div>

    <!-- Images -->
    <div class="col-lg-6 d-flex gap-3 justify-content-center">
        <div class="d-flex flex-column gap-3 w-50">
        <img src="images/image3.jpg" class="img-fluid rounded-4 mb-3 vietnam-img" alt="Vietnamese Curry">
        <img src="images/image4.jpg" class="img-fluid rounded-4 vietnam-img" alt="Vietnamese Food">
      </div>
    </div>
</div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
  <?php include __DIR__ . '/../includes/footer.php'; ?>
  

