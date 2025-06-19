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
<div class="header" style="display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:15px;">
        <img src="<?php echo $logoPath; ?>" alt="Logo du site">
        <?php if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])): ?>
            <span style="font-size:1.1em;font-weight:600;color:#2d3a4b;">Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom'].' '.$_SESSION['nom']); ?></span>
        <?php endif; ?>
    </div>
    <button class="burger" onclick="document.querySelector('.global-menu').classList.toggle('open')">☰</button>
    <div style="display:flex;align-items:center;gap:10px;">
        <?php if ($showRetour): ?>
            <a href="dashboard.php" class="btn" style="padding:7px 16px;font-size:0.98em;">&larr; Retour au dashboard</a>
        <?php endif; ?>
        <?php if ($current === 'dashboard.php'): ?>
            <form action="logout.php" method="post" style="display:inline;">
                <input type="submit" value="Déconnexion" class="btn" style="padding:7px 16px;font-size:0.98em;">
            </form>
        <?php endif; ?>
    </div>
</div>
