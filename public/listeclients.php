<?php
session_start();



try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Récupérer tous les clients
    $stmt = $pdo->query("SELECT * FROM clients ORDER BY date_inscription DESC");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Clients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .client-list {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .table-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .client-row:hover {
            background-color: #f8f9fa;
        }
        .page-title {
            color: #2c3e50;
            text-align: center;
            margin: 40px 0;
            padding-bottom: 15px;
            border-bottom: 2px solid #3498db;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbaradmin.php'; ?>
<div class="messages-header text-center"style="background: linear-gradient(rgba(42, 42, 228, 0.93), rgba(0,0,128,0.7)), url('images/restaurant-bg.jpg'); background-size: cover; background-position: center; color: white; padding: 6rem 0; margin-bottom: 3rem;">
        <h1 class="display-4">Liste des Clients</h1>
       
    </div>
    
        <div class="client-list">
            <?php if (empty($clients)): ?>
                <div class="alert alert-info text-center">
                    Aucun client enregistré.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-header">
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Date d'inscription</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <tr class="client-row">
                                    <td><?= htmlspecialchars($client['nom']) ?></td>
                                    <td><?= htmlspecialchars($client['prenom']) ?></td>
                                    <td><?= htmlspecialchars($client['email']) ?></td>
                                    <td><?= htmlspecialchars($client['telephone']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($client['date_inscription'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>