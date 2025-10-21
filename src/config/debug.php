<?php
/**
 * Fichier de configuration du mode debug
 * À inclure EN PREMIER dans chaque page pour afficher les erreurs PHP
 */

$param = parse_ini_file(__DIR__ . '/../../private/parametres.ini');
$DEBUG = isset($param['debug']) && ($param['debug'] === true || $param['debug'] === 'true');

if ($DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
?>
