<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Vérifier son vote</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Insérez votre logo ici -->
            <img src="bgsharklo.jpg" alt="Logo du site">
        </div>
        <h1>Vérifier son vote pour le BDE R&T</h1>
        <p>Veuillez entrer le hash de votre vote pour vérifier l'équipe pour laquelle vous avez voté :</p>
        <form action="checkVote.php" method="post">
            <input type="text" name="hash" placeholder="Entrez votre hash de vote" required>
            <input type="submit" value="Vérifier">
        </form>
        <div class="result" id="result">
            <?php
            include 'fonctionsPHP.php';
            //récupération du hash
            if(isset($_POST['hash'])){
                //vérification du hash
                $hash = $_POST['hash'];
                $equipe = getEquipeVote($hash);
                if($equipe != ""){
                    //récupération de l'équipe pour laquelle le vote a été effectué
                    echo "<p class='reussite'>Vous avez voté pour l'équipe de $equipe</p>";
                }
                else{
                    echo "<p>Le hash de vote n'existe pas</p>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
