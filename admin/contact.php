<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Contact & FAQ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../public/assets/css/styles.css">
    <?php 
    session_start();
    require_once __DIR__ . '/../src/includes/fonctionsPHP.php';
    printFaviconTag(); 
    addDarkModeScript();
    $supportEmail = isset($param['support_email']) ? $param['support_email'] : 'support@' . $DOMAIN;
    ?>
</head>
<body>
    <?php include '../src/includes/inc_header.php'; ?>
    <?php include '../src/includes/inc_admin_menu.php'; ?>
    <div class="container card">
        <h1>💬 Contact & FAQ</h1>
        <h2>Questions fréquentes</h2>
        <ul style="line-height: 2;">
            <li><strong>Comment voter ?</strong> <br>Inscrivez-vous via le lien fourni par l'organisateur, puis suivez le lien reçu par mail.</li>
            <li><strong>Comment vérifier mon vote ?</strong> <br>Utilisez la page "Vérifier son vote" et entrez le hash reçu après votre vote.</li>
            <li><strong>Je n'ai pas reçu de mail, que faire ?</strong> <br>Vérifiez vos spams ou contactez l'organisateur de l'événement.</li>
            <li><strong>Comment devenir organisateur ?</strong> <br>Inscrivez-vous via la page dédiée aux organisateurs.</li>
        </ul>
        <h2>Contact</h2>
        <p>Pour toute question ou problème technique, contactez-nous à : <a href="mailto:<?php echo htmlspecialchars($supportEmail); ?>" class="btn"><?php echo htmlspecialchars($supportEmail); ?></a></p>
    </div>
</body>
</html>

