<?php
// Export des résultats d'un événement au format CSV
include '../src/includes/fonctionsPHP.php';
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Erreur : événement non spécifié.');
}
$eventId = $_GET['id'];
$event = getEvent($eventId, $conn);
if (!$event) {
    die('Erreur : événement introuvable.');
}
$listes = getListes($eventId, $conn);
if (!$listes || count($listes) === 0) {
    die('Aucune liste/candidat pour cet événement.');
}
// Préparation du CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="resultats_event_' . $eventId . '.csv"');
$output = fopen('php://output', 'w');
fputcsv($output, ['Nom de la liste', 'Description', 'Nombre de votes']);
foreach ($listes as $liste) {
    $votes = getVotes($liste['id'], $conn);
    fputcsv($output, [
        $liste['nom'],
        $liste['description'],
        $votes
    ]);
}
fclose($output);
exit();
?>

