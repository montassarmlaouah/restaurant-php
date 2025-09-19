<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    $plat_id = intval($_POST['plat_id']);
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_POST['remove'])) {
        unset($_SESSION['cart'][$plat_id]);
        $itemQuantity = 0;
    } else {
        $change = intval($_POST['change']);
        
        if (!isset($_SESSION['cart'][$plat_id])) {
            $_SESSION['cart'][$plat_id] = 0;
        }

        $_SESSION['cart'][$plat_id] += $change;

        if ($_SESSION['cart'][$plat_id] <= 0) {
            unset($_SESSION['cart'][$plat_id]);
            $itemQuantity = 0;
        } else {
            $itemQuantity = $_SESSION['cart'][$plat_id];
        }
    }

    $cartCount = array_sum($_SESSION['cart']);
    
    echo json_encode([
        'success' => true,
        'cartCount' => $cartCount,
        'itemQuantity' => $itemQuantity
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
<script>
    function updateQuantity(platId, change) {
        fetch('upadate_panier.php', {  // Changed from update_cart.php to upadate_panier.php
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `plat_id=${platId}&change=${change}`
        })
        // ...existing code...
    }

    function removeItem(platId) {
        if (confirm('Voulez-vous vraiment supprimer cet article ?')) {
            fetch('upadate_panier.php', {  // Changed from update_cart.php to upadate_panier.php
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `plat_id=${platId}&remove=1`
            })
            // ...existing code...
        }
    }
    // ...existing code...
</script>