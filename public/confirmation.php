<?php
session_start();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("
        SELECT c.*, cl.nom as client_nom, cl.prenom as client_prenom 
        FROM commandes c
        JOIN clients cl ON c.client_id = cl.id
        WHERE c.id = ? AND c.client_id = ?
    ");
    $stmt->execute([$_GET['id'], $_SESSION['client_id']]);
    $commande = $stmt->fetch();

    if (!$commande) {
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
    <title>Confirmation de commande - Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .confirmation-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/header-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            text-align: center;
        }

        .confirmation-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            text-align: center;
        }

        .check-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="confirmation-header">
        <h1 class="display-4">Commande confirmée</h1>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="confirmation-card">
                    <i class="bi bi-check-circle-fill check-icon"></i>
                    <h2 class="mb-4">Merci pour votre commande !</h2>
                    <p class="lead mb-4">
                        Votre commande #<?= $commande['id'] ?> a été enregistrée avec succès.
                    </p>
                    <div class="text-muted mb-4">
                        <p>Un email de confirmation vous sera envoyé prochainement.</p>
                        <p>Mode de livraison : <?= htmlspecialchars($locations[$commande['mode_livraison']]) ?></p>
                        <?php if ($commande['mode_livraison'] === 'domicile'): ?>
                            <p>Adresse : <?= htmlspecialchars($commande['adresse_livraison']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="index.php" class="btn btn-primary">
                            <i class="bi bi-house me-2"></i>Retour à l'accueil
                        </a>
                        <a href="mes-commandes.php" class="btn btn-outline-primary">
                            <i class="bi bi-list-ul me-2"></i>Voir mes commandes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>