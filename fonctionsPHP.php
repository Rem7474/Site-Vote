<?php
//connexion à la base de données
include 'FonctionsConnexion.php';
include 'fonctionsBDD.php';
$conn = connexionBDD('./private/parametres.ini');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
function InscriptionVote($nom, $prenom){
    global $conn;
    //vérication de la taille des noms et prénoms
    if(strlen($nom) < 2 || strlen($nom) > 50 || strlen($prenom) < 2 || strlen($prenom) > 50){
        //redirection vers une page d'erreur
        header('Location: erreur.html');
        exit();
    }
    //mise en minuscule des noms et prénoms
    $nom = strtolower($nom);
    $prenom = strtolower($prenom);
    //retirer les accents
    $nom = deleteaccent($nom);
    $prenom = deleteaccent($prenom);
    //retire les espaces
    $nom = str_replace(' ', '', $nom);
    $prenom = str_replace(' ', '', $prenom);
    //création du login avec les 6 premières lettres du nom et les 2 premières lettres du prénom
    //test si le nom est plus petit que 6 caractères
    if(strlen($nom) < 6){
        $login = $nom.substr($prenom, 0, 1);
    }
    else{
        $login = substr($nom, 0, 6).substr($prenom, 0, 2);
    }
    //création du mail
    $email = $login."@etu.univ-smb.fr";
    //hashage salé avec le timestamp du nom et prénom
    $salt = time();
    $hash = hash('sha256', $nom.$prenom.$salt);
    //envoi du mail de confirmation si la personne n'est pas déjà inscrite
    $user=getUser($login, $conn);
    if($user){
        //redirection vers une page d'erreur
        header('Location: erreur.html');
        exit();
    }
    else{
        SendMail($email, "[Vote BDE R&T] Confirmation d'inscription", "https://vote.remcorp.fr/index.php?hash=".$hash);
        //enregistrement du hash dans la base de données
        $ajout=addHash($hash, $conn);
        $ajout2=addUser($nom, $prenom, $login, $email, $conn);
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
    if(hashExiste($hash, $conn) && ($vote=="1" || $vote=="2")){
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
function resultats(){
    global $conn;
    $result1=getVotes("1", $conn);
    $result2=getVotes("2", $conn);
    return array($result1, $result2);
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
?>