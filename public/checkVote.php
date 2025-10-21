<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>🔍 Vérifier son vote - Vote en ligne</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
    <?php 
    require_once __DIR__ . '/../src/includes/fonctionsPHP.php';
    printFaviconTag(); 
    addDarkModeScript(); 
    ?>
</head>
<body>
    <div class="container card">
        <div class="header">
            <img src="assets/images/bgsharklo.jpg" alt="Logo du site">
        </div>
        <h1>🔍 Vérifier mon vote</h1>
        <p>Entrez le hash de vérification que vous avez reçu par email après avoir voté pour confirmer votre choix :</p>
        <form action="checkVote.php" method="post">
            <label for="hash">🔐 Hash de vérification</label>
            <input type="text" id="hash" name="hash" placeholder="Entrez votre hash de vote" required>
            <small>Le hash vous a été envoyé par email après votre vote</small>
            <input type="submit" value="🔍 Vérifier" class="btn">
        </form>
        <div class="result" id="result">
            <?php
            //récupération du hash
            if(isset($_POST['hash'])){
                //vérification du hash
                $hash = sanitizeInput($_POST['hash']);
                $equipe = getEquipeVote($hash);
                if($equipe != ""){
                    //récupération de l'équipe pour laquelle le vote a été effectué
                    echo "<div class='card' style='background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-left: 5px solid #28a745;'>";
                    echo "<h2 style='color: #155724; margin: 0 0 10px 0;'>✅ Vote confirmé</h2>";
                    echo "<p style='color: #155724; font-size: 1.1em;'>Vous avez voté pour l'équipe : <strong>".htmlspecialchars($equipe)."</strong></p>";
                    echo "</div>";
                }
                else{
                    echo "<div class='card erreur'>";
                    echo "<p style='margin: 0;'>❌ Le hash de vote n'existe pas ou est invalide</p>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>
    <div class="footer">
        <p><a href="../admin/login.php" class="btn">🔐 Connexion organisateur</a></p>
        <p><a href="../admin/register.php" class="btn">📝 S'inscrire en tant qu'organisateur</a></p>
    </div>
</body>
</html>

