<?php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    $plat_id = intval($_POST['plat_id']);
    
    if (!$plat_id) {
        throw new Exception('ID du plat invalide');
    }
    
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Verify if plat exists
    $stmt = $pdo->prepare("SELECT * FROM plats WHERE id = ?");
    $stmt->execute([$plat_id]);
    $plat = $stmt->fetch();
    
    if (!$plat) {
        throw new Exception('Plat non trouvé');
    }
    
    // Initialize cart if needed
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Add to cart
    if (isset($_SESSION['cart'][$plat_id])) {
        $_SESSION['cart'][$plat_id]++;
    } else {
        $_SESSION['cart'][$plat_id] = 1;
    }
    
    $cartCount = array_sum($_SESSION['cart']);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Plat ajouté au panier',
        'cartCount' => $cartCount
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}