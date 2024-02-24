<?php
//Formulaire d'inscription au vote, avec les champs suivants : nom, prénom
//ensuite : envoie d'un mail de confirmation avec un lien pour valider l'inscription qui contient un hash salé du nom et prénom de l'utilisateur (hash('sha256', $nom.$prenom.$salt))

//Si l'utilisateur clique sur le lien, on vérifie que le hash est correct, et on enregistre l'utilisateur dans la base de données

//Si l'utilisateur est déjà inscrit, on lui affiche un message d'erreur
//Si l'utilisateur est déjà inscrit et a validé son inscription, on lui affiche un message d'erreur
//Si l'utilisateur est déjà inscrit mais n'a pas validé son inscription, on lui affiche un message d'erreur

//récupération des données du formulaire si elles existent
if(isset($_POST['nom']) && isset($_POST['prenom'])){
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    //fonction InscriptionVote($nom, $prenom) à créer
    InscriptionVote($nom, $prenom);
}
//récupération du vote si il existe
else if(isset($_POST['vote']) && isset($_POST['hash'])){
    $vote = $_POST['vote'];
    $hash = $_POST['hash'];
    //vérification de l'existence du hash dans la base de données (fonction à créer)
    if(!hashExiste($hash)){
        //redirige vers une page d'erreur
        header('Location: erreur.html');
        exit();
    }
    //vérification du vote (values = 1, 2)
    if($vote != 1 && $vote != 2){
        //redirige vers une page d'erreur
        header('Location: erreur.html');
        exit();
    }
    else{
        //enregistrement du vote dans la base de données (fonction à créer)
        enregistrerVote($vote);
        //redirection d'une page html de confirmation
        header('Location: confirmationVote.html');
        exit();
    }
}
//récupération du hash dans l'url
else if(isset($_GET['hash'])){
    //vérification du hash
    $hash = $_GET['hash'];
    //vérification de l'existence du hash dans la base de données (fonction à créer)
    if(hashExiste($hash)){
        //redirection vers le formulaire de vote en passant en paramètre le hash
        header('Location: formulaireVote.php?hash='.$hash);
        exit();
    }
    else{
        //affichage d'une page html d'erreur
        header('Location: erreur.html');
        exit();
    }
}
else{
    //forumlaire d'inscription header
    header('Location: formulaire.html');
    exit();
}
?>