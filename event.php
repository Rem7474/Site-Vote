<?php
//page pour afficher un évènement, voir les listes, les votes pour les listes, et pouvoir ajouter une liste
session_start();
include 'fonctionsPHP.php';

// Vérifier que l'utilisateur est connecté
requireLogin();

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$IDevent = sanitizeInput($_GET['id']);
$event = getEvent($IDevent, $conn);

if ($event == null) {
    logSecurityEvent('EVENT_NOT_FOUND', "Event ID: $IDevent", 'WARNING');
    header('Location: dashboard.php');
    exit();
}

// Vérifier que l'utilisateur est bien le propriétaire de l'événement
if ($event['reforga'] != $_SESSION['id']) {
    logSecurityEvent('UNAUTHORIZED_ACCESS', "User: {$_SESSION['id']} - Event: $IDevent", 'WARNING');
    header('Location: dashboard.php');
    exit();
}

$lists = getListes($IDevent, $conn);

//ajout d'une liste, avec nom, description et photo
if (isset($_POST['nom']) && isset($_POST['description']) && isset($_FILES['photo'])) {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        logSecurityEvent('CSRF_ATTEMPT', 'Add list', 'WARNING');
        echo 'Erreur de sécurité - Token invalide';
        exit();
    }
    
    $nom = sanitizeInput($_POST['nom']);
    $description = sanitizeInput($_POST['description']);
    $photo = $_FILES['photo'];
    
    // Validation du fichier
    $validation = validateFileUpload($photo);
    if (!$validation['valid']) {
        echo htmlspecialchars($validation['error']);
        exit();
    }
    
    // Obtenir l'extension
    $extension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
    
    //déplacement de la photo dans le dossier images
    $nomphoto = preg_replace('/[^a-zA-Z0-9]/', '', $nom) . $IDevent . '.' . $extension;
    
    // Créer le dossier images si il n'existe pas
    if (!file_exists('./images')) {
        mkdir('./images', 0755, true);
    }
    
    if (move_uploaded_file($photo['tmp_name'], './images/' . $nomphoto)) {
        $idList = addListe($nom, $nomphoto, $description, $IDevent, $conn);
        if ($idList != null) {
            logSecurityEvent('LIST_CREATED', "List: $nom - Event: $IDevent", 'INFO');
            header('Location: event.php?id=' . $IDevent);
            exit();
        }
        else {
            echo 'Erreur lors de l\'ajout de la liste';
        }
    } else {
        echo 'Erreur lors de l\'upload du fichier';
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
            <?php echo csrfField(); ?>
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
