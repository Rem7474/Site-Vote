<?php
// Statistiques avancées et historique temporel des votes pour un événement
session_start();
include 'fonctionsPHP.php';
if (!isset($_SESSION['id']) || !isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}
$eventId = $_GET['id'];
$event = getEvent($eventId, $conn);
if (!$event || $event['reforga'] != $_SESSION['id']) {
    echo "<p>Accès refusé.</p>";
    exit();
}
$listes = getListes($eventId, $conn);
// Récupération de l'évolution temporelle des votes (par heure)
$labels = [];
$datasets = [];
$colors = ["#4f8cff", "#28a745", "#e67e22", "#e74c3c", "#8e44ad", "#16a085", "#f39c12"];
foreach ($listes as $i => $liste) {
    // On suppose qu'il existe une fonction getVotesTimeline($listeId, $conn) qui retourne un tableau ['YYYY-MM-DD HH' => nbVotes]
    $timeline = function_exists('getVotesTimeline') ? getVotesTimeline($liste['id'], $conn) : [];
    foreach ($timeline as $heure => $nb) {
        if (!in_array($heure, $labels)) $labels[] = $heure;
    }
    $datasets[] = [
        'label' => $liste['nom'],
        'data' => $timeline,
        'color' => $colors[$i % count($colors)]
    ];
}
// Tri des labels (heures)
sort($labels);
// Préparation des données pour Chart.js
$chartData = [];
foreach ($datasets as $ds) {
    $data = [];
    foreach ($labels as $h) {
        $data[] = isset($ds['data'][$h]) ? $ds['data'][$h] : 0;
    }
    $chartData[] = [
        'label' => $ds['label'],
        'data' => $data,
        'borderColor' => $ds['color'],
        'backgroundColor' => $ds['color'],
        'fill' => false
    ];
}
// Statistiques globales
$totalVotes = 0;
$votesParListe = [];
foreach ($listes as $liste) {
    $votes = getVotes($liste['id'], $conn);
    $votesParListe[$liste['nom']] = $votes;
    $totalVotes += $votes;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Statistiques avancées</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'inc_header.php'; ?>
<?php include 'inc_admin_menu.php'; ?>
<div class="container">
    <h1>Statistiques avancées pour l'événement : <?php echo htmlspecialchars($event['nom']); ?></h1>
    <h2>Évolution temporelle des votes</h2>
    <canvas id="timelineChart" width="600" height="300"></canvas>
    <script>
    const timelineData = {
        labels: <?php echo json_encode($labels); ?>,
        datasets: <?php echo json_encode($chartData); ?>
    };
    new Chart(document.getElementById('timelineChart').getContext('2d'), {
        type: 'line',
        data: timelineData,
        options: {
            responsive: true,
            plugins: {legend: {position: 'top'}},
            scales: {x: {title: {display: true, text: 'Heure'}}, y: {title: {display: true, text: 'Votes'}}}
        }
    });
    </script>
    <h2>Statistiques globales</h2>
    <ul>
        <li>Total de votes : <strong><?php echo $totalVotes; ?></strong></li>
        <?php foreach ($votesParListe as $nom => $nb): ?>
            <li><?php echo htmlspecialchars($nom); ?> : <?php echo $nb; ?> votes</li>
        <?php endforeach; ?>
    </ul>
    <a href="dashboard.php">Retour au dashboard</a>
</div>
</body>
</html>
