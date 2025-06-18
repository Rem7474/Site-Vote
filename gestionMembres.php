<?php
// Page de gestion des membres d'une liste
session_start();
include 'fonctionsPHP.php';
// Vérification de la connexion organisateur
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}
$idOrga = $_SESSION['id'];
// Gestion du logo personnalisé
$logoPath = 'bgsharklo.jpg';
$customLogo = './images/logo_' . $idOrga . '.jpg';
if (file_exists($customLogo)) {
    $logoPath = $customLogo;
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}
$idListe = $_GET['id'];
$liste = null;
$event = null;
// Récupération de la liste et de l'événement associé
$listes = getListes(null, $conn); // getListes($IDevent, $conn) mais on ne connait pas l'event ici
foreach ($listes as $l) {
    if ($l['id'] == $idListe) {
        $liste = $l;
        $event = getEvent($l['refevent'], $conn);
        break;
    }
}
if (!$liste) {
    echo "<p>Liste introuvable.</p>";
    exit();
}
// Ajout d'un membre
if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['fonction'])) {
    addMembre($_POST['nom'], $_POST['prenom'], $_POST['fonction'], $idListe, $conn);
    echo "<script>window.toastMessage='Membre ajouté avec succès';window.toastType='success';</script>";
    header('Refresh:1;url=gestionMembres.php?id=' . $idListe);
    exit();
}
// Suppression d'un membre
if (isset($_GET['delete']) && isset($_GET['nom']) && isset($_GET['prenom'])) {
    deleteMembre($_GET['nom'], $_GET['prenom'], $idListe, $conn);
    echo "<script>window.toastMessage='Membre supprimé avec succès';window.toastType='success';</script>";
    header('Refresh:1;url=gestionMembres.php?id=' . $idListe);
    exit();
}
$membres = getMembres($idListe, $conn);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Gestion des membres</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="<?php echo $logoPath; ?>" alt="Logo du site">
        </div>
        <a href="event.php?id=<?php echo $liste['refevent']; ?>">Retour à l'événement</a>
        <h1>Membres de la liste : <?php echo htmlspecialchars($liste['nom']); ?></h1>
        <h2>Ajouter un membre</h2>
        <form method="post">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" required>
            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" required>
            <label for="fonction">Fonction :</label>
            <input type="text" name="fonction" required>
            <input type="submit" value="Ajouter">
        </form>
        <h2>Liste des membres</h2>
        <?php if ($membres && count($membres) > 0): ?>
            <table>
                <tr><th>Nom</th><th>Prénom</th><th>Fonction</th><th>Action</th></tr>
                <?php foreach ($membres as $membre): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($membre['nom']); ?></td>
                        <td><?php echo htmlspecialchars($membre['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($membre['fonction']); ?></td>
                        <td><a href="gestionMembres.php?id=<?php echo $idListe; ?>&delete=1&nom=<?php echo urlencode($membre['nom']); ?>&prenom=<?php echo urlencode($membre['prenom']); ?>">Supprimer</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Aucun membre pour cette liste.</p>
        <?php endif; ?>
    </div>
    <?php include 'inc_header.php'; ?>
    <?php include 'inc_admin_menu.php'; ?>
</body>
</html>
