<?php
// Export des résultats d'un événement au format CSV
session_start();
include '../src/includes/fonctionsPHP.php';

// Vérifier que l'utilisateur est connecté
requireLogin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Erreur : événement non spécifié.');
}

$eventId = sanitizeInput($_GET['id']);
$event = getEvent($eventId, $conn);

if (!$event) {
    die('Erreur : événement introuvable.');
}

// Vérifier que l'utilisateur est bien le propriétaire de l'événement
if ($event['reforga'] != $_SESSION['id']) {
    logSecurityEvent('UNAUTHORIZED_EXPORT', "User: {$_SESSION['id']} - Event: $eventId", 'WARNING');
    die('Erreur : accès non autorisé.');
}

$listes = getListes($eventId, $conn);
if (!$listes || count($listes) === 0) {
    die('Aucune liste/candidat pour cet événement.');
}

// Log de l'export
logSecurityEvent('RESULTS_EXPORTED', "Event: $eventId - User: {$_SESSION['id']}", 'INFO');

// Préparation du fichier Excel (CSV avec séparateur point-virgule)
$eventName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $event['nom']);
$filename = 'resultats_' . $eventName . '_' . date('Y-m-d') . '.csv';

// Headers pour Excel
header('Content-Type: text/csv; charset=Windows-1252');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// Fonction pour encoder en Windows-1252
function encodeForExcel($text) {
    return mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
}

// En-têtes (avec point-virgule comme séparateur pour Excel)
fputcsv($output, [
    encodeForExcel('Événement'), 
    encodeForExcel($event['nom'])
], ';');

fputcsv($output, [
    encodeForExcel('Date export'), 
    date('d/m/Y H:i:s')
], ';');

fputcsv($output, [
    encodeForExcel('Université'), 
    encodeForExcel($event['Univ'] ?? '')
], ';');

fputcsv($output, [], ';'); // Ligne vide

// Colonnes
fputcsv($output, [
    encodeForExcel('Nom de la liste'), 
    encodeForExcel('Description'), 
    encodeForExcel('Nombre de votes'), 
    encodeForExcel('Pourcentage')
], ';');

$totalVotes = 0;
foreach ($listes as $liste) {
    $totalVotes += getVotes($liste['id'], $conn);
}

foreach ($listes as $liste) {
    $votes = getVotes($liste['id'], $conn);
    $percentage = $totalVotes > 0 ? round(($votes / $totalVotes) * 100, 2) : 0;
    
    fputcsv($output, [
        encodeForExcel($liste['nom']),
        encodeForExcel($liste['description']),
        $votes,
        str_replace('.', ',', $percentage) . '%'  // Virgule décimale pour Excel français
    ], ';');
}

fputcsv($output, [], ';'); // Ligne vide
fputcsv($output, [
    encodeForExcel('Total des votes'), 
    '', 
    $totalVotes, 
    '100%'
], ';');

fclose($output);
exit();
?>