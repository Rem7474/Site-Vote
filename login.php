<?php
include 'fonctionsPHP.php';

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
            session_start();
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
    <title>Page de connexion en tant qu'organisateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="bgsharklo.jpg" alt="Logo du site">
        </div>
        <h1>Connexion organisateur</h1>
        <p>Bienvenue sur le site de vote en ligne. Pour vous connecter en tant qu'organisateur, veuillez remplir le formulaire ci-dessous.</p>
        <p>Pour effectuer un vote, veuillez vous rendre sur le lien indiqué par l'organisateur.</p>
        <form action="login.php" method="post">
            <?php echo csrfField(); ?>
            <label for="login">Email</label>
            <input type="text" name="login" placeholder="email" required>
            <label for="password">Mot de passe</label>
            <input type="password" name="password" placeholder="mot de passe" required>
            <input type="submit" value="Se connecter">
        </form>
    </div>
    <div class="footer">
        <p><a href="register.php">S'inscrire en tant qu'organisateur</a></p>
    </div>
</body>
</html>

