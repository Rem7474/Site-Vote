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
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>📋 <?php echo htmlspecialchars($event['nom']); ?> - Gestion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <?php printFaviconTag(); addDarkModeScript(); ?>
</head>
<body>
    <?php include 'inc_header.php'; ?>
    <?php include 'inc_admin_menu.php'; ?>
    <div class="container card">
        <div class="header">
            <a href="dashboard.php" class="btn" style="margin-bottom: 15px;">← Retour au tableau de bord</a>
        </div>
        <h1>📋 Gestion de l'événement</h1>
        <h2><?php echo htmlspecialchars($event['nom']); ?></h2>
        
        <div class="card" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-left: 5px solid #2196f3; margin-bottom: 20px;">
            <h3 style="color: #0d47a1; margin: 0 0 5px 0;">📊 Statistiques</h3>
            <p style="color: #0d47a1; margin: 0;"><strong><?php echo count($lists); ?> liste(s)</strong> | <strong>Total votes : <?php $totalVotes = 0; foreach($lists as $l) { $totalVotes += getVotes($l['id'], $conn); } echo $totalVotes; ?></strong></p>
        </div>
        
        <h3>🗳️ Listes candidates</h3>
        <?php if (!empty($lists)): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Photo</th>
                            <th>Votes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lists as $list): 
                            $votes = getVotes($list['id'], $conn);
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($list['nom']); ?></strong></td>
                                <td><?php echo htmlspecialchars($list['description']); ?></td>
                                <td>
                                    <div class="liste">
                                        <?php if (!empty($list['photo']) && file_exists('./images/'.$list['photo'])): ?>
                                            <img src="./images/<?php echo htmlspecialchars($list['photo']); ?>" alt="<?php echo htmlspecialchars($list['nom']); ?>">
                                        <?php else: ?>
                                            <span>Pas d'image</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><strong><?php echo $votes; ?></strong> 🗳️</td>
                                <td><a href="list.php?id=<?php echo htmlspecialchars($list['id']); ?>" class="btn" style="padding: 5px 15px;">👁️ Voir</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="card" style="background: #fff3cd; border-left: 5px solid #ffc107; padding: 15px;">
                <p style="color: #856404; margin: 0;">⚠️ Aucune liste n'a encore été ajoutée pour cet événement.</p>
            </div>
        <?php endif; ?>
        
        <div class="card" style="margin-top: 30px; background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%); border-left: 5px solid #9c27b0;">
            <h3 style="color: #4a148c; margin-top: 0;">➕ Ajouter une nouvelle liste</h3>
            <form method="post" enctype="multipart/form-data">
                <?php echo csrfField(); ?>
                <label for="nom">📝 Nom de la liste</label>
                <input type="text" name="nom" id="nom" placeholder="Exemple: Équipe Innovation" required>
                
                <label for="description">📄 Description</label>
                <input type="text" name="description" id="description" placeholder="Décrivez brièvement l'équipe" required>
                
                <label for="photo">📷 Photo de la liste (JPG, PNG, WEBP, max 5MB)</label>
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp,image/gif" required>
                
                <input type="submit" value="✨ Ajouter la liste" class="btn">
            </form>
        </div>
    </div>
</body>
</html>
