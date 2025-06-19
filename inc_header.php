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
<div class="header" style="display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;background:linear-gradient(135deg,#e0e7ff 0%,#f4f4f4 100%);padding:18px 20px 18px 20px;box-sizing:border-box;">
    <div style="display:flex;align-items:center;gap:18px;min-width:0;">
        <img src="<?php echo $logoPath; ?>" alt="Logo du site" style="max-width:110px;min-width:70px;width:18vw;min-height:70px;max-height:110px;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.07);background:#f2f6ff;">
        <?php if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])): ?>
            <span style="font-size:1.25em;font-weight:600;color:#2d3a4b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:40vw;">Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom'].' '.$_SESSION['nom']); ?></span>
        <?php endif; ?>
    </div>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <button class="burger" onclick="document.querySelector('.global-menu').classList.toggle('open')">☰</button>
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
<style>
@media (max-width: 900px) {
    .header {flex-direction: column;align-items: flex-start !important;gap: 10px !important;}
    .header img {max-width: 80px !important;min-width: 50px !important;}
    .header span {font-size: 1.1em !important;max-width: 90vw !important;}
    .header .btn, .header form {margin-top: 8px;}
}
@media (max-width: 600px) {
    .header {padding: 10px 4vw 10px 4vw !important;}
    .header img {max-width: 60px !important;min-width: 40px !important;}
    .header span {font-size: 1em !important;}
}
</style>
