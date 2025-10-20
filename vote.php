<?php
if (!isset($infosEvent) || empty($infosEvent)) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Inscription pour les votes de : <?php echo $infosEvent["nom"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <?php printFaviconTag(); addDarkModeScript(); ?>
</head>
<body>
    <div class="container card">
        <div class="header">
            <img src="bgsharklo.jpg" alt="Logo du site">
        </div>
        <h1>Inscription pour les votes de : <?php echo htmlspecialchars($infosEvent["nom"]); ?></h1>
        <p>Bienvenue sur le site d'inscription pour les votes de l'évènement <?php echo htmlspecialchars($infosEvent["nom"]); ?>. Pour vous inscrire, veuillez remplir le formulaire ci-dessous.</p>
        <p>Vous recevrez un mail contenant un lien pour effectuer votre vote.</p>
        <form action="index.php" method="post">
            <?php echo csrfField(); ?>
            <input type="text" name="login" placeholder="login universitaire" required>
            <input type="text" name="event" value="<?php echo htmlspecialchars($infosEvent["id"]); ?>" hidden>
            <input type="submit" value="S'inscrire" class="btn">
        </form>
    </div>
    <div class="footer">
        <p><a href="checkVote.php" class="btn">Vérifier un vote effectué</a></p>
        <p><a href="login.php" class="btn">Connexion administrateur</a></p>
    </div>
</body>
</html>
