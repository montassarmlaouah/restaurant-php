<?php
session_start();


try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $upload_dir = "uploads/categories/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $message = '';
    $error = '';

    // Traitement de l'ajout
    if (isset($_POST['ajouter'])) {
        $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
        
        if ($nom) {
            try {
                $image_name = null;
                if (!empty($_FILES['image']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        throw new Exception("Format d'image non autorisé.");
                    }
                    
                    $image_name = uniqid() . "." . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
                }

                $stmt = $pdo->prepare("INSERT INTO categories (nom, image) VALUES (?, ?)");
                $stmt->execute([$nom, $image_name]);
                header("Location: categoriesadmin.php?success=ajout");
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }

    // Traitement de la modification
    if (isset($_POST['modifier'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
        
        if ($id && $nom) {
            try {
                $image = $_POST['ancienne_image'];
                if (!empty($_FILES['image']['name'])) {
                    // Supprimer l'ancienne image
                    if ($image && file_exists($upload_dir . $image)) {
                        unlink($upload_dir . $image);
                    }
                    
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        throw new Exception("Format d'image non autorisé.");
                    }
                    
                    $image = uniqid() . "." . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
                }

                $stmt = $pdo->prepare("UPDATE categories SET nom = ?, image = ? WHERE id = ?");
                $stmt->execute([$nom, $image, $id]);
                header("Location: categoriesadmin.php?success=modification");
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }

    // Traitement de la suppression
    if (isset($_GET['supprimer'])) {
        $id = filter_input(INPUT_GET, 'supprimer', FILTER_VALIDATE_INT);
        if ($id) {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM plats WHERE categorie_id = ?");
                $stmt->execute([$id]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("Cette catégorie contient des plats et ne peut pas être supprimée.");
                }

                $stmt = $pdo->prepare("SELECT image FROM categories WHERE id = ?");
                $stmt->execute([$id]);
                $image = $stmt->fetchColumn();
                
                if ($image && file_exists($upload_dir . $image)) {
                    unlink($upload_dir . $image);
                }

                $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                $stmt->execute([$id]);
                
                header("Location: categoriesadmin.php?success=suppression");
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }

    // Récupérer la catégorie à modifier
    $edit_categorie = null;
    if (isset($_GET['edit'])) {
        $id = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $edit_categorie = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    // Récupérer toutes les catégories
    $categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des catégories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbaradmin.php'; ?>

    <div class="menu-header text-center"  style="background: linear-gradient(rgba(42, 42, 228, 0.93), rgba(0,0,128,0.7)), url('images/restaurant-bg.jpg'); background-size: cover; background-position: center; color: white; padding: 6rem 0; margin-bottom: 3rem;">

        <h1 class="display-4">Gestion des categories</h1>
        
        </button>
    </div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php
                            switch ($_GET['success']) {
                                case 'ajout':
                                    echo "La catégorie a été ajoutée avec succès.";
                                    break;
                                case 'modification':
                                    echo "La catégorie a été modifiée avec succès.";
                                    break;
                                case 'suppression':
                                    echo "La catégorie a été supprimée avec succès.";
                                    break;
                            }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <?= $edit_categorie ? "Modifier la catégorie" : "Ajouter une catégorie" ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom de la catégorie</label>
                                <input type="text" class="form-control" name="nom" id="nom" 
                                       value="<?= htmlspecialchars($edit_categorie['nom'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" class="form-control" name="image" id="image" accept="image/*">
                                <?php if ($edit_categorie && $edit_categorie['image']): ?>
                                    <div class="mt-2">
                                        <img src="<?= htmlspecialchars($upload_dir . $edit_categorie['image']) ?>" 
                                             alt="Image actuelle" class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                    <input type="hidden" name="ancienne_image" 
                                           value="<?= htmlspecialchars($edit_categorie['image']) ?>">
                                <?php endif; ?>
                            </div>

                            <?php if ($edit_categorie): ?>
                                <input type="hidden" name="id" value="<?= $edit_categorie['id'] ?>">
                                <button type="submit" name="modifier" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Modifier
                                </button>
                                <a href="categoriesadmin.php" class="btn btn-secondary">
                                    <i class="bi bi-x"></i> Annuler
                                </a>
                            <?php else: ?>
                                <button type="submit" name="ajouter" class="btn btn-success">
                                    <i class="bi bi-plus-lg"></i> Ajouter
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Liste des catégories</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Nom</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $cat): ?>
                                        <tr>
                                            <td style="width: 100px;">
                                                <?php if ($cat['image']): ?>
                                                    <img src="<?= htmlspecialchars($upload_dir . $cat['image']) ?>" 
                                                         alt="<?= htmlspecialchars($cat['nom']) ?>" 
                                                         class="img-thumbnail" style="max-width: 80px;">
                                                <?php else: ?>
                                                    <div class="text-muted small">Pas d'image</div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle"><?= htmlspecialchars($cat['nom']) ?></td>
                                            <td class="align-middle">
                                                <a href="?edit=<?= $cat['id'] ?>" 
                                                   class="btn btn-sm btn-primary me-2">
                                                    <i class="bi bi-pencil"></i> Modifier
                                                </a>
                                                <a href="?supprimer=<?= $cat['id'] ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                                    <i class="bi bi-trash"></i> Supprimer
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>