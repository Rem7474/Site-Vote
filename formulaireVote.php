<?php
//récupération du hash dans l'url
include 'fonctionsPHP.php';
if(isset($_GET['hash'])){
    //vérification du hash
    $hash = $_GET['hash'];
    //vérification de l'existence du hash dans la base de données (fonction à créer)
    if(!hashExiste($hash)){
        //affichage d'une page html d'erreur
        header('Location: erreur.html');
        exit();
    }
}
else{
    //forumlaire d'inscription header
    header('Location: formulaire.html');
    exit();
}
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
        <h1>Vote pour le BDE R&T</h1>
        <p>Bienvenue sur la page de vote pour le BDE R&T. Pour voter, veuillez choisir un candidat dans la liste ci-dessous.</p>

        <form action="index.php" method="post" class="vote-form">
            <div class="candidate">
                <input id="candidat1" type="radio" name="candidat" value="1">
                <label for="candidat1">Equipe de ....</label>
                <img src="candidat1.jpg" alt="Photo de l'équipe de ....">
            </div>
            <div class="candidate">
                <input id="candidat2" type="radio" name="candidat" value="2">
                <label for="candidat2">Equipe de ....</label>
                <img src="candidat2.jpg" alt="Photo de l'équipe de ....">
            </div>
            <input type="hidden" name="hash" value="<?php echo $hash; ?>">
            <input type="submit" value="Voter">
        </form>
    </div>
</body>
</html>
