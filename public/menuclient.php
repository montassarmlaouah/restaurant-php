
<?php
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $categorie_id = isset($_GET['categorie']) ? intval($_GET['categorie']) : null;
    
    if ($categorie_id) {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$categorie_id]);
        $categorie = $stmt->fetch();
        
        if (!$categorie) {
            header('Location: index.php');
            exit();
        }
        
         $stmt = $pdo->prepare("
            SELECT p.*, c.nom as categorie_nom 
            FROM plats p 
            JOIN categories c ON p.categorie_id = c.id 
            WHERE p.categorie_id = ?
            ORDER BY p.nom
        ");
        $stmt->execute([$categorie_id]);
        $plats = $stmt->fetchAll();
    } else {
        header('Location: index.php');
        exit();
    }
    
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($categorie['nom']) ?> - Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    </head>
<body>
    

<style>
.menu-header {
    background: linear-gradient(135deg, #c9bfbfff, #ff7f00);
    color: white;
    padding: 100px 20px;
    margin-bottom: 50px;
    text-align: center;
}
 
.menu-header h1 {
    font-weight: 700;
    font-size: 2.5rem;
}
.menu-header p {
    font-size: 1.2rem;
    color: #f1f1f1;
}

/* ====== TITRES DE CATÉGORIE ====== */
.category-title {
    position: relative;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 30px;
    color: #2c3e50;
}
.category-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 60px;
    height: 4px;
    background-color: #ff5c43;
    border-radius: 2px;
}

/* ====== CARTES PLATS ====== */
.menu-card {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-bottom: 30px;
    background: #fff;
}
.menu-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}
.menu-card img {
    height: 200px;
    width: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.menu-card:hover img {
    transform: scale(1.05);
}
.menu-card .card-body {
    padding: 1.5rem;
}
.menu-card .card-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
}
.menu-card .description {
    color: #666;
    font-size: 0.95rem;
    margin-bottom: 1rem;
    min-height: 48px;
}
.price {
    font-size: 1.25rem;
    font-weight: bold;
    color: #ff5c43;
}

/* ====== BOUTON AJOUTER ====== */
.add-to-cart {
    background: linear-gradient(135deg, #ff5c43, #ff784e);
    border: none;
    color: #fff;
    font-weight: 600;
    padding: 10px 16px;
    border-radius: 10px;
    transition: all 0.3s ease;
}
.add-to-cart:hover {
    background: linear-gradient(135deg, #ff784e, #ff5c43);
    transform: scale(1.05);
}

/* ====== SECTION MENU ====== */
.menu-section {
    padding: 40px 0;
}

/* ====== RESPONSIVE ====== */
@media (max-width: 768px) {
    .menu-header h1 {
        font-size: 2rem;
    }
    .menu-card img {
        height: 180px;
    }
}

    </style>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="menu-header text-center">
        <div class="container">
            <div class="row justify-content-center">
                <?php
                    $categorieImagePath = 'uploads/categories/' . $categorie['image'];
                    if (!empty($categorie['image']) && file_exists($categorieImagePath)):
                ?>
                    <div class="col-12 mb-4">
                        <img src="<?= htmlspecialchars($categorieImagePath) ?>" 
                             alt="<?= htmlspecialchars($categorie['nom']) ?>" 
                             class="rounded shadow" 
                             style="max-width: 220px; max-height: 180px; object-fit: cover;">
                    </div>
                <?php else: ?>
                    <div class="col-12 mb-4">
                        <img src="images/no-image.png" 
                             alt="Image non disponible" 
                             class="rounded shadow" 
                             style="max-width: 220px; max-height: 180px; object-fit: cover;">
                    </div>
                <?php endif; ?>
            </div>
            <h1 class="display-4"><?= htmlspecialchars($categorie['nom']) ?></h1>
            <p class="lead">Découvrez notre sélection de <?= htmlspecialchars(mb_strtolower($categorie['nom'])) ?></p>
            <a href="index.php" class="btn btn-outline-light mt-3">
                <i class="bi bi-arrow-left"></i> Retour aux catégories
            </a>
        </div>
    </div>

    <div class="container">
        <div class="menu-section">
            <div class="row">
                <?php foreach($plats as $plat): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card menu-card">
                            <?php
                                $imagePath = 'uploads/plats/' . $plat['image'];
                                if (!empty($plat['image']) && file_exists($imagePath)):
                            ?>
                                <img src="<?= htmlspecialchars($imagePath) ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($plat['nom']) ?>">
                            <?php else: ?>
                                <img src="images/no-image.png" 
                                     class="card-img-top" 
                                     alt="Image non disponible">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($plat['nom']) ?></h5>
                                <p class="description"><?= htmlspecialchars($plat['description']) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="price"><?= number_format($plat['prix'], 2) ?> €</span>
                                    <button class="btn add-to-cart" 
                                            onclick="addToCart(<?= $plat['id'] ?>)">
                                        <i class="bi bi-cart-plus"></i> Ajouter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToCart(platId) {
            fetch('ajouter_panier.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'plat_id=' + platId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Plat ajouté au panier !');
                    if (data.cartCount) {
                        document.querySelector('.cart-count').textContent = data.cartCount;
                    }
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue');
            });
        }
    </script>
</body>
</html>