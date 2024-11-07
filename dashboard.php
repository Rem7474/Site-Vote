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
//print de l'array $events
//print_r($events);
//affiche les erreurs php
ini_set('display_errors', 1);


//création d'un nouvel evenement
if(isset($_POST['nom']) && isset($_POST['universite'])){
    addEvent($_POST['nom'], $_POST['universite'], $_SESSION['id'], $conn);
//A MODIFIER + SI PAS ENCORE D'EVENTS alors ne pas afficher le tableau
    header('location:dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Dashboard</h2>
        <!-- affiche un header avec le nom de l'utilisateur et un bouton de déconnexion -->
        <header>
            <h2>Bonjour <?php echo $_SESSION['prenom'].' '.$_SESSION['nom']; ?></h2>
            <form action="logout.php" method="post">
                <input type="submit" value="Déconnexion">
            </form>
        </header>
        <h2>Evenements</h2>
        <?php if (!empty($events)): ?>
            <table>
                <tr>
                    <th>Nom</th>
                    <th>Université</th>
                    <th>Listes</th>
                    <th>Nombre de votes</th>
                    <th>Liens de partage</th>
                </tr>
                <?php foreach ($events as $event): 
                    $listes = getListes($event['id'], $conn); ?>
                    <tr>
                        <td><a href="event.php?id=<?php echo $event['id']; ?>"><?php echo $event['nom']; ?></a></td>
                        <td><?php echo $event['univ']; ?></td>
                        <td>
                        <?php if (!empty($listes)): ?>
                            <?php foreach ($listes as $liste): ?>
                                <!-- Affiche les listes de l'événement avec leur nom, photo et description -->
                                <div class="liste">
                                    <img src="./images/<?php echo $liste['photo']; ?>" alt="<?php echo $liste['nom']; ?>">
                                    <?php echo $liste['nom'] . ' : ' . $liste['description']; ?><br>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aucune liste disponible pour le moment.</p>
                        <?php endif; ?>
                    </td>
                        <td><?php echo getNbVotes($event['id'], $conn); ?></td>
                        <td><a href="https://vote.remcorp.fr/vote.php?id=<?php echo $event['id']; ?>">Lien de partage</a></td>
                    </tr>
                <?php endforeach ?>
            </table>
        <?php else: ?>
            <p>Aucun événement disponible.</p>
        <?php endif; ?>
        <h2>Créer un nouvel événement</h2>
        <form method="post" action="dashboard.php">
                <label for="nom">Nom de l'événement</label>
                <input type="text" name="nom">
                <label for="universite">Université (ex : univ-smb.fr)</label>
                <input type="text" name="universite">
                <input type="submit" value="Créer">
        </form>
    </div>
</body>
</html>