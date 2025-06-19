<?php
session_start();
if(!isset($_SESSION['id'])){
    header('location:login.php');
}
include 'fonctionsPHP.php';
// Gestion du logo personnalisé
$idOrga = $_SESSION['id'];
$logoPath = 'bgsharklo.jpg';
foreach(['jpg','png','webp'] as $ext) {
    $customLogo = './images/logo_' . $idOrga . '.' . $ext;
    if (file_exists($customLogo)) {
        $logoPath = $customLogo;
        break;
    }
}
// Gestion du changement de logo
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['logo']['tmp_name'];
    $fileType = mime_content_type($fileTmp);
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (in_array($fileType, $allowed)) {
        $ext = $fileType === 'image/png' ? 'png' : ($fileType === 'image/webp' ? 'webp' : 'jpg');
        $dest = './images/logo_' . $idOrga . '.' . $ext;
        move_uploaded_file($fileTmp, $dest);
        foreach(['jpg','png','webp'] as $e) {
            $old = './images/logo_' . $idOrga . '.' . $e;
            if ($old !== $dest && file_exists($old)) unlink($old);
        }
        echo "<script>window.toastMessage='Logo mis à jour avec succès';window.toastType='success';</script>";
        header('Refresh:1;url=dashboard.php');
        exit();
    } else {
        $msg = 'Format de logo non supporté.';
        echo "<script>window.toastMessage='Format de logo non supporté';window.toastType='error';</script>";
    }
}

// but de la page : afficher les evenements qu'il a créer, et affiche un formulaire pour créer un nouvel evenement
// quand il clique sur un evenement, il est redirigé vers la page event.php, ou il peut voir les détails de l'evenement
// il peut voir aussi les votes pour chaque evenement depuis cette page

//récupération des evenements de l'utilisateur
$events = getEventsOrga($_SESSION['id'], $conn);
//print de l'array $events;
//print_r($events);
//affiche les erreurs php
ini_set('display_errors', 1);


