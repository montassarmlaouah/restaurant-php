<?php

session_start();



try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $reservations = $pdo->query("SELECT * FROM reservations ORDER BY date_reservation DESC, heure DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réservations - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .reservations-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/restaurant-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            animation: fadeIn 1s ease-in;
        }

        .reservation-card {
            transition: all 0.3s ease;
            animation: slideUp 0.5s ease-out;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }

        .reservation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table > :not(caption) > * > * {
            padding: 1rem;
        }

        .reservation-date {
            color: #0d6efd;
            font-weight: 500;
        }

        .reservation-time {
            color: #6c757d;
            font-weight: 500;
        }

        .reservation-persons {
            background: #e9ecef;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="bg-light">
    <?php include __DIR__ . '/../includes/navbaradmin.php'; ?>

    <div class="reservations-header text-center"style="background: linear-gradient(rgba(42, 42, 228, 0.93), rgba(0,0,128,0.7)), url('images/restaurant-bg.jpg'); background-size: cover; background-position: center; color: white; padding: 6rem 0; margin-bottom: 3rem;">
        <h1 class="display-4">Gestion des Réservations</h1>
        <p class="lead">Suivi des réservations du restaurant</p>
    </div>

    <div class="container mb-5">
        <?php if (empty($reservations)): ?>
            <div class="text-center py-5">
                <i class="bi bi-calendar2-x fs-1 text-muted"></i>
                <h2 class="mt-3">Aucune réservation</h2>
                <p class="text-muted">Il n'y a pas encore de réservations à afficher</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Client</th>
                                <th>Contact</th>
                                <th>Réservation</th>
                                <th>Personnes</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $res): ?>
                                <tr class="reservation-card">
                                    <td>
                                        <strong><?= htmlspecialchars($res['nom']) ?></strong>
                                    </td>
                                    <td>
                                        <i class="bi bi-telephone me-2"></i>
                                        <?= htmlspecialchars($res['telephone']) ?>
                                    </td>
                                    <td>
                                        <span class="reservation-date">
                                            <?= date('d/m/Y', strtotime($res['date_reservation'])) ?>
                                        </span>
                                        <br>
                                        <span class="reservation-time">
                                            <?= date('H:i', strtotime($res['heure'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="reservation-persons">
                                            <i class="bi bi-people me-1"></i>
                                            <?= $res['nombre_personnes'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($res['message']): ?>
                                            <button class="btn btn-sm btn-outline-info" 
                                                    data-bs-toggle="tooltip" 
                                                    title="<?= htmlspecialchars($res['message']) ?>">
                                                <i class="bi bi-chat-text"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                   
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body>
</html>