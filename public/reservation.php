<?php
session_start();

$erreur = null;
$succes = null;

// Validate and process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Database connection
        $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Validate inputs
        $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_NUMBER_INT);
        $date_reservation = filter_input(INPUT_POST, 'date_reservation', FILTER_SANITIZE_STRING);
        $heure = filter_input(INPUT_POST, 'heure', FILTER_SANITIZE_STRING);
        $nombre_personnes = filter_input(INPUT_POST, 'nombre_personnes', FILTER_VALIDATE_INT);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Additional validations
        if (!$nom || !$telephone || !$date_reservation || !$heure || !$nombre_personnes) {
            throw new Exception("Veuillez remplir tous les champs obligatoires");
        }

        // Validate date (must be in the future)
        $date_time_reservation = new DateTime("$date_reservation $heure");
        $now = new DateTime();

        if ($date_time_reservation <= $now) {
            throw new Exception("La date de réservation doit être dans le futur");
        }

        // Check if the chosen time is within restaurant hours (example: 11:00 to 23:00)
        $heure_parts = explode(':', $heure);
        $heure_int = intval($heure_parts[0]);
        
        if ($heure_int < 11 || $heure_int >= 23) {
            throw new Exception("Les réservations sont possibles entre 11h et 23h");
        }

        // Check table availability (example: max 50 people per time slot)
        $stmt = $pdo->prepare("
            SELECT SUM(nombre_personnes) as total_personnes
            FROM reservations
            WHERE date_reservation = ? 
            AND heure = ?
            AND statut = 'confirmee'
        ");
        $stmt->execute([$date_reservation, $heure]);
        $result = $stmt->fetch();

        $total_personnes = $result['total_personnes'] ?? 0;
        if (($total_personnes + $nombre_personnes) > 50) {
            throw new Exception("Désolé, nous n'avons plus de place disponible pour ce créneau horaire");
        }

        // Insert reservation
        $stmt = $pdo->prepare("
            INSERT INTO reservations (
                nom, telephone, date_reservation, heure, 
                nombre_personnes, message, statut
            ) VALUES (?, ?, ?, ?, ?, ?, 'en_attente')
        ");

        $stmt->execute([
            $nom,
            $telephone,
            $date_reservation,
            $heure,
            $nombre_personnes,
            $message ?? null
        ]);

        $succes = "Votre réservation a été enregistrée avec succès! Nous vous contacterons pour la confirmation.";

        // Clear form after successful submission
        $_POST = [];

    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation</title>
    <style>
    body {
    font-family: "Segoe UI", Roboto, sans-serif;
    margin: 0;
    padding: 0;
}

.reservation-card {
    background: linear-gradient(135deg,  #c9bfbfff,#ff7f00);
    border-radius: 18px;
    padding: 40px 32px;
    max-width: 650px;
    margin: 60px auto;
    box-shadow: 0 8px 28px rgba(0,0,0,0.3);
    color: #fff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.reservation-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 36px rgba(0,0,0,0.5);
}

.reservation-card h2 {
    color: #ff5c43;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 28px;
    text-align: center;
}

.form-label {
    color: #ddd;
    font-weight: 500;
    margin-bottom: 6px;
    display: block;
}

.form-control {
    background: #2f2f2f;
    border-radius: 12px;
    border: 1px solid #444;
    font-size: 1em;
    color: #eee;
    margin-bottom: 20px;
    padding: 14px 18px;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.form-control:focus {
    box-shadow: 0 0 0 3px rgba(255, 92, 67, 0.3);
    border-color: #ff5c43;
    outline: none;
}

.btn-reserver {
    background: linear-gradient(135deg, #ff5c43, #ff784e);
    color: #fff;
    font-weight: 600;
    font-size: 1.2em;
    border-radius: 14px;
    padding: 16px 0;
    width: 100%;
    border: none;
    margin-top: 12px;
    transition: background 0.3s, transform 0.2s;
    cursor: pointer;
}
.btn-reserver:hover {
    background: linear-gradient(135deg, #ff784e, #ff5c43);
    transform: translateY(-2px);
}

.alert {
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 20px;
    font-size: 0.95em;
}

@media (max-width: 768px) {
    .reservation-card {
        padding: 24px 18px;
        margin: 30px 15px;
    }
}

    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container mb-5">
    <div class="reservation-card">
            <?php if ($erreur): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?= htmlspecialchars($erreur) ?>
                </div>
            <?php endif; ?>

            <?php if ($succes): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    <?= htmlspecialchars($succes) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" 
                           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control" id="telephone" name="telephone" 
                           value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="date_reservation" class="form-label">Date de réservation</label>
                    <input type="date" class="form-control" id="date_reservation" name="date_reservation" 
                           value="<?= htmlspecialchars($_POST['date_reservation'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="heure" class="form-label">Heure</label>
                    <input type="time" class="form-control" id="heure" name="heure" 
                           value="<?= htmlspecialchars($_POST['heure'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nombre_personnes" class="form-label">Nombre de personnes</label>
                    <input type="number" class="form-control" id="nombre_personnes" name="nombre_personnes" 
                           value="<?= htmlspecialchars($_POST['nombre_personnes'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message (optionnel)</label>
                    <textarea class="form-control" id="message" name="message"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
<button class="btn-reserver">Réserver</button>

        </form>
        </div>
    </div>

    <script>
        // Set minimum date to today
        const dateInput = document.getElementById('date_reservation');
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
        
        // Set default date to today
        if (!dateInput.value) {
            dateInput.value = today;
        }
    </script>
  <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>