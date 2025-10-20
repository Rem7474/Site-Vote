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
    <title>Page d'inscription en tant qu'organisateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="bgsharklo.jpg" alt="Logo du site">
        </div>
        <h1>Inscription en tant qu'organisateur</h1>
        <p>Bienvenue sur le site de vote en ligne. Pour vous inscrire en tant qu'organisateur, veuillez remplir le formulaire ci-dessous.</p>
        <p>Pour effectuer un vote, veuillez vous rendre sur le lien indiqué par l'organisateur.</p>
        <form action="register.php" method="post">
            <?php echo csrfField(); ?>
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>
            <label for="email">Adresse e-mail :</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Mot de passe : (8 caractères minimum)</label>
            <input type="password" id="password" name="password" required minlength="8">
            <label for="password2">Confirmer le mot de passe :</label>
            <input type="password" id="password2" name="password2" required minlength="8">
            <input type="submit" value="S'inscrire">
    </div>
</body>
</html>
