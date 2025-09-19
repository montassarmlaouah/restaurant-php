<?php
session_start();
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['client_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Check if commande_id is provided
if (!isset($_POST['commande_id']) || !is_numeric($_POST['commande_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de commande invalide']);
    exit();
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $commande_id = intval($_POST['commande_id']);

    // Verify order belongs to user and is cancellable
    $stmt = $pdo->prepare("
        SELECT id FROM commandes 
        WHERE id = ? AND client_id = ? AND statut = 'en_attente'
    ");
    $stmt->execute([$commande_id, $_SESSION['client_id']]);

    if (!$stmt->fetch()) {
        echo json_encode([
            'success' => false, 
            'message' => 'Commande non trouvée ou ne peut pas être annulée'
        ]);
        exit();
    }

    // Cancel order
    $stmt = $pdo->prepare("UPDATE commandes SET statut = 'annulee' WHERE id = ?");
    $stmt->execute([$commande_id]);

    echo json_encode(['success' => true, 'message' => 'Commande annulée avec succès']);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur lors de l\'annulation de la commande'
    ]);
}