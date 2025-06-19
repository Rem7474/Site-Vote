<?php
// Page de résultats dynamique pour un événement
include 'fonctionsPHP.php';
include 'inc_header.php';

// Vérification de l'id de l'événement
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Erreur : aucun événement sélectionné.</p>";
    exit();
}
$eventId = $_GET['id'];
$event = getEvent($eventId, $conn);
if (!$event) {
    echo "<p>Erreur : événement introuvable.</p>";
    exit();
}
// Détermination du logo de l'organisateur
$logoPath = 'bgsharklo.jpg';
if (isset($event['reforga'])) {
    foreach(['jpg','png','webp'] as $ext) {
        $customLogo = './images/logo_' . $event['reforga'] . '.' . $ext;
        if (file_exists($customLogo)) {
            $logoPath = $customLogo;
            break;
        }
    }
}
// Récupération des listes/candidats de l'événement
$listes = getListes($eventId, $conn);
if (!$listes || count($listes) === 0) {
    echo "<p>Aucune liste/candidat pour cet événement.</p>";
    exit();
}
// Récupération des votes pour chaque liste
$totalVotes = 0;
$votesParListe = [];
foreach ($listes as $liste) {
    $votes = getVotes($liste['id'], $conn);
    $votesParListe[$liste['id']] = $votes;
    $totalVotes += $votes;
}
// Calcul des pourcentages et détermination du gagnant
$maxVotes = max($votesParListe);
$gagnants = [];
foreach ($listes as $liste) {
    if ($votesParListe[$liste['id']] == $maxVotes) {
        $gagnants[] = $liste;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Résultats du vote</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <?php printFaviconTag(); addDarkModeScript(); ?>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="<?php echo $logoPath; ?>" alt="Logo du site">
        </div>
        <h1>Résultats du vote pour l'événement : <?php echo htmlspecialchars($event['nom']); ?></h1>
        <div class="result">
            <h2>Résumé des votes :</h2>
            <?php foreach ($listes as $liste): 
                $votes = $votesParListe[$liste['id']];
                $percent = $totalVotes > 0 ? round($votes / $totalVotes * 100) : 0;
            ?>
            <div class="progress-wrapper">
                <p><?php echo htmlspecialchars($liste['nom']); ?></p>
                <progress value="<?php echo $percent; ?>" max="100"></progress>
                <span><?php echo $percent; ?>% (<?php echo $votes; ?> votes)</span>
            </div>
            <?php endforeach; ?>
            <p>Total des votes : <?php echo $totalVotes; ?></p>
        </div>
        <div class="winner">
            <h2>Gagnant<?php echo count($gagnants) > 1 ? 's' : ''; ?> :</h2>
            <?php foreach ($gagnants as $gagnant): ?>
                <div class="gagnant-block">
                    <strong><?php echo htmlspecialchars($gagnant['nom']); ?></strong><br>
                    <img src="./images/<?php echo htmlspecialchars($gagnant['photo']); ?>" alt="Photo de la liste gagnante" style="max-width:150px;">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div style="text-align:center;margin:30px 0 0 0;">
        <button class="btn" onclick="navigator.clipboard.writeText(window.location.href)" style="padding:10px 30px;font-size:1.1em;">📋 Copier le lien de cette page</button>
    </div>
</body>
</html>
