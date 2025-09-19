<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Restaurant Management System" />
    <meta name="author" content="Your Name" />
    <title>Restaurant</title>
    <link rel="icon" href="{{ asset('images/restaurant_logo.png') }}" sizes="200x128" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/styles.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous">
    <style>
        .nav-link {
            color: white;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: #28a745; /* Green hover color */
        }
        .nav-item .nav-link.active {
            color: #28a745; /* Green for the active link */
            border-bottom: 2px solid #28a745; /* Optional: adds an underline */
        }
        .navbar-brand img {
            height: 40px;
            border-radius: 6px;
        }
        /* Custom styles for the dropdown menu */
        .user-dropdown {
            margin-left: auto; /* Pushes the dropdown to the right */
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="accueil.php">
            <img src="images/logo.jpg" alt="Restaurant Logo" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="accueil.php"><i class="fas fa-home"></i> Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="menuadmin.php"><i class="fas fa-utensils"></i> Plat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categoriesadmin.php"><i class="fas fa-edit"></i> Catégorie</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reservationsadmin.php"><i class="fas fa-calendar-alt"></i> Réservations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="commandes.php"><i class="fas fa-receipt"></i> Commandes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="listeclients.php"><i class="fas fa-users"></i> Clients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="listecontact.php"><i class="fas fa-envelope"></i> Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="inforamtion.php"><i class="fas fa-info-circle"></i> Information</a>
                </li>
            </ul>
            <div class="dropdown">
                
                        <form action="logoutadmin.php" method="post" class="m-0">
                            <button class="btn btn-warning" type="submit" class="dropdown-item text-danger">Déconnexion</button>
                        </form>
                    
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/scripts.js"></script>
</body>
</html>