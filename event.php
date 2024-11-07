<?php
//page pour afficher un évènement, voir les listes, les votes pour les listes, et pouvoir ajouter une liste
session_start();
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
else if ($event['reforga'] != $_SESSION['id']) {
    header('Location: dashboard.php');
    exit();
}
$lists = getListes($IDevent, $conn);

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
        <a href="dashboard.php">Retour au tableau de bord</a>
        <h1>Evènement : <?php echo $event['nom']; ?></h1>
        <h2>Listes</h2>
        <?php if (!empty($lists)): ?>
            <table>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Photo</th>
                    <th>Nombre de votes</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($lists as $list): 
                    $votes = getVotes($list['id'], $conn);
                ?>
                    <tr>
                        <td><?php echo $list['nom']; ?></td>
                        <td><?php echo $list['description']; ?></td>
                        <td><div class="liste"><img src="./images/<?php echo $list['photo']; ?>" alt="<?php echo $list['nom']; ?>"></div></td>
                        <td><?php echo $votes; ?></td>
                        <td><a href="list.php?id=<?php echo $list['id']; ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
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
