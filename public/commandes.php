<?php
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->query("
        SELECT c.*, cl.nom as client_nom, cl.email as client_email,
               COUNT(ci.id) as nb_items
        FROM commandes c
        LEFT JOIN clients cl ON c.client_id = cl.id
        LEFT JOIN commande_items ci ON c.id = ci.commande_id
        GROUP BY c.id
        ORDER BY c.date_commande DESC
    ");
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_POST['update_status'])) {
        $commande_id = filter_input(INPUT_POST, 'commande_id', FILTER_VALIDATE_INT);
        $nouveau_statut = filter_input(INPUT_POST, 'nouveau_statut', FILTER_SANITIZE_STRING);
        
        if ($commande_id && $nouveau_statut) {
            $stmt = $pdo->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
            $stmt->execute([$nouveau_statut, $commande_id]);
            header('Location: commandes.php?success=update');
            exit();
        }
    }

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Labels des statuts
$status_labels = [
    'en_attente' => ['label' => 'En attente', 'color' => 'warning'],
    'confirmee' => ['label' => 'Confirmée', 'color' => 'info'],
    'en_preparation' => ['label' => 'En préparation', 'color' => 'primary'],
    'prete' => ['label' => 'Prête', 'color' => 'success'],
    'livree' => ['label' => 'Livrée', 'color' => 'secondary'],
    'annulee' => ['label' => 'Annulée', 'color' => 'danger']
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .orders-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/restaurant-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }

        .order-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-5px);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }

        .order-details {
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 1rem;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbaradmin.php'; ?>

    <div class="orders-header text-center" style="background: linear-gradient(rgba(42, 42, 228, 0.93), rgba(0,0,128,0.7)), url('images/restaurant-bg.jpg'); background-size: cover; background-position: center; color: white; padding: 6rem 0; margin-bottom: 3rem;">
        <h1 class="display-4">Gestion des Commandes</h1>
        <p class="lead">Suivi et gestion des commandes clients</p>
    </div>

    <div class="container mb-5">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                Le statut de la commande a été mis à jour avec succès.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($commandes)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <h2 class="mt-3">Aucune commande</h2>
                <p class="text-muted">Il n'y a pas encore de commandes à afficher</p>
            </div>
        <?php else: ?>
            <?php foreach ($commandes as $commande): ?>
                <div class="order-card">
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Commande #<?= $commande['id'] ?></h5>
                                <small class="text-muted">
                                    <?= date('d/m/Y à H:i', strtotime($commande['date_commande'])) ?>
                                </small>
                            </div>
                            <form method="POST" class="d-flex align-items-center">
                                <input type="hidden" name="commande_id" value="<?= $commande['id'] ?>">
                                <select name="nouveau_statut" class="form-select form-select-sm me-2" style="width: auto;">
                                    <?php foreach ($status_labels as $value => $status): ?>
                                        <option value="<?= $value ?>" 
                                                <?= $commande['statut'] === $value ? 'selected' : '' ?>>
                                            <?= $status['label'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-primary btn-sm">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </form>
                        </div>
                        
                        <div class="order-details mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Client</h6>
                                    <p class="mb-1">
                                        <i class="bi bi-person me-2"></i>
                                        <?= htmlspecialchars($commande['client_nom']) ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-envelope me-2"></i>
                                        <?= htmlspecialchars($commande['client_email']) ?>
                                    </p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <h6>Détails</h6>
                                    <p class="mb-1">
                                        <i class="bi bi-cart me-2"></i>
                                        <?= $commande['nb_items'] ?> article(s)
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-currency-euro me-2"></i>
                                        <strong><?= number_format($commande['total'], 2) ?> €</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>