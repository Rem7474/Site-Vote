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
</head>
<body>
<?php include 'inc_header.php'; ?>
<?php include 'inc_admin_menu.php'; ?>
<div class="container card">
    <h1>Dashboard</h1>
    <div class="logo-admin-block card" style="margin-bottom:20px;">
        <form method="post" enctype="multipart/form-data" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <label for="logo" style="font-weight:bold;">Changer le logo&nbsp;:</label>
            <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" style="width:auto;" onchange="previewLogo(this)">
            <input type="submit" value="Mettre à jour" class="btn" style="width:auto;">
            <img id="logo-preview" src="<?php echo $logoPath; ?>" alt="Prévisualisation logo" style="max-width:60px;max-height:60px;border-radius:50%;margin-left:10px;box-shadow:0 1px 4px rgba(0,0,0,0.07);">
        </form>
        <script>
        function previewLogo(input) {
            const preview = document.getElementById('logo-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        </script>
        <?php if (isset($msg)) echo '<p class="erreur">'.$msg.'</p>'; ?>
        <small>Le logo personnalisé s'affichera sur toutes vos pages. Si aucun logo n'est défini, le logo par défaut sera utilisé.</small>
    </div>
    <header style="margin-bottom:18px;">
        <h2>Bonjour <?php echo $_SESSION['prenom'].' '.$_SESSION['nom']; ?></h2>
        <form action="logout.php" method="post" style="display:inline;">
            <input type="submit" value="Déconnexion" class="btn">
        </form>
    </header>
    <h2>Événements</h2>
    <div class="table-responsive">
    <?php if (!empty($events)): ?>
        <table>
            <tr>
                <th>Nom</th>
                <th>Université</th>
                <th>Listes</th>
                <th>Nombre de votes</th>
                <th>Liens</th>
                <th>Graphique</th>
            </tr>
            <?php foreach ($events as $event): 
                $listes = getListes($event['id'], $conn); 
                $nbVotes = getNbVotes($event['id'], $conn);
            ?>
            <tr>
                <td><a href="event.php?id=<?php echo $event['id']; ?>" class="btn" style="padding:6px 14px;font-size:1em;">Gérer<br><?php echo $event['nom']; ?></a></td>
                <td><?php echo $event['univ']; ?></td>
                <td>
                <?php if (!empty($listes)): ?>
                    <?php foreach ($listes as $liste): ?>
                        <div class="liste"><img src="./images/<?php echo $liste['photo']; ?>" alt="<?php echo $liste['nom']; ?>"></div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune liste</p>
                <?php endif; ?>
                </td>
                <td><?php echo $nbVotes; ?></td>
                <td style="min-width:180px;">
                    <a href="resultats.php?id=<?php echo $event['id']; ?>" class="btn" style="padding:6px 10px;font-size:0.95em;">Résultats</a> 
                    <a href="exportResultats.php?id=<?php echo $event['id']; ?>" class="btn" style="padding:6px 10px;font-size:0.95em;">Export</a>
                    <a href="gestionUtilisateurs.php?id=<?php echo $event['id']; ?>" class="btn" style="padding:6px 10px;font-size:0.95em;">Utilisateurs</a>
                    <a href="gestionMembres.php?id=<?php echo !empty($listes) ? $listes[0]['id'] : ''; ?>" class="btn" style="padding:6px 10px;font-size:0.95em;">Membres</a>
                    <a href="statistiques.php?id=<?php echo $event['id']; ?>" class="btn" style="padding:6px 10px;font-size:0.95em;">Stats</a>
                </td>
                <td>
                    <canvas id="chart_<?php echo $event['id']; ?>" width="120" height="80"></canvas>
                    <script>
                    <?php
                    $labels = [];
                    $data = [];
                    $colors = [];
                    foreach ($listes as $liste) {
                        $labels[] = addslashes($liste['nom']);
                        $data[] = getVotes($liste['id'], $conn);
                        $colors[] = "'rgba(".rand(50,200).",".rand(100,200).",".rand(200,255).",0.7)'";
                    }
                    ?>
                    new Chart(document.getElementById('chart_<?php echo $event['id']; ?>').getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: [<?php echo "'".implode("','", $labels)."'"; ?>],
                            datasets: [{
                                data: [<?php echo implode(",", $data); ?>],
                                backgroundColor: [<?php echo implode(",", $colors); ?>],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            plugins: {legend: {display: false}},
                            cutout: '70%',
                            responsive: false,
                            maintainAspectRatio: false
                        }
                    });
                    </script>
                </td>
            </tr>
            <?php endforeach ?>
        </table>
    <?php else: ?>
        <p>Aucun événement disponible.</p>
    <?php endif; ?>
    </div>
    <div class="card" style="margin-top:30px;">
        <h2>Créer un nouvel événement</h2>
        <form method="post" action="dashboard.php">
            <label for="nom">Nom de l'événement</label>
            <input type="text" name="nom">
            <label for="universite">Université (ex : univ-smb.fr)</label>
            <input type="text" name="universite">
            <input type="submit" value="Créer" class="btn">
        </form>
    </div>
</div>
</body>
</html>