<?php
include('fonctionsPHP.php');

if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password2'])) {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        logSecurityEvent('CSRF_ATTEMPT', 'Register', 'WARNING');
        header('Location: erreur.html');
        exit();
    }
    
    // Rate limiting - max 3 inscriptions par IP toutes les 30 minutes
    if (!checkRateLimit('register', 3, 1800)) {
        logSecurityEvent('RATE_LIMIT_EXCEEDED', 'Registration attempts', 'WARNING');
        header('Location: erreur.html');
        exit();
    }
    
    $nom = sanitizeInput($_POST['nom']);
    $prenom = sanitizeInput($_POST['prenom']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    
    // Validation
    if (!validateEmail($email)) {
        logSecurityEvent('INVALID_EMAIL', "Email: $email", 'WARNING');
        header('Location: erreur.html');
        exit();
    }
    
    if ($password !== $password2) {
        logSecurityEvent('PASSWORD_MISMATCH', "Email: $email", 'INFO');
        header('Location: erreur.html');
        exit();
    }
    
    if (strlen($password) < 8) {
        logSecurityEvent('WEAK_PASSWORD', "Email: $email", 'WARNING');
        header('Location: erreur.html');
        exit();
    }
    
    if (getOrga($email, $conn)) {
        logSecurityEvent('DUPLICATE_ORGANIZER', "Email: $email", 'WARNING');
        header('Location: erreur.html');
        exit();
    }
    
    $password = password_hash($password, PASSWORD_DEFAULT);
    addOrga($email, $password, $nom, $prenom, $conn);
    logSecurityEvent('ORGANIZER_REGISTERED', "Email: $email", 'INFO');
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>📝 Inscription Organisateur - Vote en ligne</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <?php printFaviconTag(); addDarkModeScript(); ?>
</head>
<body>
    <div class="container card">
        <div class="header">
            <img src="bgsharklo.jpg" alt="Logo du site">
        </div>
        <h1>📝 Créer un compte organisateur</h1>
        <p>Créez votre compte pour organiser des votes en ligne sécurisés pour vos événements universitaires.</p>
        <form action="register.php" method="post">
            <?php echo csrfField(); ?>
            <label for="nom">👤 Nom</label>
            <input type="text" id="nom" name="nom" placeholder="Dupont" required>
            <label for="prenom">👤 Prénom</label>
            <input type="text" id="prenom" name="prenom" placeholder="Jean" required>
            <label for="email">📧 Adresse e-mail</label>
            <input type="email" id="email" name="email" placeholder="jean.dupont@exemple.com" required>
            <label for="password">🔒 Mot de passe (minimum 8 caractères)</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required minlength="8">
            <label for="password2">🔒 Confirmer le mot de passe</label>
            <input type="password" id="password2" name="password2" placeholder="••••••••" required minlength="8">
            <input type="submit" value="✨ Créer mon compte" class="btn">
        </form>
    </div>
    <div class="footer">
        <p>Déjà un compte ?</p>
        <p><a href="login.php" class="btn">🔐 Se connecter</a></p>
    </div>
</body>
</html>
