<?php
// Page de création/modification d'une liste (candidat)
session_start();
include '../src/includes/fonctionsPHP.php';
if (!isset($_SESSION['id']) || !isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}
$idListe = $_GET['id'];
$liste = getListeById($idListe, $conn); // Utilise une fonction dédiée
if (!$liste) {
    echo "<p>Liste introuvable.</p>";
    exit();
}
// Modification de la liste
if (isset($_POST['nom']) && isset($_POST['description'])) {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        logSecurityEvent('CSRF_ATTEMPT', 'Modifier liste', 'WARNING');
        header('Location: dashboard.php');
        exit();
    }
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    // Gestion de la photo si upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = $_FILES['photo'];
        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp');
        $extension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
        if (in_array($extension, $extensions)) {
            $safeNom = strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '_', $nom));
            $nomphoto = $safeNom . $liste['refevent'] . '.' . $extension;
            move_uploaded_file($photo['tmp_name'], '../images/' . $nomphoto);
        } else {
            echo '<script>window.toastMessage="Extension de fichier non autorisée";window.toastType="error";</script>';
            header('Refresh:2;url=list.php?id=' . $idListe);
            exit();
        }
    } else {
        $nomphoto = $liste['photo'];
    }
    // Mise à jour de la liste
    $sql = "UPDATE listes SET nom = :nom, description = :description, photo = :photo WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':nom' => $nom,
        ':description' => $description,
        ':photo' => $nomphoto,
        ':id' => $idListe
    ]);
    echo "<script>window.toastMessage='Liste modifiée avec succès';window.toastType='success';</script>";
    header('Refresh:1;url=event.php?id=' . $liste['refevent']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Modifier la liste</title>
    <link rel="stylesheet" type="text/css" href="../public/assets/css/styles.css">
    <script>
    function previewImage(input) {
        const preview = document.getElementById('img-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    }
    </script>
</head>
<body>
<?php include 'inc_header.php'; ?>
<?php include '../src/includes/inc_admin_menu.php'; ?>
<div class="container">
    <h1>Modifier la liste : <?php echo htmlspecialchars($liste['nom']); ?></h1>
    <form method="post" enctype="multipart/form-data">
        <?php echo csrfField(); ?>
        <label for="nom">Nom de la liste</label>
        <input type="text" name="nom" value="<?php echo htmlspecialchars($liste['nom']); ?>" required>
        <label for="description">Description</label>
        <input type="text" name="description" value="<?php echo htmlspecialchars($liste['description']); ?>" required>
        <label for="photo">Photo de la liste</label>
        <input type="file" name="photo" onchange="previewImage(this)">
        <img id="img-preview" src="<?php echo $liste['photo'] ? '../images/' . htmlspecialchars($liste['photo']) : ''; ?>" alt="Prévisualisation" style="max-width:120px;display:<?php echo $liste['photo'] ? 'block' : 'none'; ?>;margin-top:10px;border-radius:7px;box-shadow:0 1px 4px rgba(0,0,0,0.07);">
        <input type="submit" value="Enregistrer les modifications">
    </form>
    <a href="event.php?id=<?php echo $liste['refevent']; ?>">Retour à l'événement</a>
</div>
</body>
</html>

