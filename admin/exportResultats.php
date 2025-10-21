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

// Préparation du CSV
$eventName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $event['nom']);
$filename = 'resultats_' . $eventName . '_' . date('Y-m-d') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// BOM UTF-8 pour Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// En-têtes
fputcsv($output, ['Événement', htmlspecialchars($event['nom'])]);
fputcsv($output, ['Date export', date('d/m/Y H:i:s')]);
fputcsv($output, ['Université', htmlspecialchars($event['Univ'] ?? '')]);
fputcsv($output, []); // Ligne vide

// Colonnes
fputcsv($output, ['Nom de la liste', 'Description', 'Nombre de votes', 'Pourcentage']);

$totalVotes = 0;
foreach ($listes as $liste) {
    $totalVotes += getVotes($liste['id'], $conn);
}

foreach ($listes as $liste) {
    $votes = getVotes($liste['id'], $conn);
    $percentage = $totalVotes > 0 ? round(($votes / $totalVotes) * 100, 2) : 0;
    
    fputcsv($output, [
        $liste['nom'],
        $liste['description'],
        $votes,
        $percentage . '%'
    ]);
}

fputcsv($output, []); // Ligne vide
fputcsv($output, ['Total des votes', '', $totalVotes, '100%']);

fclose($output);
exit();
?>