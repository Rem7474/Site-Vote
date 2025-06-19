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
<div class="header">
    <img src="<?php echo $logoPath; ?>" alt="Logo du site">
    <?php if ($showRetour): ?>
    <div style="margin-top:10px;">
        <a href="dashboard.php" style="color:#3b6eea;text-decoration:underline;font-size:0.98em;">&larr; Retour au dashboard</a>
    </div>
    <?php endif; ?>
</div>
