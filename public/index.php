<?php
include '../src/includes/fonctionsPHP.php';

if(isset($_POST['login']) && !empty($_POST['login']) && isset($_POST['event']) && !empty($_POST['event'])){
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        logSecurityEvent('CSRF_ATTEMPT', 'Registration', 'WARNING');
    header('Location: erreur.html');
        exit();
    }
    
    $login = $_POST['login'];
    $event = $_POST['event'];
    InscriptionVote($login, $event);
}
//récupération du vote si il existe
else if(isset($_POST['vote']) && isset($_POST['hash'])){
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        logSecurityEvent('CSRF_ATTEMPT', 'Vote', 'WARNING');
    header('Location: erreur.html');
        exit();
    }
    
    $vote = $_POST['vote'];
    $hash = $_POST['hash'];
    EnregistrerVote($vote, $hash);
}
//récupération du hash dans l'url
else if(isset($_GET['hash'])){
    $hash = $_GET['hash'];
    //vérification de l'existence du hash dans la base de données (fonction à créer) UNDEFINED FUNCTION
    $refevent=getHash($hash, $conn);
    if($refevent != null){
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
else {
    // Redirection par défaut vers la page de connexion organisateur
    ob_clean();
    header('Location: ../admin/login.php');
    exit();
}
?>