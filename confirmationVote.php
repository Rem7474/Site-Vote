<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Inscription pour les votes du BDE R&T</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Insérez votre logo ici -->
            <img src="bgsharklo.jpg" alt="Logo du site">
        </div>
        <h1>Inscription pour les votes du BDE R&T</h1>
        <p class="reussite">Bravo, votre vote a bien été pris en compte !</p>
        <p class="reussite">Merci pour votre participation.</p>
        <!-- Affichage du hash de vote -->
        <p class="hash">Hash de participation : <?php echo $_GET["hash"]?></p>
        <!-- Lien pour vérifier son vote-->
        <p class="reussite">Conserver ce hash pour vérifier votre vote : <a href="checkVote.php">Vérifier son vote</a></p>
    </div>
</body>
</html>
