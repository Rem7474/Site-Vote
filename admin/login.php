<?php
include '../src/includes/fonctionsPHP.php';
session_start();

if (isset($_SESSION['id'])) {
    header('Location: dashboard.php');
    exit();
}

if (isset($_POST['login']) && isset($_POST['password'])) {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        logSecurityEvent('CSRF_ATTEMPT', 'Login', 'WARNING');
        header('Location: login.php');
        exit();
    }
    
    // Rate limiting - max 5 tentatives de connexion toutes les 15 minutes
    if (!checkRateLimit('login', 5, 900)) {
        logSecurityEvent('RATE_LIMIT_EXCEEDED', 'Login attempts', 'WARNING');
        header('Location: erreur.html');
        exit();
    }
    
    $login = sanitizeInput($_POST['login']);
    $password = $_POST['password'];
    
    if (!validateEmail($login)) {
        logSecurityEvent('INVALID_EMAIL_FORMAT', "Email: $login", 'WARNING');
        header('Location: login.php');
        exit();
    }
    
    $result=getInfosOrga($login, $conn);
    if ($result){
        //vérification du mot de passe
        if (password_verify($password, $result['password'])){
            //on démarre la session
            $_SESSION['email'] = $result['email'];
            $_SESSION['id'] = $result['id'];
            $_SESSION['nom'] = $result['nom'];
            $_SESSION['prenom'] = $result['prenom'];
            
            logSecurityEvent('LOGIN_SUCCESS', "Email: $login", 'INFO');
            header('Location: dashboard.php');
            exit();
        }
        else{
            logSecurityEvent('LOGIN_FAILED', "Email: $login - Wrong password", 'WARNING');
            header('Location: login.php');
            exit();
        }
    }
    else{
        logSecurityEvent('LOGIN_FAILED', "Email: $login - User not found", 'WARNING');
        header('Location: login.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>🔐 Connexion Organisateur - Vote en ligne</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <?php printFaviconTag(); addDarkModeScript(); ?>
</head>
<body>
    <div class="container card">
        <div class="header">
            <img src="bgsharklo.jpg" alt="Logo du site">
        </div>
        <h1>🔐 Connexion Organisateur</h1>
        <p>Accédez à votre tableau de bord pour gérer vos événements de vote et consulter les résultats en temps réel.</p>
        <form action="login.php" method="post">
            <?php echo csrfField(); ?>
            <label for="login">📧 Email</label>
            <input type="email" id="login" name="login" placeholder="votre@email.com" required>
            <label for="password">🔒 Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
            <input type="submit" value="🚀 Se connecter" class="btn">
        </form>
    </div>
    <div class="footer">
        <p>Pas encore de compte ?</p>
        <p><a href="register.php" class="btn">📝 S'inscrire en tant qu'organisateur</a></p>
        <p style="margin-top: 20px;"><a href="checkVote.php">🔍 Vérifier un vote</a></p>
    </div>
</body>
</html>

