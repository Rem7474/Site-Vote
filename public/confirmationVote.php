<?php
// Vérification de la présence du hash de vote
if (!isset($_GET["hash"])) {
    header("Location: index.php");
    exit();
}
//longueur du hash
$hashLength = 64;
// Vérification de la longueur du hash
if (strlen($_GET["hash"]) != $hashLength) {
    header("Location: index.php");
    exit();
}
$hash=$_GET["hash"];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>✅ Vote confirmé - Vote en ligne</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
    <?php 
    include_once 'fonctionsPHP.php';
    printFaviconTag(); 
    addDarkModeScript(); 
    ?>
</head>
<body>
    <div class="container card">
        <div class="header">
            <img src="bgsharklo.jpg" alt="Logo du site">
        </div>
        <div class="card" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-left: 5px solid #28a745; margin-bottom: 20px;">
            <h1 style="color: #155724; margin: 0 0 10px 0;">🎉 Vote enregistré avec succès !</h1>
            <p style="color: #155724; font-size: 1.1em; margin: 0;">Bravo, votre vote a bien été pris en compte.<br>Merci pour votre participation ! 🙏</p>
        </div>
        
        <div class="card" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%); border-left: 5px solid #ffc107;">
            <h2 style="color: #856404; margin: 0 0 10px 0;">🔐 Hash de vérification</h2>
            <p style="color: #856404; margin: 0;"><strong>Conservez précieusement ce code pour vérifier votre vote :</strong></p>
            <p class="hash" style="background: #fff; padding: 15px; border-radius: 7px; font-family: monospace; word-break: break-all; margin: 10px 0; color: #333; font-weight: bold;"><?php echo htmlspecialchars($hash); ?></p>
            <button class="btn" onclick="navigator.clipboard.writeText('<?php echo htmlspecialchars($hash); ?>')" style="width: 100%; margin: 10px 0;">📋 Copier le hash</button>
        </div>
        
        <div class="footer">
            <p><a href="checkVote.php" class="btn">🔍 Vérifier mon vote maintenant</a></p>
        </div>
    </div>
</body>
</html>

