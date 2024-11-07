<?php
include 'fonctionsPHP.php';
if(isset($_POST['login']) && !empty($_POST['login']) && isset($_POST['event']) && !empty($_POST['event'])){
    $login = $_POST['login'];
    $event = $_POST['event'];
    InscriptionVote($login, $event);
}
//récupération du vote si il existe
else if(isset($_POST['vote']) && isset($_POST['hash'])){
    $vote = $_POST['vote'];
    $hash = $_POST['hash'];
    EnregistrerVote($vote, $hash);
}
//récupération du hash dans l'url
else if(isset($_GET['hash'])){
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
elseif (isset($_GET['id']) && !empty($_GET['id'])){
    $event = $_GET['id'];
    //récupération des informations de l'événement
    $infosEvent = getEvent($event, $conn);
    //forumlaire d'inscription au vote
    include('vote.php');
    exit();
}
?>