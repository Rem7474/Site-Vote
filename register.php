<?php
include('fonctionsPHP.php');
if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password2'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    if ($password == $password2 && strlen($password) >= 8 && filter_var($email, FILTER_VALIDATE_EMAIL) && !getOrga($email, $conn)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        addOrga($email, $password, $nom, $prenom, $conn);
        header('Location: login.php');
    } else {
        //redirige vers erreur
        header('Location: erreur.html');
    }
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
