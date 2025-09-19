<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=restaurant;charset=utf8', 'root', '');

// Récupération des infos du site
$site = $pdo->query("SELECT * FROM site_infos WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

// Récupération des catégories (images populaires)
$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.footer-custom {
    background: url('footer.png') center center / cover no-repeat;
    margin: 0; /* supprime les espaces extérieurs */
    padding-top: 30px;
    color: #fff;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Contenu principal du footer */
.footer-container {
    display: flex;
    justify-content: space-around;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}
.footer-col {
    flex: 1;
    min-width: 220px;
}
.footer-col h3 {
    color: #ff8000;
    font-size: 1.2em;
    margin-bottom: 12px;
    font-family: 'Georgia', serif;
}
.contact p {
    margin: 6px 0;
}
.contact i {
    color: #ff8000;
    margin-right: 6px;
}
.popular-items {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.popular-items img {
    width: 55px;
    height: 55px;
    object-fit: cover;
    border-radius: 6px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.newsletter-form {
    display: flex;
    margin-bottom: 10px;
}
.newsletter-form input {
    flex: 1;
    padding: 6px;
    border: 1px solid #ff8000;
    border-radius: 4px 0 0 4px;
    outline: none;
}
.newsletter-form button {
    padding: 6px 14px;
    background: #ff8000;
    color: #fff;
    border: none;
    border-radius: 0 4px 4px 0;
    cursor: pointer;
}
.newsletter-form button:hover {
    background: #ffb84d;
    color: #222;
}
.social-icons a {
    display: inline-block;
    width: 34px;
    height: 34px;
    background: #ff8000;
    color: #fff;
    border-radius: 4px;
    text-align: center;
    line-height: 34px;
    margin-right: 6px;
    font-size: 1.2em;
}
.social-icons a:hover {
    background: #ffb84d;
    color: #222;
}

/* Bande du bas */
.footer-bottom {
    border-top: 1px dashed #ffb84d;
    text-align: center;
    padding: 10px 0;
    font-size: 0.9em;
    background: #222; /* plus sobre */
    margin-top: 20px;
}
</style>

<footer class="footer-custom">
    <div class="footer-container">
        <!-- Contact -->
        <div class="footer-col contact">
            <h3>Contact Nous:</h3>
            <p style="color: #000;"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($site['adresse'] ?? '123 San Sebastian, NYC') ?></p>
            <p style="color: #000;"><i class="bi bi-telephone"></i> <?= htmlspecialchars($site['telephone'] ?? '+11 222 3333') ?></p>
            <p style="color: #000;"><i class="bi bi-envelope"></i> <?= htmlspecialchars($site['email'] ?? 'mail@example.com') ?></p>
        </div>

        <!-- Popular Items -->
        <div class="footer-col popular">
            <h3>Articles populaires</h3>
            <div class="popular-items">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $categorie): ?>
                        <img src="<?= htmlspecialchars('uploads/categories/' . $categorie['image']) ?>" 
                             alt="<?= htmlspecialchars($categorie['nom']) ?>">
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No items available.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="footer-col newsletter">
            <h3>Bulletin d'information</h3>
            <form class="newsletter-form" method="post" action="newsletter.php">
                <input type="email" name="email" placeholder="Email" required>
                <button type="submit">aller</button>
            </form>
            <div class="social-icons">
                <a href="#"><i class="bi bi-instagram"></i></a>
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-google"></i></a>
               
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Restaurant Hassijerbi.</p>
    </div>
</footer>
