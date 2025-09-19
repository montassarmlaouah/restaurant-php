<?php



try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Récupérer tous les messages de contact avec la nouvelle structure
    $stmt = $pdo->query("
        SELECT id, nom, email, message, date_envoi, statut 
        FROM contacts 
        ORDER BY date_envoi DESC
    ");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Messages de Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .messages-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/restaurant-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .message-card {
            transition: all 0.3s ease;
        }
        .message-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .status-new {
            border-left: 4px solid #0d6efd;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbaradmin.php'; ?>

    <div class="messages-header text-center"style="background: linear-gradient(rgba(42, 42, 228, 0.93), rgba(0,0,128,0.7)), url('images/restaurant-bg.jpg'); background-size: cover; background-position: center; color: white; padding: 6rem 0; margin-bottom: 3rem;">
        <h1 class="display-4">Messages de Contact</h1>
        <p class="lead">Gestion des messages reçus</p>
    </div>

    <div class="container mb-5">
        <?php if (empty($messages)): ?>
            <div class="text-center py-5">
                <i class="bi bi-envelope fs-1 text-muted"></i>
                <h2 class="mt-3">Aucun message</h2>
                <p class="text-muted">Vous n'avez pas encore reçu de messages</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Statut</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                            <tr class="<?= $message['statut'] === 'nouveau' ? 'table-info' : '' ?>">
                                <td><?= date('d/m/Y H:i', strtotime($message['date_envoi'])) ?></td>
                                <td><?= htmlspecialchars($message['nom']) ?></td>
                                <td><?= htmlspecialchars($message['email']) ?></td>
                                <td><?= htmlspecialchars(substr($message['message'], 0, 50)) ?>...</td>
                                <td>
                                    <span class="badge bg-<?= $message['statut'] === 'nouveau' ? 'primary' : 'secondary' ?>">
                                        <?= ucfirst($message['statut']) ?>
                                    </span>
                                </td>
                                
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>