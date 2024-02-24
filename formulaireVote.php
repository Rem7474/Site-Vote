<?php
//récupération du hash dans l'url
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
</head>
<body>
    <h1>Vote pour le BDE R&T</h1>
    <p>Bienvenue sur la page de vote pour le BDE R&T. Pour voter, veuillez choisir un candidat dans la liste ci-dessous.</p>

    <form action="index.php" method="post">
        <div class="gauche">
            <input id="candidat1" type="radio" name="candidat" value="1"><br>
            <label for="candidat1">Equipe de ....</label>
            <img src="candidat1.jpg" alt="Photo de l'équipe de ...."><br>
        </div>
        <div class="droite">
            <input id="candidat2" type="radio" name="candidat" value="2"><br>
            <label for="candidat2">Equipe de ....</label>
            <img src="candidat2.jpg" alt="Photo de l'équipe de ...."><br>
        </div>
        <input type="hidden" name="hash" value="<?php echo $hash; ?>">
        <input type="submit" value="Voter">
    </form>
</html>