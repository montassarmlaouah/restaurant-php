

<?php
        // Initialize session globally if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Ensure session keys exist to avoid undefined array key warnings
        if (!isset($_SESSION['client_id'])) {
            $_SESSION['client_id'] = null;
        }
        if (!isset($_SESSION['client_prenom'])) {
            $_SESSION['client_prenom'] = null;
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Separate session handling for cart and user account with error handling
        try {
            $cartItems = is_array($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
            $isLoggedIn = (!empty($_SESSION['client_id'])) ? true : false;
            $clientPrenom = ($isLoggedIn && !empty($_SESSION['client_prenom'])) ? htmlspecialchars($_SESSION['client_prenom']) : null;
        } catch (Throwable $e) {
            $cartItems = 0;
            $isLoggedIn = false;
            $clientPrenom = null;
            // Optionally log the error: error_log($e->getMessage());
        }
       
       
       ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fresh Style Navbar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    .navbar {
      background-color: #f8fdf8; /* très clair */
      padding: 0.8rem 1rem;
    }
    .navbar-brand {
      font-weight: bold;
      font-size: 1.3rem;
      color: #f4511e !important; /* Orange comme "FRESH" */
    }
    .navbar-brand img {
      height: 40px;
      margin-right: 5px;
    }
    .navbar-nav .nav-link {
      color: #666 !important;
      font-weight: 500;
      margin: 0 10px;
    }
    .navbar-nav .nav-link.active {
      border-bottom: 2px solid #f4511e;
      color: #000 !important;
    }
    .search-input {
      border: 1px solid #ccc;
      border-radius: 20px;
      padding: 6px 15px;
      margin-right: 10px;
      width: 180px;
    }
    .btn-cart {
      background-color: #ffc107; /* jaune rond */
      border: none;
      border-radius: 50%;
      width: 42px;
      height: 42px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 10px;
    }
    .btn-cart i {
      font-size: 1.2rem;
      color: white;
    }
    .btn-signup {
      background-color: #2e7d32; /* vert foncé */
      border: none;
      color: #fff;
      font-weight: bold;
      padding: 8px 18px;
      border-radius: 6px;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg shadow-sm">
  <div class="container">
    <!-- Logo + Titre -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="images/logo.jpg" alt="Logo">
      FRESH
    </a>

    <!-- Bouton mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link active" href="index.php" >Accueil</a></li>
        <li class="nav-item"><a class="nav-link" href="apropos.php">À propos</a></li>
        <li class="nav-item"><a class="nav-link"  href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
      </ul>
    </div>
    
    <?php if ($isLoggedIn): ?>
      <a href="mes-commandes.php" class="btn btn-warning ms-2">Mes Commandes</a>
      <div class="dropdown ms-2">
        <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
          Profil (<?php echo $clientPrenom; ?>)
        </a>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
          <li><a class="dropdown-item" href="mon-compte.php">Mon Compte</a></li>
          <li>
            <form action="logout.php" method="post" style="margin:0;">
              <button type="submit" class="dropdown-item text-danger">Déconnexion</button>
            </form>
          </li>
        </ul>
      </div>
    <?php else: ?>
      <a href="connexionclient.php" class="btn btn-primary ms-2">Connexion</a>
      <a href="inscriptionclient.php" class="btn btn-signup ms-2">Inscription</a>
    <?php endif; ?>
    <button type="button" class="btn-cart position-relative ms-2" onclick="window.location.href='panier.php'">
      <i class="bi bi-cart-fill"></i>
      <?php if ($cartItems > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
          <?php echo $cartItems; ?>
        </span>
      <?php endif; ?>
    </button>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
