<?php
//page pour afficher un évènement, voir les listes, les votes pour les listes, et pouvoir ajouter une liste
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'fonctionsPHP.php';
if (!isset($_SESSION['id']) || !isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}
$IDevent = $_GET['id'];
$event = getEvent($IDevent, $conn);
if ($event == null) {
    //header('Location: dashboard.php');
    //exit();
    echo "Event not found";
}
$lists = getListes($IDevent, $conn);
$votes = getVotes($IDevent, $conn);

//ajout d'une liste, avec nom, description et photo
if (isset($_POST['nom']) && isset($_POST['description']) && isset($_FILES['photo'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $photo = $_FILES['photo'];
    //vérification de l'extension de la photo
    $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp');
    $extension = explode('.', $photo['name']);
    $extension = strtolower(end($extension));
    if (!in_array($extension, $extensions)) {
        echo 'Extension de fichier non autorisée';
        exit();
    }
    //déplacement de la photo dans le dossier images
    $nomphoto = $nom . $IDevent . '.' . $extension;
    move_uploaded_file($photo['tmp_name'], './images/' . $nomphoto);
    $idList = addListe($nom, $nomphoto, $description, $IDevent, $conn);
    if ($idList != null) {
        header('Location: list.php?id=' . $idList);
        exit();
    }
    else {
        echo 'Erreur lors de l\'ajout de la liste';
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Event</title>
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body>
    <div class="container">
        <h1>Evènement : <?php echo $event['nom']; ?></h1>
        <h2>Listes</h2>
        <?php if (!empty($lists)):
            ?>
            <ul>
                <?php foreach ($lists as $list): ?>
                    <!-- Affichage de chaque liste avec les informations et le nombre de votes -->
                    <li><?php echo $list['nom'] . ' : ' . $list['description']; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucune liste disponible pour cet événement.</p>
        <?php endif; ?>
        <h2>Ajouter une liste</h2>
        <form method="post" enctype="multipart/form-data">
            <label for="nom">Nom de la liste</label>
            <input type="text" name="nom" id="nom" required>
            <label for="description">Description</label>
            <input type="text" name="description" id="description" required>
            <label for="photo">Photo de la liste</label>
            <input type="file" name="photo" id="photo" required>
            <input type="submit" value="Ajouter">
        </form>
    </div>
    </body>
</html>
