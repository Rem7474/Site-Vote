<?php
//récupération du hash dans l'url
include '../src/includes/fonctionsPHP.php';
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
    header('Location: erreur.html');
        exit();
    }
}
else{
    //forumlaire d'inscription header
    header('Location: erreur.html');
    exit();
}
//récupération des candidats dans la base de données
$candidats = getListes($IDevent, $conn);


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>🗳️ Vote - <?php echo htmlspecialchars($nomEvent); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
    <?php printFaviconTag(); addDarkModeScript(); ?>
</head>
<body>
    <div class="container card">
        <div class="header">
            <img src="assets/images/logo-default.jpg" alt="Logo du site">
        </div>
        <h1>🗳️ Votez maintenant</h1>
        <h2><?php echo htmlspecialchars($nomEvent); ?></h2>
        <p>Bienvenue sur votre page de vote personnalisée. Choisissez l'équipe pour laquelle vous souhaitez voter ci-dessous.</p>
        <p class="reussite"><strong>⚠️ Attention :</strong> Vous ne pourrez voter qu'une seule fois. Votre choix est définitif.</p>

        <form action="index.php" method="post" class="vote-form">
            <?php echo csrfField(); ?>
            <?php
            foreach($candidats as $candidat){
                echo '<div class="candidate">';
                echo '<input id="candidat'.htmlspecialchars($candidat['id']).'" type="radio" name="vote" value="'.htmlspecialchars($candidat['id']).'" required>';
                echo '<label for="candidat'.htmlspecialchars($candidat['id']).'"><strong>'.htmlspecialchars($candidat['nom']).'</strong></label>';
                if (!empty($candidat['photo']) && file_exists('assets/images/'. $candidat['photo'])) {
                    echo '<img src="assets/images/'.htmlspecialchars($candidat['photo']).'" alt="Photo de l\'équipe '.htmlspecialchars($candidat['nom']).'">';
                }
                echo '</div>';
            }
            ?>
            <input type="hidden" name="hash" value="<?php echo htmlspecialchars($hash); ?>">
            <input type="submit" value="🗳️ Confirmer mon vote" class="btn">
        </form>
    </div>
    <div class="footer">
        <p>⚠️ Votre vote est anonyme et sécurisé</p>
    </div>
</body>
</html>

