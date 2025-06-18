<?php
// Page de gestion du logo pour l'organisateur
session_start();
include 'fonctionsPHP.php';
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}
$idOrga = $_SESSION['id'];
$logoPath = 'bgsharklo.jpg';
$customLogo = './images/logo_' . $idOrga . '.jpg';
if (file_exists($customLogo)) {
    $logoPath = $customLogo;
}
// Upload d'un nouveau logo
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['logo']['tmp_name'];
    $fileType = mime_content_type($fileTmp);
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (in_array($fileType, $allowed)) {
        $ext = $fileType === 'image/png' ? 'png' : ($fileType === 'image/webp' ? 'webp' : 'jpg');
        $dest = './images/logo_' . $idOrga . '.' . $ext;
        move_uploaded_file($fileTmp, $dest);
        // Supprimer les anciens logos si extension différente
        foreach(['jpg','png','webp'] as $e) {
            $old = './images/logo_' . $idOrga . '.' . $e;
            if ($old !== $dest && file_exists($old)) unlink($old);
        }
        header('Location: gestionLogo.php');
        exit();
    } else {
        $msg = 'Format de logo non supporté.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Gestion du logo</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="<?php echo $logoPath; ?>" alt="Logo actuel" style="max-width:200px;">
        </div>
        <h1>Gestion du logo de votre espace</h1>
        <?php if (isset($msg)) echo '<p class="erreur">'.$msg.'</p>'; ?>
        <form method="post" enctype="multipart/form-data">
            <label for="logo">Nouveau logo (jpg, png, webp) :</label>
            <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" required>
            <input type="submit" value="Mettre à jour le logo">
        </form>
        <p>Le logo personnalisé s'affichera sur toutes vos pages. Si aucun logo n'est défini, le logo par défaut sera utilisé.</p>
        <a href="dashboard.php">Retour au dashboard</a>
    </div>
</body>
</html>
