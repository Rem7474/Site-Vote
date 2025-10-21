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
    <title>📝 Inscription - <?php echo htmlspecialchars($infosEvent["nom"]); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
    <?php printFaviconTag(); addDarkModeScript(); ?>
</head>
<body>
    <div class="container card">
        <div class="header">
            <img src="assets/images/logo-default.jpg" alt="Logo du site">
        </div>
        <h1>📝 Inscription pour les votes</h1>
        <h2><?php echo htmlspecialchars($infosEvent["nom"]); ?></h2>
        <p>Bienvenue sur le site d'inscription pour l'événement <strong><?php echo htmlspecialchars($infosEvent["nom"]); ?></strong>.</p>
        <p>Pour vous inscrire, veuillez remplir le formulaire ci-dessous. Vous recevrez un mail contenant un lien unique pour effectuer votre vote.</p>
        <form action="index.php" method="post">
            <?php echo csrfField(); ?>
            <label for="login">🎓 Login universitaire (format: prenom.nom)</label>
            <input type="text" id="login" name="login" placeholder="prenom.nom" pattern="^[a-z]+\.[a-z]+$" required>
            <small>Exemple: jean.dupont</small>
            <input type="hidden" name="event" value="<?php echo htmlspecialchars($infosEvent["id"]); ?>">
            <input type="submit" value="📧 Recevoir mon lien de vote" class="btn">
        </form>
    </div>
    <div class="footer">
        <p><a href="checkVote.php" class="btn">🔍 Vérifier un vote effectué</a></p>
        <p><a href="../admin/login.php" class="btn">🔐 Connexion administrateur</a></p>
    </div>
</body>
</html>

