<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
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
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/restaurant-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 6rem 0;
            margin-bottom: 3rem;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .category-section {
            padding: 4rem 0;
            background: #f8f9fa;
        }
        .menu-preview {
            padding: 4rem 0;
        }
        .about-section {
            padding: 4rem 0;
            background: #fff;
        }
        .animate-up {
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbaradmin.php'; ?>

    <div class="hero-section text-center" style="background: linear-gradient(rgba(42, 42, 228, 0.93), rgba(0,0,128,0.7)), url('images/restaurant-bg.jpg'); background-size: cover; background-position: center; color: white; padding: 6rem 0; margin-bottom: 3rem;">
        <div class="container">
            <h1 class="display-3 mb-4">Bienvenue à Notre Restaurant</h1>
            <p class="lead mb-4">Découvrez une expérience culinaire unique</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card text-center animate-up">
                    <i class="bi bi-grid-3x3-gap fs-1 text-primary mb-3"></i>
                    <h3><?= $stats['categories'] ?></h3>
                    <p class="text-muted mb-0">Catégories</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center animate-up">
                    <i class="fas fa-utensils text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h3><?= $stats['plats'] ?></h3>
                    <p class="text-muted mb-0">Plats</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center animate-up">
                    <i class="bi bi-people fs-1 text-info mb-3"></i>
                    <h3><?= $stats['clients'] ?></h3>
                    <p class="text-muted mb-0">Clients</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center animate-up">
                    <i class="bi bi-bag-check fs-1 text-warning mb-3"></i>
                    <h3><?= $stats['commandes'] ?></h3>
                    <p class="text-muted mb-0">Commandes</p>
                </div>
            </div>
        </div>
    </div>

    <div class="category-section" id="categories">
        <div class="container">
            <h2 class="text-center mb-5">Nos Catégories</h2>
            <div class="row g-4">
                <?php foreach ($categories as $categorie): ?>
                    <div class="col-md-4">
                        <div class="card h-100 animate-up">
                            <?php if ($categorie['image']): ?>
                                <img src="uploads/categories/<?= htmlspecialchars($categorie['image']) ?>" 
                                     class="card-img-top" alt="<?= htmlspecialchars($categorie['nom']) ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= htmlspecialchars($categorie['nom']) ?></h5>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-4">À Propos de Nous</h2>
                    <p class="lead">Découvrez notre passion pour la cuisine authentique</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                </div>
                <div class="col-md-6">
                    <img src="images/about-restaurant.jpg" alt="Notre Restaurant" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>