<?php
function ConnexionBDD($fichierParametre) {
    $param = parse_ini_file($fichierParametre);
    $dsn = 'pgsql:host=' . $param['lehost'] . ';dbname=' . $param['dbname'] . ';port=' . $param['leport'];
    try {
        $connex = new PDO($dsn, $param['user'], $param['pass']);
    } catch (PDOException $e) {
        if (isset($param['debug']) && ($param['debug'] === true || $param['debug'] === 'true')) {
            echo "Erreur de connexion à la base de données : " . $e->getMessage();
        }
        die("");
    }
    if (isset($param['debug']) && ($param['debug'] === true || $param['debug'] === 'true')) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
    return $connex;
}
function DeconnexionBDD($connex) {
    $connex = null;
}
?>