<?php
//fonction pour ajouter un hash dans la base de données
function addHash($hash, $conn){
    $sql = "INSERT INTO utilisateurs (hash) VALUES (:hash) returning id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hash', $hash);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
//fonction pour vérifier si un hash existe dans la base de données
function getHash($hash, $conn){
    $sql = "SELECT * FROM utilisateurs WHERE hash = :hash";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hash', $hash);
    $stmt->execute();
    $result = $stmt->fetch();
    if($result){
        return true;
    }
    else{
        return false;
    }
}
//fonction pour enregistrer un vote dans la base de données
function addVote($vote, $conn){
    $sql = "INSERT INTO votes (vote) VALUES (:vote)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':vote', $vote);
    $stmt->execute();
}
//fonction pour récupérer le nombre de votes pour un candidat
function getVotes($vote, $conn){
    $sql = "SELECT COUNT(*) FROM votes WHERE vote = :vote";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':vote', $vote);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result[0];
}
?>