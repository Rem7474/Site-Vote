<?php
//récupération des résultats des votes
include 'fonctionsPHP.php';
$vote1 = resultats("1");
$vote2 = resultats("2");
//calcul du pourcentage des votes
$total = $vote1 + $vote2;
$percent1 = round($vote1 / $total * 100);
$percent2 = round($vote2 / $total * 100);
//détermination du gagnant
if($vote1 == $vote2){
    $winner = "Egalité !";
    $winnerImg = "candidat1.jpg";
}
else if($vote1 > $vote2){
    $winner = "Equipe de Couniamamaw";
    $winnerImg = "candidat1.jpg";
}
else{
    $winner = "Equipe de Medrick";
    $winnerImg = "candidat2.jpg";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Résultats du vote</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Insérez votre logo ici -->
            <img src="bgsharklo.jpg" alt="Logo du site">
        </div>
        <h1>Résultats du vote pour le BDE R&T</h1>
        <div class="result">
            <h2>Résumé des votes :</h2>
            <div class="progress-wrapper">
                <p>Equipe de Couniamamaw</p>
                <progress value="<?php echo $percent1; ?>" max="100"></progress>
                <span><?php echo $percent1; ?>%</span>
            </div>
            <div class="progress-wrapper">
                <p>Equipe de Medrick</p>
                <progress value="<?php echo $percent2; ?>" max="100"></progress>
                <span><?php echo $percent2; ?>%</span>
            </div>
        </div>
        <div class="winner">
            <h2>Le gagnant est : <?php echo $winner; ?></h2>
            <img src="<?php echo $winnerImg; ?>" alt="Photo de l'équipe gagnante">
        </div>
    </div>
</body>
</html>
