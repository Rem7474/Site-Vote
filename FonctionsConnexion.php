<?php
function ConnexionBDD($fichierParametre) {
    include $fichierParametre; // on "inclut" le fichier source contenant du code
    $dsn='pgsql:host='.$lehost.';dbname='.$dbname.';port='.$leport;
    // connexion à la bdd (connexion non persistante)
    try {
        $connex = new PDO($dsn, $user, $pass); // tentative de connexion
        //print "Connecté :)<br />";// si réussite
    } catch (PDOException $e) { // si échec
        //print "Erreur de connexion à la base de données ! : " . $e->getMessage();
        die(""); // Arrêt du script - sortie.
    }
    if ($debug == true){
        //affichage des erreurs et avertissements PHP
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
    return $connex; // on retourne le connecteur (à l'appelant)
}
function DeconnexionBDD($connex) {
    $connex = null; // fermeture de la connexion a la base de donnees (même si demande de connexion persistante).
}
?>