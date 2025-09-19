<?php
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $upload_dir = "uploads/plats/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Récupérer toutes les catégories pour le select
    $categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

    // Ajouter un plat
    if (isset($_POST['ajouter'])) {
        $nom = $_POST['nom'];
        $categorie_id = $_POST['categorie_id'];
        $description = $_POST['description'];
        $prix = $_POST['prix'];
        $image = null;

        if (!empty($_FILES['image']['name'])) {
            $image = uniqid() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
        }

        $stmt = $pdo->prepare("INSERT INTO plats (nom, categorie_id, description, image, prix) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $categorie_id, $description, $image, $prix]);
        header("Location: menuadmin.php?success=ajout");
        exit;
    }

    // Modifier un plat
    if (isset($_POST['modifier'])) {
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $categorie_id = $_POST['categorie_id'];
        $description = $_POST['description'];
        $prix = $_POST['prix'];
        $ancienne_image = $_POST['ancienne_image'];
        $image = $ancienne_image;

        if (!empty($_FILES['image']['name'])) {
            $image = uniqid() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
            if ($ancienne_image && file_exists($upload_dir . $ancienne_image)) {
                unlink($upload_dir . $ancienne_image);
            }
        }

        $stmt = $pdo->prepare("UPDATE plats SET nom=?, categorie_id=?, description=?, image=?, prix=? WHERE id=?");
        $stmt->execute([$nom, $categorie_id, $description, $image, $prix, $id]);
        header("Location: menuadmin.php?success=modification");
        exit;
    }

    if (isset($_GET['supprimer'])) {
        $id = $_GET['supprimer'];
        $plat = $pdo->query("SELECT image FROM plats WHERE id=$id")->fetch();
        if ($plat && $plat['image'] && file_exists($upload_dir . $plat['image'])) {
            unlink($upload_dir . $plat['image']);
        }
        $pdo->exec("DELETE FROM commande_items WHERE plat_id=$id");
        $pdo->exec("DELETE FROM plats WHERE id=$id");
        header("Location: menuadmin.php?success=suppression");
        exit;
    }

    $plats = $pdo->query("SELECT p.*, c.nom AS categorie_nom FROM plats p LEFT JOIN categories c ON p.categorie_id = c.id ORDER BY p.nom")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Plats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .menu-header { background: #222; color: #fff; padding: 3rem 0; margin-bottom: 2rem; }
        .plat-card { transition: transform 0.3s ease; }
        .plat-card:hover { transform: translateY(-5px); }
        .plat-image { height: 200px; object-fit: cover; width: 100%; border-radius: 8px 8px 0 0; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbaradmin.php'; ?>

    <div class="menu-header text-center"  style="background: linear-gradient(rgba(42, 42, 228, 0.93), rgba(0,0,128,0.7)), url('images/restaurant-bg.jpg'); background-size: cover; background-position: center; color: white; padding: 6rem 0; margin-bottom: 3rem;">

        <h1 class="display-4">Gestion des Plats</h1>
        <button type="button" class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#modalAjoutPlat">
            <i class="bi bi-plus-circle"></i> Ajouter un plat
        </button>
    </div>

    <div class="container mb-5">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php
                    switch ($_GET['success']) {
                        case 'ajout': echo "Le plat a été ajouté avec succès."; break;
                        case 'modification': echo "Le plat a été modifié avec succès."; break;
                        case 'suppression': echo "Le plat a été supprimé avec succès."; break;
                    }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Modal Ajout Plat -->
        <div class="modal fade" id="modalAjoutPlat" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un nouveau plat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control" name="nom" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catégorie</label>
                                <select class="form-control" name="categorie_id" required>
                                    <option value="">Choisir...</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prix (€)</label>
                                <input type="number" step="0.01" class="form-control" name="prix" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Image</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="ajouter" class="btn btn-success">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($plats as $plat): ?>
                <div class="col-md-4">
                    <div class="card plat-card h-100 shadow-sm">
                        <?php if ($plat['image']): ?>
                            <img src="<?= htmlspecialchars($upload_dir . $plat['image']) ?>" class="plat-image" alt="<?= htmlspecialchars($plat['nom']) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($plat['nom']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($plat['description']) ?></p>
                            <p class="card-text"><strong>Catégorie :</strong> <?= htmlspecialchars($plat['categorie_nom']) ?></p>
                            <p class="card-text"><strong>Prix :</strong> <?= number_format($plat['prix'], 2) ?> €</p>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $plat['id'] ?>">
                                    <i class="bi bi-pencil"></i> Modifier
                                </button>
                                <a href="?supprimer=<?= $plat['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce plat ?')">
                                    <i class="bi bi-trash"></i> Supprimer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalEdit<?= $plat['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Modifier le plat</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $plat['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Nom</label>
                                        <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($plat['nom']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Catégorie</label>
                                        <select class="form-control" name="categorie_id" required>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>" <?= $plat['categorie_id'] == $cat['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['nom']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" required><?= htmlspecialchars($plat['description']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Prix (€)</label>
                                        <input type="number" step="0.01" class="form-control" name="prix" value="<?= htmlspecialchars($plat['prix']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Image</label>
                                        <?php if ($plat['image']): ?>
                                            <div class="mb-2">
                                                <img src="<?= htmlspecialchars($upload_dir . $plat['image']) ?>" alt="Image actuelle" class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control" name="image" accept="image/*">
                                        <input type="hidden" name="ancienne_image" value="<?= htmlspecialchars($plat['image']) ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" name="modifier" class="btn btn-primary">Modifier</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>