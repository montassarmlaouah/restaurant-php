<?php
session_start();

// Verify cart and delivery selection
if (empty($_SESSION['cart']) || !isset($_SESSION['order_location'])) {
    header('Location: commander.php');
    exit();
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Get cart items
    $plats = [];
    $total = 0;
    if (!empty($_SESSION['cart'])) {
        $plat_ids = array_keys($_SESSION['cart']);
        $placeholders = str_repeat('?,', count($plat_ids) - 1) . '?';
        
        $stmt = $pdo->prepare("SELECT * FROM plats WHERE id IN ($placeholders)");
        $stmt->execute($plat_ids);
        $plats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($plats as $plat) {
            $total += $plat['prix'] * $_SESSION['cart'][$plat['id']];
        }
    }

    // Process payment
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_SESSION['client_id'])) {
            $error = "Veuillez vous connecter pour finaliser la commande";
        } else {
            try {
                $pdo->beginTransaction();

                // Create order
                $stmt = $pdo->prepare("
                    INSERT INTO commandes (
                        client_id, 
                        total, 
                        statut, 
                        mode_livraison, 
                        adresse_livraison,
                        date_commande
                    ) VALUES (?, ?, 'en_attente', ?, ?, NOW())
                ");
                $stmt->execute([
                    $_SESSION['client_id'],
                    $total,
                    $_SESSION['order_location'],
                    $_SESSION['order_location'] === 'domicile' ? $_SESSION['delivery_address'] : null
                ]);
                
                $commande_id = $pdo->lastInsertId();

                // Add order items
                $stmt = $pdo->prepare("
                    INSERT INTO commande_items (
                        commande_id, 
                        plat_id, 
                        quantite, 
                        prix_unitaire
                    ) VALUES (?, ?, ?, ?)
                ");

                foreach ($plats as $plat) {
                    $stmt->execute([
                        $commande_id,
                        $plat['id'],
                        $_SESSION['cart'][$plat['id']],
                        $plat['prix']
                    ]);
                }

                $pdo->commit();
                
                // Clear cart and delivery info
                unset($_SESSION['cart'], $_SESSION['order_location'], $_SESSION['delivery_address']);
                
                // Redirect to confirmation
                header("Location: confirmation.php?id=" . $commande_id);
                exit();

            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Une erreur est survenue lors de la commande";
            }
        }
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
    <title>Paiement - Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .payment-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/header-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            text-align: center;
        }

        .payment-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .order-summary {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
        }

        .payment-method {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            border-color: #ffc107;
        }

        .payment-method.selected {
            border-color: #28a745;
            background-color: #f8fff9;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="payment-header">
        <h1 class="display-4">Paiement</h1>
        <p class="lead">Finalisez votre commande</p>
    </div>

    <div class="container mb-5">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
                <?php if ($error === "Veuillez vous connecter pour finaliser la commande"): ?>
                    <a href="connexionclient.php" class="btn btn-outline-danger ms-3">Se connecter</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="payment-card">
                    <h3 class="mb-4">Mode de paiement</h3>
                    <form method="POST" id="paymentForm">
                        <div class="payment-method selected" onclick="selectPayment('especes')">
                            <input type="radio" name="payment_method" value="especes" checked class="d-none">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash me-3 fs-4"></i>
                                <div>
                                    <h5 class="mb-1">Paiement en espèces</h5>
                                    <p class="mb-0 text-muted">Payez à la livraison ou au restaurant</p>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 mt-4">
                            <i class="bi bi-lock me-2"></i>
                            Confirmer la commande
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="payment-card">
                    <h4 class="mb-4">Résumé de la commande</h4>
                    <div class="order-summary">
                        <div class="mb-3">
                            <h6>Mode de livraison:</h6>
                            <p class="mb-0">
                                <?= htmlspecialchars($locations[$_SESSION['order_location']]) ?>
                                <?php if ($_SESSION['order_location'] === 'domicile'): ?>
                                    <br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($_SESSION['delivery_address']) ?>
                                    </small>
                                <?php endif; ?>
                            </p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Total à payer</h5>
                            <h5 class="mb-0"><?= number_format($total, 2) ?> €</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectPayment(method) {
            document.querySelectorAll('.payment-method').forEach(option => {
                option.classList.remove('selected');
            });
            document.querySelector(`input[value="${method}"]`).closest('.payment-method').classList.add('selected');
        }
    </script>
</body>
</html>