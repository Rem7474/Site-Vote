<?php
//récupération du hash dans l'url
include 'fonctionsPHP.php';
if(isset($_GET['hash'])){
    //vérification du hash
    $hash = $_GET['hash'];
    //vérification de l'existence du hash dans la base de données (fonction à créer)
    $IDevent=getHash($hash,$conn);
    if($IDevent){
        //récupération de l'événement associé au hash
        $event = getEvent($IDevent, $conn);
        $nomEvent = htmlspecialchars($event['nom']);
    }
    else{
        //hash non trouvé dans la base de données
        header('Location: formulaire.html');
        exit();
    }
}
else{
    //forumlaire d'inscription header
    header('Location: formulaire.html');
    exit();
}
//récupération des candidats dans la base de données
$candidats = getListes($IDevent, $conn);


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Vote</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Insérez votre logo ici -->
            <img src="bgsharklo.jpg" alt="Logo du site">
        </div>
        <h1>Vote pour le <?php echo $nomEvent;?></h1>
        <p>Bienvenue sur la page de vote pour le <?php echo $nomEvent;?>. Pour voter, veuillez choisir un candidat dans la liste ci-dessous.</p>

        <form action="index.php" method="post" class="vote-form">
            <?php
            foreach($candidats as $candidat){
                echo '<div class="candidate">';
                echo '<input id="candidat'.$candidat['id'].'" type="radio" name="vote" value="'.$candidat['id'].'">';
                echo '<label for="candidat'.$candidat['id'].'">'.htmlspecialchars($candidat['nom']).'</label>';
                echo '<img src="./images/'.htmlspecialchars($candidat['photo']).'" alt="Photo de l\'équipe de '.htmlspecialchars($candidat['nom']).'">';
                echo '</div>';
            }
            ?>
            <input type="hidden" name="hash" value="<?php echo $hash; ?>">
            <input type="submit" value="Voter">
        </form>
    </div>
</body>
</html>