//création d'un nouvel evenement
if(isset($_POST['nom']) && isset($_POST['universite'])){
    addEvent($_POST['nom'], $_POST['universite'], $_SESSION['id'], $conn);
//A MODIFIER + SI PAS ENCORE D'EVENTS alors ne pas afficher le tableau
    header('location:dashboard.php');
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php printFaviconTag(); addDarkModeScript(); ?>
</head>
<body>
<?php include 'inc_header.php'; ?>
<?php include 'inc_admin_menu.php'; ?>
<div class="container card">
    <h1>Dashboard</h1>
    <!-- Statistiques avancées -->
    <div class="card" style="margin-bottom:20px;">
        <h2>Statistiques globales</h2>
        <div style="display:flex;flex-wrap:wrap;gap:30px;align-items:center;">
            <div>
                <strong>Taux de participation :</strong> <span id="taux-participation">Calcul...</span><br>
                <strong>Nombre total de votes :</strong> <span id="total-votes">Calcul...</span>
            </div>
            <canvas id="evolutionVotes" width="320" height="120"></canvas>
        </div>
    </div>
    <!-- Onglets de personnalisation en bas de page -->
    <div class="card" style="margin:40px auto 0 auto;max-width:500px;">
        <h2>Personnalisation</h2>
        <div style="display:flex;flex-direction:column;gap:30px;">
            <!-- Logo -->
            <div>
                <h3>Logo personnalisé</h3>
                <form method="post" enctype="multipart/form-data" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:center;">
                    <label for="logo" style="font-weight:bold;">Logo&nbsp;:</label>
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" style="width:auto;">
                    <input type="submit" value="Mettre à jour" class="btn" style="width:auto;">
                    <?php
                    $idOrga = $_SESSION['id'];
                    $logoPath = 'bgsharklo.jpg';
                    foreach(['jpg','png','webp'] as $ext) {
                        $customLogo = './images/logo_' . $idOrga . '.' . $ext;
                        if (file_exists($customLogo)) {
                            $logoPath = $customLogo;
                            break;
                        }
                    }
                    if ($logoPath) {
                        echo '<img src="'.$logoPath.'" alt="Logo actuel" style="max-width:60px;max-height:60px;vertical-align:middle;border-radius:50%;box-shadow:0 1px 4px rgba(0,0,0,0.07);">';
                    }
                    ?>
                </form>
                <?php
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $fileTmp = $_FILES['logo']['tmp_name'];
                    $fileType = mime_content_type($fileTmp);
                    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                    if (in_array($fileType, $allowed)) {
                        $ext = $fileType === 'image/png' ? 'png' : ($fileType === 'image/webp' ? 'webp' : 'jpg');
                        $dest = './images/logo_' . $idOrga . '.' . $ext;
                        move_uploaded_file($fileTmp, $dest);
                        foreach(['jpg','png','webp'] as $e) {
                            $old = './images/logo_' . $idOrga . '.' . $e;
                            if ($old !== $dest && file_exists($old)) unlink($old);
                        }
                        echo '<script>window.toastMessage="Logo mis à jour avec succès";window.toastType="success";setTimeout(()=>location.reload(),800);</script>';
                    } else {
                        echo '<p class="erreur">Format de logo non supporté (jpg, png, webp uniquement).</p>';
                    }
                }
                ?>
                <small>Le logo personnalisé s’affichera sur toutes vos pages. Format accepté : .jpg, .png, .webp.</small>
            </div>
            <!-- Favicon -->
            <div>
                <h3>Favicon personnalisé</h3>
                <form method="post" enctype="multipart/form-data" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:center;">
                    <label for="favicon" style="font-weight:bold;">Favicon&nbsp;:</label>
                    <input type="file" name="favicon" accept="image/x-icon,image/png" style="width:auto;">
                    <input type="submit" name="upload_favicon" value="Mettre à jour" class="btn" style="width:auto;">
                    <?php
                    $faviconPath = null;
                    foreach(['ico','png'] as $ext) {
                        $customFavicon = './images/favicon_' . $idOrga . '.' . $ext;
                        if (file_exists($customFavicon)) {
                            $faviconPath = $customFavicon;
                            break;
                        }
                    }
                    if ($faviconPath) {
                        echo '<img src="'.$faviconPath.'" alt="Favicon actuel" style="max-width:32px;max-height:32px;vertical-align:middle;">';
                    }
                    ?>
                </form>
                <?php
                if (isset($_POST['upload_favicon']) && isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
                    $fileTmp = $_FILES['favicon']['tmp_name'];
                    $fileType = mime_content_type($fileTmp);
                    $allowed = ['image/x-icon', 'image/vnd.microsoft.icon', 'image/png'];
                    if (in_array($fileType, $allowed)) {
                        $ext = $fileType === 'image/png' ? 'png' : 'ico';
                        $dest = './images/favicon_' . $idOrga . '.' . $ext;
                        move_uploaded_file($fileTmp, $dest);
                        foreach(['ico','png'] as $e) {
                            $old = './images/favicon_' . $idOrga . '.' . $e;
                            if ($old !== $dest && file_exists($old)) unlink($old);
                        }
                        echo '<script>window.toastMessage="Favicon mis à jour avec succès";window.toastType="success";setTimeout(()=>location.reload(),800);</script>';
                    } else {
                        echo '<p class="erreur">Format de favicon non supporté (ico, png uniquement).</p>';
                    }
                }
                ?>
                <small>Le favicon personnalisé s’affichera sur toutes vos pages. Format accepté : .ico ou .png (32x32 recommandé).</small>
            </div>
        </div>
    </div>
    <div style="text-align:center;margin:30px 0 0 0;">
        <button class="btn" onclick="toggleDarkMode()" style="padding:10px 30px;font-size:1.1em;">🌓 Thème sombre</button>
    </div>
</div>
</body>
</html>