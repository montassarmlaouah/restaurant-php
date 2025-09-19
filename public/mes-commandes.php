<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['client_id'])) {
    header('Location: connexionclient.php');
    exit();
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Check if the session is still valid
$stmt = $pdo->prepare("SELECT id FROM clients WHERE id = ?");
$stmt->execute([$_SESSION['client_id']]);
if ($stmt->rowCount() === 0) {
    session_destroy();
    header('Location: connexionclient.php');
    exit();
}

// Get user's orders
$stmt = $pdo->prepare("
    SELECT c.*, COUNT(ci.id) as nb_items
    FROM commandes c
    LEFT JOIN commande_items ci ON c.id = ci.commande_id
    WHERE c.client_id = ?
    GROUP BY c.id
    ORDER BY c.date_commande DESC
");
$stmt->execute([$_SESSION['client_id']]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Status labels
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
    <title>Mes Commandes - Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .orders-header {
            background: linear-gradient(135deg, #c9bfbfff, #ff7f00);
    color: white;
    padding: 100px 20px;
    margin-bottom: 50px;
    text-align: center;
}
        

        .order-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-5px);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
  <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="orders-header">
        <h1 class="display-4">Mes Commandes</h1>
        <p class="lead">Historique de vos commandes</p>
    </div>

    <div class="container mb-5">
        <?php if (empty($commandes)): ?>
            <div class="text-center py-5">
                <i class="bi bi-bag-x fs-1 text-muted"></i>
                <h2 class="mt-3">Aucune commande</h2>
                <p class="text-muted">Vous n'avez pas encore passé de commande</p>
                <a href="menuclient.php" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left me-2"></i>Voir le menu
                </a>
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
                            <span class="status-badge bg-<?= $status_labels[$commande['statut']]['color'] ?>">
                                <?= $status_labels[$commande['statut']]['label'] ?>
                            </span>
                        </div>
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Total:</strong> 
                                <?= number_format($commande['total'], 2) ?> €
                            </div>
                            <?php if ($commande['statut'] === 'en_attente'): ?>
                                <button class="btn btn-outline-danger btn-sm" 
                                        onclick="cancelOrder(<?= $commande['id'] ?>)">
                                    <i class="bi bi-x-circle me-1"></i>Annuler
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        function cancelOrder(orderId) {
            if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
                fetch('annuler_commande.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'commande_id=' + orderId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Une erreur est survenue');
                    }
                })
                .catch(() => {
                    alert('Une erreur est survenue');
                });
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>