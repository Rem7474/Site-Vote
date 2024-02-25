<?php
//connexion à la base de données
include 'FonctionsConnexion.php';
include 'fonctionsBDD.php';
$conn = connexionBDD('./private/parametres.ini');

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
    $nom = strtr($nom, "éèêëàâäôöûüîïç", "eeeeaaaoouuiic");
    $prenom = strtr($prenom, "éèêëàâäôöûüîïç", "eeeeaaaoouuiic");
    //retire les espaces
    $nom = str_replace(' ', '', $nom);
    $prenom = str_replace(' ', '', $prenom);
    //création du login avec les 6 premières lettres du nom et les 2 premières lettres du prénom
    $login = substr($nom, 0, 6).substr($prenom, 0, 2);
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
        mail($email, "[Vote BDE R&T] Confirmation d'inscription", "Cliquez sur ce lien pour effectué votre vote : https://vote.remcorp.fr/vote.php?hash=".$hash);
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
    if(hashExiste($hash, $conn) && ($vote==1 || $vote==2)){
        //enregistrement du vote dans la base de données
        $result=addVote($vote, $conn);
        if ($result){
            //redirection vers une page de confirmation
            header('Location: confirmationVote.php?hash='.$hash);
            exit();
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
?>