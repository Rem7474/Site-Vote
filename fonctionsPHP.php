<?php
//connexion à la base de données
include 'FonctionsConnexion.php';
$conn = ConnexionBDD('parametresBDD.php');
function InscriptionVote($nom, $prenom){
    //vérication de la taille des noms et prénoms
    if(strlen($nom) < 2 || strlen($nom) > 50 || strlen($prenom) < 2 || strlen($prenom) > 50){
        //redirection vers une page d'erreur
        header('Location: erreur.html');
        exit();
    }
    //mise en minuscule des noms et prénoms
    $nom = strtolower($nom);
    $prenom = strtolower($prenom);
    //création du mail
    $email = $prenom.".".$nom."@etu.univ-smb.fr";
    //hashage salé avec le timestamp du nom et prénom
    $salt = time();
    $hash = hash('sha256', $nom.$prenom.$salt);
    //envoi du mail de confirmation
    mail($email, "[Vote BDE R&T] Confirmation d'inscription", "Cliquez sur ce lien pour effectué votre vote : https://vote.remcorp.fr/vote.php?hash=".$hash);
    //enregistrement du hash dans la base de données
    $ajout=ajouterHash($hash, $conn);
    //redirection vers une page de confirmation si l'ajout a réussi
    if($ajout){
        header('Location: confirmationInscription.html');
        exit();
    }
    else{
        //redirection vers une page d'erreur si l'ajout a échoué
        header('Location: erreur.html');
        exit();
    }
}
