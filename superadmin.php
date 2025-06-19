<?php
session_start();
include 'fonctionsPHP.php';
$param = parse_ini_file('./private/parametres.ini');
$superadmin_user = $param['superadmin_user'] ?? '';
$superadmin_pass = $param['superadmin_pass'] ?? '';

// Authentification super admin
if (!isset($_SESSION['superadmin'])) {
    if (isset($_POST['login']) && isset($_POST['password'])) {
        if ($_POST['login'] === $superadmin_user && $_POST['password'] === $superadmin_pass) {
            $_SESSION['superadmin'] = true;
            header('Location: superadmin.php');
            exit();
        } else {
            $error = 'Identifiants incorrects';
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Connexion Super Admin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body>
        <div class="container card">
            <h1>Connexion Super Admin</h1>
            <?php if (isset($error)) echo '<p class="erreur">'.$error.'</p>'; ?>
            <form method="post">
                <label for="login">Login</label>
                <input type="text" name="login" required>
                <label for="password">Mot de passe</label>
                <input type="password" name="password" required>
                <input type="submit" value="Se connecter" class="btn">
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}
// Récupération des organisateurs et de leurs événements
$organisateurs = $conn->query('SELECT * FROM organisateurs')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Super Admin - Gestion des organisateurs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div class="container card">
    <h1>Super Admin</h1>
    <h2>Liste des organisateurs</h2>
    <div class="table-responsive">
    <table>
        <tr><th>Nom</th><th>Prénom</th><th>Email</th><th>Événements</th></tr>
        <?php foreach ($organisateurs as $orga):
            $events = $conn->prepare('SELECT * FROM evenements WHERE reforga = ?');
            $events->execute([$orga['id']]);
            $events = $events->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <tr>
            <td><?php echo htmlspecialchars($orga['nom']); ?></td>
            <td><?php echo htmlspecialchars($orga['prenom']); ?></td>
            <td><?php echo htmlspecialchars($orga['email']); ?></td>
            <td>
                <?php if ($events): ?>
                    <ul>
                        <?php foreach ($events as $event): ?>
                            <li><?php echo htmlspecialchars($event['nom']); ?> (<?php echo htmlspecialchars($event['univ']); ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    Aucun événement
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <form method="post" action="logout.php" style="margin-top:20px;">
        <input type="submit" value="Déconnexion" class="btn">
    </form>
</div>
</body>
</html>
