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
            <img src="bgsharklo.jpg" alt="Logo du site">
        </div>
        <h1>Inscription pour les votes du BDE R&T</h1>
        <p>Bienvenue sur le site d'inscription pour les votes du BDE R&T. Pour vous inscrire, veuillez remplir le formulaire ci-dessous.</p>
        <p>Vous recevrez un mail contenant un lien pour effectuer votre vote.</p>
        <form action="index.php" method="post">
            <input type="text" name="login" placeholder="login universitaire" required>
            <input type="text" name="event" value=<?php echo $event; ?> hidden>
            <input type="submit" value="S'inscrire">
        </form>
    </div>
    <div class="footer">
        <p><a href="checkVote.php">Vérifier un vote effectué</a></p>
        <p><a href="login.php">Connexion administrateur</a></p>
    </div>
</body>
</html>
