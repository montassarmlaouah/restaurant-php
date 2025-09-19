<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="icon" href="images/logo1.png" sizes="200x128" type="image/png">

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css"></style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
        }
        .services-section {
            background: #fff;
            padding: 60px 20px;
            margin: 40px auto;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0,0,0,0.1);
        }
        .services-section h2 {
            font-family: 'Cursive', sans-serif;
            text-align: center;
            margin-bottom: 15px;
        }
        .services-section p.sub-text {
            text-align: center;
            color: #666;
            margin-bottom: 50px;
        }
        .service-box {
            text-align: center;
            padding: 20px;
        }
        .service-box i {
            font-size: 40px;
            color: #f1c40f;
            margin-bottom: 15px;
        }
        .service-box h5 {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .offer-section {
            text-align: center;
            margin-top: 40px;
        }
        .offer-section p {
            color: #666;
        }
        .offer-section button {
            margin-top: 20px;
            background: black;
            color: white;
            border: none;
            padding: 10px 20px;
            transition: 0.3s;
        }
        .offer-section button:hover {
            background: #f1c40f;
            color: black;
        }
        .gallery img {
            border-radius: 10px;
            width: 100%;
            height: auto;
            object-fit: cover;
        }
    </style>
</head>
<body>
     <?php include __DIR__ . '/../includes/navbar.php'; ?>

<!-- SECTION SERVICES -->
<section class="services-section container">
  <h2>Nos Services</h2>
  <p class="sub-text">Découvrez notre gamme de services pour satisfaire toutes vos envies culinaires.</p>

  <div class="row text-center">
    <div class="col-md-3 service-box">
      <i class="fa-solid fa-list"></i>
      <h5>Grand Choix</h5>
      <p>Un large éventail de plats pour tous les goûts.</p>
    </div>
    <div class="col-md-3 service-box">
      <i class="fa-solid fa-pizza-slice"></i>
      <h5>Spécialités Italiennes</h5>
      <p>Pizzas et pâtes préparées selon la tradition italienne.</p>
    </div>
    <div class="col-md-3 service-box">
      <i class="fa-solid fa-burger"></i>
      <h5>Burgers Savoureux</h5>
      <p>Burgers gourmands et généreux, faits maison.</p>
    </div>
    <div class="col-md-3 service-box">
      <i class="fa-solid fa-bowl-rice"></i>
      <h5>Pâtes Exquises</h5>
      <p>Pâtes fraîches et sauces raffinées pour tous les palais.</p>
    </div>
  </div>

  <div class="row text-center mt-4">
    <div class="col-md-3 service-box">
      <i class="fa-solid fa-truck"></i>
      <h5>Livraison Rapide</h5>
      <p>Profitez de notre service de livraison efficace.</p>
    </div>
    <div class="col-md-3 service-box">
      <i class="fa-solid fa-ice-cream"></i>
      <h5>Desserts Gourmands</h5>
      <p>Des desserts faits maison pour finir sur une note sucrée.</p>
    </div>
    <div class="col-md-3 service-box">
      <i class="fa-solid fa-user-chef"></i>
      <h5>Chefs Talentueux</h5>
      <p>Notre équipe de chefs passionnés vous régale chaque jour.</p>
    </div>
    <div class="col-md-3 service-box">
      <i class="fa-solid fa-wine-glass"></i>
      <h5>Vins Délicieux</h5>
      <p>Une sélection de vins pour accompagner vos repas.</p>
    </div>
  </div>

  <!-- GALERIE D'IMAGES -->
  <div class="row gallery mt-5">
    <div class="col-md-4">
      <img src="images/dessert.jpg" alt="Dessert" style="width:300px; height:300px; object-fit:cover;">
    </div>
    <div class="col-md-4">
      <img src="images/burger.jpg" alt="Burger" style="width:300px; height:300px; object-fit:cover;">
    </div>
    <div class="col-md-4">
      <img src="images/restaurant.jpg" alt="Restaurant" style="width:300px; height:300px; object-fit:cover;">
    </div>
  </div>

  <div class="offer-section mt-5">
    <h3>CE QUE NOUS OFFRONS</h3>
    <p>Nous vous proposons une expérience culinaire unique, adaptée à toutes vos envies.<br> Découvrez notre menu complet et laissez-vous tenter par nos spécialités.</p>
   
       <div class="hero-btns">
<a href="http://localhost/hassijerbi/menu/menu.html" class="btn btn-warning">VOIR LE MENU COMPLET</a>
</div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>