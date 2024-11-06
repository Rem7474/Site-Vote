<?php
session_start();
if(!isset($_SESSION['id'])){
    header('location:login.php');
}
include 'fonctionsPHP.php';
// but de la page : afficher les evenements qu'il a créer, et affiche un formulaire pour créer un nouvel evenement
// quand il clique sur un evenement, il est redirigé vers la page event.php, ou il peut voir les détails de l'evenement
// il peut voir aussi les votes pour chaque evenement depuis cette page

//récupération des evenements de l'utilisateur
$events = getEventsOrga($_SESSION['id'], $conn);

//création d'un nouvel evenement
if(isset($_POST['nom']) && isset($_POST['universite'])){
    addEvent($_POST['nom'], $_POST['universite'], $_SESSION['id'], $conn);
//A MODIFIER + SI PAS ENCORE D'EVENTS alors ne pas afficher le tableau
    header('location:dashboard.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="header">
        <h2>Dashboard</h2>
    </div>
    <div class="content">
        //affiche un header avec le nom de l'utilisateur et un bouton de déconnexion
        <header>
            <h2>Bonjour <?php echo $_SESSION['prenom'].' '.$_SESSION['nom']; ?></h2>
            <a href="logout.php" class="btn">Déconnexion</a>
    </div>
    <div class="content">
        <h2>Evenements</h2>
        <table>
            <tr>
                <th>Nom</th>
                <th>Université</th>
                <th>Listes</th>
                <th>Nombre de votes</th>
                <th>Liens de partage</th>
            </tr>
            <?php foreach ($events as $event): 
            $listes = getLists($event['id']);?>
                <tr>
                    <td><a href="event.php?id=<?php echo $event['id']; ?>"><?php echo $event['Nom']; ?></a></td>
                    <td><?php echo $event['universite']; ?></td>
                    <td><?php foreach ($listes as $liste) {
                        //affiche les listes de l'evenement avec leur nom, photo et description
                        echo '<img src="data:image/jpeg;base64,'.base64_encode($liste['photo']).'" width="50" height="50">';
                        echo $liste['nom'].' : '.$liste['description'].'<br>';
                    }  ?></td>

                    <td><?php echo getNbVotes($event['id']); ?></td>
                    <td><a href="https://vote.remcorp.fr/vote.php?id=<?php echo $event['id']; ?>">Lien de partage</a></td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
    <div class="content">
        <h2>Créer un nouvel événement</h2>
        <form method="post" action="dashboard.php">
            <div class="input-group">
                <label>Nom</label>
                <input type="text" name="nom">
            </div>
            <div class="input-group">
                <label>Université (ex : univ-smb.fr)</label>
                <input type="text" name="universite">
            </div>
            <div class="input-group">
                <button type="submit" class="btn" name="create_event">Créer</button>
            </div>
        </form>
    </div>
</body>