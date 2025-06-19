<?php
// Gestion des utilisateurs (votants) pour un événement
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
// Suppression d'un utilisateur
if (isset($_GET['delete']) && isset($_GET['login'])) {
    deleteUser($_GET['login'], $eventId, $conn);
    echo "<script>window.toastMessage='Utilisateur supprimé avec succès';window.toastType='success';</script>";
    header('Refresh:1;url=gestionUtilisateurs.php?id=' . $eventId);
    exit();
}
$users = getUsers($eventId, $conn);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php include 'inc_header.php'; ?>
<?php include 'inc_admin_menu.php'; ?>
<div class="container">
    <h1>Utilisateurs inscrits à l'événement : <?php echo htmlspecialchars($event['nom']); ?></h1>
    <?php if ($users && count($users) > 0): ?>
        <table>
            <tr><th>Login</th><th>Email</th><th>Action</th></tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['login']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><a href="gestionUtilisateurs.php?id=<?php echo $eventId; ?>&delete=1&login=<?php echo urlencode($user['login']); ?>" class="btn double-confirm">Supprimer</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Aucun utilisateur inscrit.</p>
    <?php endif; ?>
    <a href="dashboard.php">Retour au dashboard</a>
</div>
</body>
</html>
