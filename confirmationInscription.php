<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>✅ Inscription confirmée - Vote en ligne</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <?php 
    include_once 'fonctionsPHP.php';
    printFaviconTag(); 
    addDarkModeScript(); 
    ?>
</head>
<body>
    <div class="container card">
        <div class="header">
            <!-- Insérez votre logo ici -->
            <img src="bgsharklo.jpg" alt="Logo du site">
        </div>
        <h1>✅ Inscription confirmée</h1>
        <p class="reussite">Votre inscription a bien été prise en compte.<br>
            Vous recevrez un mail sur l'adresse <strong><?php echo htmlspecialchars($_GET["mail"] ?? ''); ?></strong> pour pouvoir effectuer votre vote.
        </p>
        <div class="footer">
            <p><a href="checkVote.php" class="btn">Vérifier mon vote</a></p>
            <p><a href="login.php" class="btn">Connexion organisateur</a></p>
        </div>
    </div>
</body>
</html>
