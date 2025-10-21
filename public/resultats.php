<?php
// Page de résultats dynamique pour un événement
session_start();
include '../src/includes/fonctionsPHP.php';

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php printFaviconTag(); addDarkModeScript(); ?>
</head>
<body>
    <?php if (isset($_SESSION['id'])): ?>
        <?php include '../src/includes/inc_header.php'; ?>
    <?php endif; ?>
    <div class="container card">
        <h1>📊 Résultats du vote</h1>
        <h2><?php echo htmlspecialchars($event['nom']); ?></h2>
        
        <!-- Section gagnant en haut -->
        <div class="winner" style="margin: 30px 0;">
            <div class="card" style="background: linear-gradient(135deg, #fff9c4 0%, #fff59d 100%); border-left: 5px solid #fdd835; padding: 25px;">
                <h2 style="color: #f57f17; margin: 0 0 15px 0; text-align: center;">🏆 Gagnant<?php echo count($gagnants) > 1 ? 's' : ''; ?></h2>
                <?php foreach ($gagnants as $gagnant): ?>
                    <div class="gagnant-block" style="text-align: center; margin: 10px 0;">
                        <p style="font-size: 1.5em; font-weight: bold; color: #f57f17; margin: 10px 0;"><?php echo htmlspecialchars($gagnant['nom']); ?></p>
                        <?php if (!empty($gagnant['photo'])): ?>
                            <img src="../images/<?php echo htmlspecialchars($gagnant['photo']); ?>" alt="Photo de la liste gagnante" style="max-width:150px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); margin: 10px 0;">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Mise en page 2 colonnes : graphique + détails -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin: 30px 0; align-items: start;">
            
            <!-- Colonne gauche : Graphique -->
            <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <h3 style="text-align: center; margin: 0 0 20px 0; color: #2d3a4b;">📊 Répartition des votes</h3>
                <canvas id="votesPieChart"></canvas>
            </div>
            
            <!-- Colonne droite : Décompte détaillé -->
            <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <h3 style="text-align: center; margin: 0 0 20px 0; color: #2d3a4b;">📈 Décompte détaillé</h3>
                <div class="result">
                    <?php foreach ($listes as $liste): 
                        $votes = $votesParListe[$liste['id']];
                        $percent = $totalVotes > 0 ? round($votes / $totalVotes * 100) : 0;
                    ?>
                    <div class="progress-wrapper" style="margin-bottom: 15px;">
                        <p style="margin-bottom: 5px; font-weight: 600;"><?php echo htmlspecialchars($liste['nom']); ?></p>
                        <progress value="<?php echo $percent; ?>" max="100" style="width: 100%;"></progress>
                        <span style="display: block; text-align: right; margin-top: 3px; font-size: 0.95em;"><?php echo $percent; ?>% (<?php echo $votes; ?> votes)</span>
                    </div>
                    <?php endforeach; ?>
                    <div style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-left: 5px solid #2196f3; margin-top: 20px; padding: 15px; border-radius: 8px;">
                        <p style="color: #0d47a1; font-size: 1.1em; margin: 0; text-align: center;"><strong>Total des votes : <?php echo $totalVotes; ?> 🗳️</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Responsive : passer en colonne unique sur petits écrans -->
        <style>
            @media (max-width: 768px) {
                div[style*="grid-template-columns: 1fr 1fr"] {
                    grid-template-columns: 1fr !important;
                }
            }
        </style>
    </div>
    <div class="footer" style="text-align:center;margin:30px 0 0 0;">
        <button class="btn" onclick="navigator.clipboard.writeText(window.location.href); this.innerHTML='✅ Lien copié!'; setTimeout(() => this.innerHTML='📋 Copier le lien de cette page', 2000);" style="padding:12px 30px;font-size:1.1em;">📋 Copier le lien de cette page</button>
        <p style="margin-top: 15px;"><a href="../admin/login.php" class="btn">🔐 Connexion organisateur</a></p>
    </div>

<script>
// Graphique en camembert Chart.js
const ctx = document.getElementById('votesPieChart').getContext('2d');
const votesData = [
    <?php foreach ($listes as $liste): ?>
    <?php echo $votesParListe[$liste['id']]; ?>,
    <?php endforeach; ?>
];
const votesLabels = [
    <?php foreach ($listes as $liste): ?>
    '<?php echo addslashes(htmlspecialchars($liste['nom'])); ?>',
    <?php endforeach; ?>
];

// Palette de couleurs moderne et contrastée
const colors = [
    '#667eea', // Bleu violet
    '#f093fb', // Rose
    '#4facfe', // Bleu ciel
    '#43e97b', // Vert
    '#fa709a', // Rose corail
    '#fee140', // Jaune
    '#30cfd0', // Turquoise
    '#a8edea', // Vert d'eau
];

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: votesLabels,
        datasets: [{
            data: votesData,
            backgroundColor: colors.slice(0, votesData.length),
            borderColor: '#fff',
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 13,
                        family: "'Segoe UI', sans-serif"
                    },
                    generateLabels: function(chart) {
                        const data = chart.data;
                        if (data.labels.length && data.datasets.length) {
                            return data.labels.map((label, i) => {
                                const value = data.datasets[0].data[i];
                                const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percent = total > 0 ? Math.round((value / total) * 100) : 0;
                                return {
                                    text: `${label}: ${percent}% (${value} votes)`,
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    hidden: false,
                                    index: i
                                };
                            });
                        }
                        return [];
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percent = total > 0 ? Math.round((value / total) * 100) : 0;
                        return `${label}: ${value} votes (${percent}%)`;
                    }
                }
            }
        }
    }
});
</script>
</body>
</html>

