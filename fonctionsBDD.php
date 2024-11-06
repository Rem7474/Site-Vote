<?php
// ********** TABLE PARTICIPANTS : id, hash, RefEvent **********
//fonction pour ajouter un hash dans la base de données
function addHash($hash, $IDevent, $conn){
    $sql = "INSERT INTO participants (hash, RefEvent) VALUES (:hash, :idevent) returning id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hash', $hash);
    $stmt->bindParam(':idevent', $IDevent);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour vérifier si un hash existe dans la base de données et renvoyer l'id de l'événement associé
function getHash($hash, $conn){
    $sql = "SELECT RefEvent FROM participants WHERE hash = :hash";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hash', $hash);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
function deleteHash($hash, $conn){
    $sql = "DELETE FROM participants WHERE hash = :hash returning id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hash', $hash);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
// fonction pour récupérer le nombre de participants en attente de vote pour un événement
function getNbParticipants($IDevent, $conn){
    $sql = "SELECT COUNT(*) FROM participants WHERE RefEvent = :IDevent";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':IDevent', $IDevent);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
// ********** TABLE EVENEMENTS : id, Nom, Univ, RefOrga **********
//fonction pour ajouter un événement dans la base de données
function addEvent($Nom, $Univ, $RefOrga, $conn){
    $sql = "INSERT INTO evenements (Nom, Univ, RefOrga) VALUES (:Nom, :Univ, :RefOrga) returning id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Nom', $Nom);
    $stmt->bindParam(':Univ', $Univ);
    $stmt->bindParam(':RefOrga', $RefOrga);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour récupérer les informations d'un événement
function getEvent($IDevent, $conn){
    $sql = "SELECT * FROM evenements WHERE id = :IDevent";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':IDevent', $IDevent);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result;
}
function getNomEvent($IDevent, $conn){
    $sql = "SELECT Nom FROM evenements WHERE id = :IDevent";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':IDevent', $IDevent);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour récupérer l'université associée a un événement
function getUniversity($IDevent, $conn){
    $sql = "SELECT Univ FROM evenements WHERE id = :IDevent";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':IDevent', $IDevent);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour récupérer les événements d'un organisateur
function getEventsOrga($RefOrga, $conn){
    $sql = "SELECT * FROM evenements WHERE reforga = :RefOrga";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':RefOrga', $RefOrga);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result;
}


// ********** TABLE VOTES : id, RefListe, hash, RefEvent **********
//fonction pour enregistrer un vote dans la base de données
function addVote($liste, $hash, $IDevent, $conn){
    $sql = "INSERT INTO votes (RefListe,hash) VALUES (:liste,:hash) returning id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':liste', $vote);
    $stmt->bindParam(':hash', $hash);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour récupérer le nombre de votes pour une liste
function getVotes($liste, $conn){
    $sql = "SELECT COUNT(*) FROM votes WHERE RefListe = :liste ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':liste', $liste);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour récupérer la liste pour laquelle un vote a été effectué
function getEquipe($hash, $conn){
    $sql = "SELECT Nom FROM votes INNER JOIN listes ON votes.RefListe = listes.id WHERE hash = :hash";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hash', $hash);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour récupérer le nombre de votes pour un événement
function getNbVotes($IDevent, $conn){
    $sql = "SELECT COUNT(*) FROM votes INNER JOIN listes ON votes.RefListe = listes.id WHERE RefEvent = :IDevent";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':IDevent', $IDevent);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
// ********** TABLE UTILISATEURS : id, login, email, RefEvent **********
function addUser($login, $email, $IDevent, $conn){
    $sql = "INSERT INTO utilisateurs (login, email, RefEvent) VALUES (:login, :email, :idevent) returning id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':login', $login);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':idevent', $IDevent);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour vérifier l'existence d'un utilisateur a un evenement dans la base de données 
function getUser($login, $IDevent, $conn){
    $sql = "SELECT * FROM utilisateurs WHERE login = :login AND idevent = :IDevent";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':login', $login);
    $stmt->bindParam(':event', $event);
    $stmt->execute();
    $result = $stmt->fetch();
    if($result){
        return true;
    }
    else{
        return false;
    }
}
//fonction pour récupérer tous les utilisateurs d'un événement
function getUsers($IDevent, $conn){
    $sql = "SELECT login, email FROM utilisateurs WHERE RefEvent = :IDevent";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':IDevent', $IDevent);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour supprimer un utilisateur de la base de données
function deleteUser($login, $IDevent, $conn){
    $sql = "DELETE FROM utilisateurs WHERE login = :login AND RefEvent = :IDevent returning id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':login', $login);
    $stmt->bindParam(':IDevent', $IDevent);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
// ********** TABLE LISTES : id, RefEvent, Nom, Photo, Description **********
//fonction pour ajouter une liste dans la base de données
function addListe($Nom, $Photo, $Description, $IDevent, $conn){
    $sql = "INSERT INTO listes (Nom, Photo, Description, RefEvent) VALUES (:Nom, :Photo, :Description, :idevent) returning id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Nom', $Nom);
    $stmt->bindParam(':Photo', $Photo);
    $stmt->bindParam(':Description', $Description);
    $stmt->bindParam(':idevent', $IDevent);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour récupérer la liste des listes pour un événement
function getListes($IDevent, $conn){
    $sql = "SELECT Nom, Photo, Description FROM listes WHERE RefEvent = :IDevent";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':IDevent', $IDevent);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result;
}
//fonction pour supprimer une liste de la base de données
function deleteListe($Nom, $IDevent, $conn){
    $sql = "DELETE FROM listes WHERE Nom = :Nom AND RefEvent = :IDevent returning id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Nom', $Nom);
    $stmt->bindParam(':IDevent', $IDevent);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
// ********** TABLE ORGANISATEURS : id, email, password, Nom, Prenom **********
//fonction pour ajouter un organisateur dans la base de données
function addOrga($email, $password, $Nom, $Prenom, $conn){
    $sql = "INSERT INTO organisateurs (email, password, Nom, Prenom) VALUES (:email, :password, :Nom, :Prenom) returning id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':Nom', $Nom);
    $stmt->bindParam(':Prenom', $Prenom);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour vérifier l'existence d'un organisateur dans la base de données
function getOrga($email, $conn){
    $sql = "SELECT * FROM organisateurs WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetch();
    if($result){
        return true;
    }
    else{
        return false;
    }
}
//fonction pour récupérer les informations d'un organisateur
function getInfosOrga($email, $conn){
    $sql = "SELECT * FROM organisateurs WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result;
}
// ********** TABLE MEMBRES : id, Nom, Prenom, Fonction, RefListe **********
//fonction pour ajouter un membre dans la base de données
function addMembre($Nom, $Prenom, $Fonction, $RefListe, $conn){
    $sql = "INSERT INTO membres (Nom, Prenom, Fonction, RefListe) VALUES (:Nom, :Prenom, :Fonction, :RefListe) returning id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Nom', $Nom);
    $stmt->bindParam(':Prenom', $Prenom);
    $stmt->bindParam(':Fonction', $Fonction);
    $stmt->bindParam(':RefListe', $RefListe);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour récupérer la liste des membres d'une liste
function getMembres($RefListe, $conn){
    $sql = "SELECT Nom, Prenom, Fonction FROM membres WHERE RefListe = :RefListe";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':RefListe', $RefListe);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour supprimer un membre de la base de données
function deleteMembre($Nom, $Prenom, $RefListe, $conn){
    $sql = "DELETE FROM membres WHERE Nom = :Nom AND Prenom = :Prenom AND RefListe = :RefListe returning id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Nom', $Nom);
    $stmt->bindParam(':Prenom', $Prenom);
    $stmt->bindParam(':RefListe', $RefListe);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
?>