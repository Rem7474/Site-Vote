<?php
// Page de résultats dynamique pour un événement
include '../src/includes/fonctionsPHP.php';
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
$logoPath = 'assets/images/bgsharklo.jpg';
if (isset($event['reforga'])) {
    foreach(['jpg','png','webp'] as $ext) {
        $customLogo = 'assets/images/logo_' . $event['reforga'] . '.' . $ext;
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
    <title>📊 Résultats - <?php echo htmlspecialchars($event['nom']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
    <?php printFaviconTag(); addDarkModeScript(); ?>
</head>
<body>
    <div class="container card">
        <div class="header">
            <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="Logo du site">
        </div>
        <h1>📊 Résultats du vote</h1>
        <h2><?php echo htmlspecialchars($event['nom']); ?></h2>
        <div class="result">
            <h3>📈 Décompte des votes :</h3>
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
            <div class="card" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-left: 5px solid #2196f3; margin-top: 20px;">
                <p style="color: #0d47a1; font-size: 1.2em; margin: 0;"><strong>Total des votes : <?php echo $totalVotes; ?> 🗳️</strong></p>
            </div>
        </div>
        
        <div class="winner" style="margin-top: 30px;">
            <div class="card" style="background: linear-gradient(135deg, #fff9c4 0%, #fff59d 100%); border-left: 5px solid #fdd835;">
                <h2 style="color: #f57f17; margin: 0 0 15px 0;">🏆 Gagnant<?php echo count($gagnants) > 1 ? 's' : ''; ?></h2>
                <?php foreach ($gagnants as $gagnant): ?>
                    <div class="gagnant-block" style="text-align: center; margin: 20px 0;">
                        <p style="font-size: 1.5em; font-weight: bold; color: #f57f17; margin: 10px 0;"><?php echo htmlspecialchars($gagnant['nom']); ?></p>
                        <?php if (!empty($gagnant['photo']) && file_exists('assets/images/'.$gagnant['photo'])): ?>
                            <img src="assets/images/<?php echo htmlspecialchars($gagnant['photo']); ?>" alt="Photo de la liste gagnante" style="max-width:200px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); margin: 15px 0;">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="footer" style="text-align:center;margin:30px 0 0 0;">
        <button class="btn" onclick="navigator.clipboard.writeText(window.location.href); this.innerHTML='✅ Lien copié!'; setTimeout(() => this.innerHTML='📋 Copier le lien de cette page', 2000);" style="padding:12px 30px;font-size:1.1em;">📋 Copier le lien de cette page</button>
        <p style="margin-top: 15px;"><a href="login.php">🔐 Connexion organisateur</a></p>
    </div>
</body>
</html>

