
<?php
session_start();
try {
    $pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/logo1.png" sizes="200x128" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-o8C3D5NgW/EYKHZsnHkzDa4Z7p0NzX0bQrtrURVn9Y+ztg9Y4M7Yc6JNVdvYkZB+XAmflzNz2ZsE1vK10vxAcQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"></style>

    <style>
        
       
        .map-section {
            padding: 60px 0;
            background-color: #f8f9fa;
        }

        .map-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .map-title {
            font-size: 2rem;
            font-weight: 600;
            color:rgb(253, 209, 13);
            text-align: center;
            margin-bottom: 30px;
        }

        .map-frame {
            width: 100%;
            height: 500px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            height: 400px;
        }
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('images/restaurant-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
            margin-bottom: 50px;
        }

        .categories-section {
            padding: 50px 0;
            background-color: #f8f9fa;
        }

        .category-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .category-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .category-card .card-body {
            padding: 20px;
            text-align: center;
            background: white;
        }

        .category-card .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .view-menu-btn {
            background-color: #ffc107;
            color: #000;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            display: inline-block;
            margin-top: 10px;
        }

        .view-menu-btn:hover {
            background-color: #ffb300;
            color: #000;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
            color: #2c3e50;
            position: relative;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background-color: #ffc107;
        }
       
        
    </style>
</head>
<body>

    <?php include __DIR__ . '/../includes/navbar.php'; ?>

<section class="map-section">
    <div class="map-container">
        <h2 class="map-title">Notre localisation</h2>
        <div class="map-frame">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d53153.82856138729!2d10.982571522919693!3d33.62827861935028!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x13aaebab42ed5d2b%3A0x631fde8b0054b4fb!2sHassi%20El%20Jerbi!5e0!3m2!1sfr!2stn!4v1746010571059!5m2!1sfr!2stn" width="1200" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>                  
              
            </iframe>
        </div>
    </div>
</section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>