<?php
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

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

} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .cart-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/header-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            text-align: center;
        }

        .cart-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .cart-table th {
            background: #f8f9fa;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .cart-table td {
            vertical-align: middle;
            padding: 1.2rem 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .cart-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 20px;
        }

        .quantity-btn {
            border: none;
            background: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .quantity-btn:hover {
            background: #e9ecef;
            transform: scale(1.1);
        }

        .quantity {
            font-weight: 600;
            min-width: 30px;
            text-align: center;
        }

        .remove-item {
            color: #dc3545;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .remove-item:hover {
            color: #c82333;
            transform: scale(1.1);
        }

        .cart-total {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .cart-total h4 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }

        .cart-total .total-amount {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .btn-commander {
            background: #28a745;
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-commander:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .empty-cart {
            text-align: center;
            padding: 3rem;
        }

        .empty-cart i {
            font-size: 5rem;
            color: #6c757d;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .cart-image {
                width: 60px;
                height: 60px;
            }
            
            .quantity-control {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
      
    
    .notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .notification.error {
        background-color: #dc3545;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
    }

    .notification.success {
        background-color: #28a745;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
    }

    .notification.show {
        opacity: 1;
        transform: translateY(0);
    }
</style>
    </style>
</head>
<body>
  <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="cart-header">
        <h1 class="display-4">Mon Panier</h1>
        <p class="lead">Gérez vos articles et passez commande</p>
    </div>

    <div class="container mb-5">
        <?php if (empty($plats)): ?>
            <div class="empty-cart">
                <i class="bi bi-cart-x"></i>
                <h2>Votre panier est vide</h2>
                <p class="text-muted">Découvrez notre délicieux menu et commencez votre commande.</p>
                <a href="menuclient.php" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left me-2"></i>Voir le menu
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="table-responsive">
                        <table class="table cart-table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Prix</th>
                                    <th>Quantité</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($plats as $plat): ?>
                                    <tr data-plat-id="<?= $plat['id'] ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="uploads/plats/<?= htmlspecialchars($plat['image']) ?>" 
                                                     alt="<?= htmlspecialchars($plat['nom']) ?>" 
                                                     class="cart-image me-3">
                                                <div>
                                                    <h6 class="mb-0"><?= htmlspecialchars($plat['nom']) ?></h6>
                                                    <small class="text-muted"><?= htmlspecialchars($plat['description']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="price"><?= number_format($plat['prix'], 2) ?> €</td>
                                        <td>
                                            <div class="quantity-control">
                                                <button class="quantity-btn minus" 
                                                        onclick="updateQuantity(<?= $plat['id'] ?>, -1)">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <span class="quantity"><?= $_SESSION['cart'][$plat['id']] ?></span>
                                                <button class="quantity-btn plus" 
                                                        onclick="updateQuantity(<?= $plat['id'] ?>, 1)">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="item-total fw-bold">
                                            <?= number_format($plat['prix'] * $_SESSION['cart'][$plat['id']], 2) ?> €
                                        </td>
                                        <td>
                                            <i class="bi bi-x-circle remove-item" 
                                               onclick="removeItem(<?= $plat['id'] ?>)"
                                               title="Supprimer"></i>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="cart-total">
                        <h4>Résumé de la commande</h4>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="h5 mb-0">Total</span>
                            <span class="total-amount cart-total-amount">
                                <?= number_format($total, 2) ?> €
                            </span>
                        </div>
                        <button onclick="window.location.href='commander.php'" 
                                class="btn btn-commander w-100">
                            <i class="bi bi-bag-check me-2"></i>Commander
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function updateQuantity(platId, change) {
            fetch('update_panier.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `plat_id=${platId}&change=${change}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const row = document.querySelector(`tr[data-plat-id="${platId}"]`);
                    const quantitySpan = row.querySelector('.quantity');
                    const itemTotal = row.querySelector('.item-total');
                    const priceCell = row.querySelector('.price');
                    const price = parseFloat(priceCell.textContent.replace('€', '').trim());
                    
                    // Update quantity
                    quantitySpan.textContent = data.itemQuantity;
                    
                    // Update item total
                    const newItemTotal = (price * data.itemQuantity).toFixed(2);
                    itemTotal.textContent = `${newItemTotal} €`;
                    
                    // Update cart total and badge
                    updateCartTotal();
                    updateCartBadge(data.cartCount);
                    
                    // Handle item removal
                    if (data.itemQuantity <= 0) {
                        row.remove();
                        if (data.cartCount === 0) {
                            location.reload();
                        }
                    }
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Une erreur est survenue');
            });
        }

        function removeItem(platId) {
            if (confirm('Voulez-vous vraiment supprimer cet article ?')) {
                fetch('update_panier.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `plat_id=${platId}&remove=1`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = document.querySelector(`tr[data-plat-id="${platId}"]`);
                        row.remove();
                        updateCartTotal();
                        updateCartBadge(data.cartCount);
                        
                        if (data.cartCount === 0) {
                            location.reload();
                        }
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Une erreur est survenue');
                });
            }
        }

        function updateCartTotal() {
            const itemTotals = Array.from(document.querySelectorAll('.item-total'))
                .map(el => parseFloat(el.textContent.replace('€', '').trim()));
            
            const newTotal = itemTotals.reduce((sum, price) => sum + price, 0).toFixed(2);
            document.querySelector('.cart-total-amount').textContent = `${newTotal} €`;
        }

        function updateCartBadge(count) {
            const badge = document.querySelector('.badge');
            if (badge) {
                badge.textContent = count;
                if (count === 0) {
                    badge.style.display = 'none';
                } else {
                    badge.style.display = 'inline';
                }
            }
        }

        function showError(message) {
            const toast = document.createElement('div');
            toast.className = 'alert alert-danger position-fixed bottom-0 end-0 m-3';
            toast.style.zIndex = '1000';
            toast.innerHTML = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
     
    function showNotification(message, type = 'error') {
        // Supprimer les notifications existantes
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());

        // Créer la nouvelle notification
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        // Ajouter l'icône appropriée
        const icon = type === 'error' ? 'bi-exclamation-circle' : 'bi-check-circle';
        notification.innerHTML = `
            <i class="bi ${icon}"></i>
            <span>${message}</span>
        `;

        document.body.appendChild(notification);

        // Animation d'entrée
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Supprimer après 3 secondes
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    function showError(message) {
        showNotification(message, 'error');
    }

    function showSuccess(message) {
        showNotification(message, 'success');
    }

    </script>
     <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>