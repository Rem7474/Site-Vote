<?php
// Inclusion à placer en haut de chaque page principale
if (session_status() === PHP_SESSION_NONE) session_start();
include_once 'fonctionsPHP.php';
// Détermination du logo à afficher
$logoPath = 'bgsharklo.jpg';
$orgaId = null;
if (isset($_SESSION['id'])) {
    $orgaId = $_SESSION['id'];
} elseif (isset($event) && isset($event['reforga'])) {
    $orgaId = $event['reforga'];
}
if ($orgaId) {
    foreach(['jpg','png','webp'] as $ext) {
        $customLogo = './images/logo_' . $orgaId . '.' . $ext;
        if (file_exists($customLogo)) {
            $logoPath = $customLogo;
            break;
        }
    }
}
// Affichage du bouton retour uniquement si on n'est PAS sur le dashboard
$current = basename($_SERVER['PHP_SELF']);
$showRetour = $current !== 'dashboard.php';
?>
<?php printFaviconTag(); addDarkModeScript(); ?>
<div class="header">
    <img src="<?php echo $logoPath; ?>" alt="Logo du site">
    <?php if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])): ?>
        <span>Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom'].' '.$_SESSION['nom']); ?></span>
    <?php endif; ?>
    <div class="header-actions">
        <button class="burger" onclick="document.querySelector('.global-menu').classList.toggle('open')">☰</button>
        <?php if ($showRetour): ?>
            <a href="dashboard.php" class="btn">&larr; Retour au dashboard</a>
        <?php endif; ?>
        <?php if ($current === 'dashboard.php'): ?>
            <form action="logout.php" method="post" style="display:inline;">
                <input type="submit" value="Déconnexion" class="btn">
            </form>
        <?php endif; ?>
    </div>
    <div class="header-theme-btn">
        <button class="btn" onclick="toggleDarkMode()">🌓 Thème sombre</button>
    </div>
    <script>
    // Burger menu mobile : ouverture/fermeture
    (function() {
        var burger = document.querySelector('.burger');
        var menu = document.querySelector('.global-menu');
        if (burger && menu) {
            burger.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('open');
            });
            document.addEventListener('click', function(e) {
                if (!menu.contains(e.target) && !burger.contains(e.target)) {
                    menu.classList.remove('open');
                }
            });
        }
    })();
    </script>
</div>
<style>
@media (max-width: 900px) {
    .header {flex-direction: column !important;align-items: center !important;gap: 10px !important;}
    .header img {max-width: 80px !important;min-width: 50px !important;}
    .header span {font-size: 1.1em !important;max-width: 90vw !important;text-align: center;}
    .header .btn, .header form {margin-top: 8px;}
    .burger { display: inline-block !important; }
}
@media (min-width: 901px) {
    .burger { display: none !important; }
}
@media (max-width: 600px) {
    .header {padding: 10px 4vw 10px 4vw !important;}
    .header img {max-width: 60px !important;min-width: 40px !important;}
    .header span {font-size: 1em !important;max-width: 98vw !important;text-align: center;}
}
</style>
