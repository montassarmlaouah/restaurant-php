<?php
session_start();

// Check if cart is not empty
if (empty($_SESSION['cart'])) {
    header('Location: panier.php');
    exit();
}

// Location options
$locations = [
    'restaurant' => 'Au restaurant',
    'domicile' => 'Livraison à domicile',
    'emporter' => 'À emporter'
];

// Process location selection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['location'])) {
        $error = "Veuillez choisir un mode de livraison";
    } else {
        $_SESSION['order_location'] = $_POST['location'];
        if ($_POST['location'] === 'domicile') {
            if (empty($_POST['adresse'])) {
                $error = "L'adresse de livraison est requise";
            } else {
                $_SESSION['delivery_address'] = $_POST['adresse'];
                header('Location: paiement.php');
                exit();
            }
        } else {
            header('Location: paiement.php');
            exit();
        }
    }
}

try {
    // ...existing database connection code...

    // Get cart items
    $plats = [];
    $total = 0;
    if (!empty($_SESSION['cart'])) {
        // ...existing cart retrieval code...
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
    <title>Choisir la livraison - Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .location-option {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .location-option:hover {
            border-color: #ffc107;
            transform: translateY(-2px);
        }

        .location-option.selected {
            border-color: #28a745;
            background-color: #f8fff9;
        }

        .location-option i {
            font-size: 2rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }

        .delivery-address {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .delivery-address.show {
            display: block;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container my-5">
        <h2 class="mb-4">Choisissez votre mode de livraison</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="locationForm">
            <div class="row">
                <?php foreach ($locations as $key => $label): ?>
                    <div class="col-md-4">
                        <div class="location-option" onclick="selectLocation('<?= $key ?>')">
                            <input type="radio" name="location" value="<?= $key ?>" 
                                   id="<?= $key ?>" class="d-none" 
                                   <?= isset($_POST['location']) && $_POST['location'] === $key ? 'checked' : '' ?>>
                            <div class="text-center">
                                <i class="bi bi-<?= $key === 'restaurant' ? 'shop' : 
                                                 ($key === 'domicile' ? 'house' : 'box-seam') ?>"></i>
                                <h4><?= htmlspecialchars($label) ?></h4>
                                <p class="text-muted mb-0">
                                    <?php
                                    switch($key) {
                                        case 'restaurant':
                                            echo "Profitez de votre repas sur place";
                                            break;
                                        case 'domicile':
                                            echo "Livraison à l'adresse de votre choix";
                                            break;
                                        case 'emporter':
                                            echo "Récupérez votre commande au restaurant";
                                            break;
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="deliveryAddress" class="delivery-address">
                <h4 class="mb-3">Adresse de livraison</h4>
                <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse complète</label>
                    <textarea class="form-control" id="adresse" name="adresse" rows="3"
                              placeholder="Numéro, rue, code postal, ville"><?= isset($_POST['adresse']) ? htmlspecialchars($_POST['adresse']) : '' ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    Continuer vers le paiement
                </button>
            </div>
        </form>
    </div>

    <script>
        function selectLocation(location) {
            // Remove selected class from all options
            document.querySelectorAll('.location-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            const selectedOption = document.querySelector(`#${location}`).closest('.location-option');
            selectedOption.classList.add('selected');
            
            // Check the radio button
            document.querySelector(`#${location}`).checked = true;
            
            // Show/hide delivery address form
            const deliveryAddress = document.getElementById('deliveryAddress');
            if (location === 'domicile') {
                deliveryAddress.classList.add('show');
            } else {
                deliveryAddress.classList.remove('show');
            }
        }

        // Initialize selected location if form was submitted
        window.onload = function() {
            const selectedLocation = document.querySelector('input[name="location"]:checked');
            if (selectedLocation) {
                selectLocation(selectedLocation.value);
            }
        }
    </script>

</body>
</html>