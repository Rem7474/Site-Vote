<?php
//connexion à la base de données
include 'FonctionsConnexion.php';
include 'fonctionsBDD.php';
$conn = connexionBDD('./private/parametres.ini');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
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
    if(!preg_match('/^[a-z]{6,8}$/', $login)){
        //affichage d'une page html d'erreur
        header('Location: erreur.html');
        exit();
    }
    //création du mail a partir de l'université définie dans la table event
    //récupération de l'université
    $universite = getUniversite($IDevent, $conn);
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
        SendMail($email, "[Vote ".$nomEvent."] Confirmation d'inscription", "https://vote.remcorp.fr/index.php?hash=".$hash);
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
    if(hashExiste($hash, $conn) && ($vote=="1" || $vote=="2")){ // A MODIFIER : VERIF ID LISTE VOTE
        //création d'un nouveau hash aléatoire
        $participation = hash('sha256', random_bytes(32));
        //enregistrement du vote dans la base de données
        $result=addVote($vote, $participation, $conn);
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

function HashExiste($hash){
    global $conn;
    //vérification de l'existence du hash dans la base de données
    if(getHash($hash, $conn)){
        return true;
    }
    else{
        return false;
    }
}
function resultats($equipe){
    global $conn;
    $result=getVotes($equipe, $conn);
    return $result;
}
function SendMail($to, $subject, $message){
    include './private/parametres.ini';
    $message = "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='utf-8'>
        <title>Inscription pour les votes du BDE R&T</title>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' type='text/css' href='https://vote.remcorp.fr/styles.css'>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <img src='https://vote.remcorp.fr/bgsharklo.jpg' alt='Logo du site'>
            </div>
            <h1>Inscription pour les votes du BDE R&T</h1>
            <p class='reussite'>Cliquez sur ce lien pour effectuer votre vote : <a href=".$message.">Lien</a></p>
        </div>
    </body>
    </html>";
    $mail= new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        //Recipients
        $mail->setFrom('vote@remcorp.fr', 'BDE R&T');
        $mail->addAddress($to);
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->send();
    } catch (Exception $e) {
        //redirection vers une page d'erreur
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