<?php
//connexion à la base de données
include 'FonctionsConnexion.php';
include 'fonctionsBDD.php';
// Correction : ne pas inclure parametres.ini comme un fichier PHP, mais le parser
$param = parse_ini_file('./private/parametres.ini');
$conn = ConnexionBDD('./private/parametres.ini');
$DEBUG = isset($param['debug']) && ($param['debug'] === true || $param['debug'] === 'true');
if ($DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
// Chargement de Composer autoload
if (!file_exists('vendor/autoload.php')) {
    if ($DEBUG) {
        echo '<b>Erreur :</b> vendor/autoload.php est manquant. Exécutez composer install.';
    }
    exit();
}
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function InscriptionVote($login, $IDevent){
    global $conn;
    //vérification de l'existence de l'événement dans la base de données
    if(!getEvent($IDevent, $conn)){
        //redirection vers une page d'erreur
        header('Location: erreur.html');
        exit();
    }
    //mise en minuscule du login
    $login = strtolower($login);
    //Vérification du login (sans caractères spéciaux + longueur entre 6 et 8 caractères)
    /*if(!preg_match('/^[a-z]{6,8}$/', $login)){
        //affichage d'une page html d'erreur
        header('Location: erreur.html');
        exit();
    }*/
    //vérification du login : prénom.nom
    if (!preg_match('/^[a-z]+\.[a-z]+$/', $login)){
        //affichage d'une page html d'erreur
        header('Location: erreur.html');
        exit();
    }
    //création du mail a partir de l'université définie dans la table event
    //récupération de l'université
    $universite = getUniversity($IDevent, $conn);
    //création du mail
    $email = $login."@etu.".$universite;
    //hashage salé avec le timestamp du nom et prénom
    $salt = time();
    $hash = hash('sha256', $login.$salt);
    //envoi du mail de confirmation si la personne n'est pas déjà inscrite à l'événement
    $user=getUser($login, $IDevent, $conn);
    if($user){
        //redirection vers une page d'erreur car l'utilisateur est déjà inscrit
        header('Location: erreur.html');
        exit();
    }
    else{
        //récupération du nom de l'événement
        $nomEvent = getNomEvent($IDevent, $conn);
        SendMail($email, "[Vote ".$nomEvent."] Confirmation d'inscription", "https://vote.remcorp.fr/index.php?hash=".$hash, $nomEvent);
        //enregistrement du hash dans la base de données
        $ajout=addHash($hash, $IDevent, $conn);
        $ajout2=addUser($login, $email, $IDevent, $conn);
        //redirection vers une page de confirmation si l'ajout a réussi
        if($ajout){
            header('Location: confirmationInscription.php?mail='.$email);
            exit();
        }
        else{
            //redirection vers une page d'erreur si l'ajout a échoué
            header('Location: erreur.html');
            exit();
        }
    }
}

function EnregistrerVote($vote, $hash){
    global $conn;
    //vérification de l'existence du hash dans la base de données
    //récupération de l'event a partir du hash, puis des listes qui se sont inscrites a cet event
    $event=getHash($hash, $conn);
    if (!isset($event)){
        //redirection vers une page d'erreur
        header('Location: erreur.html');
        exit();
    }
    $listes=getListes($event, $conn);
    //récupération des id des listes, pour les mettre dans un tableau
    $idListes=array();
    foreach($listes as $liste){
        array_push($idListes, $liste['id']);
    }
    //vérification si vote est dans les id des listes
    if(in_array($vote, $idListes)){
        //création d'un nouveau hash aléatoire
        $participation = hash('sha256', random_bytes(32));
        //enregistrement du vote dans la base de données
        $result=addVote($vote, $participation,$event, $conn);
        if ($result){
            //suppression du hash dans la base de données
            $result2=deleteHash($hash, $conn);
            if(!$result2){
                //redirection vers une page d'erreur
                header('Location: erreur.html');
                exit();
            }
            else{
                //redirection vers une page de confirmation
                header('Location: confirmationVote.php?hash='.$participation);
                exit();
            }
        }
        else{
            //redirection vers une page d'erreur
            header('Location: erreur.html');
            exit();
        }
    }
    else{
        //redirection vers une page d'erreur
        header('Location: erreur.html');
        exit();
    }
}
function resultats($equipe){
    global $conn;
    $result=getVotes($equipe, $conn);
    return $result;
}
function SendMail($to, $subject, $lien, $event){
    $param = parse_ini_file('./private/parametres.ini');
    $smtp_host = $param['smtp_host'] ?? '';
    $smtp_port = $param['smtp_port'] ?? 587;
    $smtp_user = $param['smtp_user'] ?? '';
    $smtp_pass = $param['smtp_pass'] ?? '';
    $message = "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='utf-8'>
        <title>Inscription pour les votes</title>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' type='text/css' href='https://vote.remcorp.fr/styles.css'>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <img src='https://vote.remcorp.fr/bgsharklo.jpg' alt='Logo du site'>
            </div>
            <h1>Inscription pour les votes de : ".$event."</h1>
            <p class='reussite'>Cliquez sur ce lien pour effectuer votre vote : <a href=".$lien.">Lien</a></p>
        </div>
    </body>
    </html>";
    $mail= new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_user;
        $mail->Password = $smtp_pass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtp_port;
        //Recipients
        $mail->setFrom('vote@remcorp.fr', $event);
        $mail->addAddress($to);
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->send();
    } catch (Exception $e) {
        //redirection vers une page d'erreur
        global $DEBUG;
        if ($DEBUG) {
            echo '<b>Erreur d\'envoi de mail :</b> ' . $e->getMessage();
        }
        header('Location: erreur.html');
    }   
}
function deleteaccent($string){
    $search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
	$replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');
	$string = str_replace($search, $replace, $string);
    return $string;
}
function getEquipeVote($hash){
    global $conn;
    $result=getEquipe($hash, $conn);
    if ($result){
        if ($result == "1"){
            return "Couniamamaw";
        }
        else{
            return "Medrick";
        }
    }
    else{
        //redirection vers une page d'erreur
        header('Location: erreur.html');
        exit();
    }
}
?>