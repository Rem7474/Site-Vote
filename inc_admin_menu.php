<?php
// Menu de navigation admin à inclure dans le dashboard et les pages admin
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<nav class="global-menu">
    <a href="dashboard.php">Dashboard</a>
    <a href="contact.php">FAQ/Contact</a>
    <?php if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])): ?>
        <span class="user">Connecté : <?php echo htmlspecialchars($_SESSION['prenom'].' '.$_SESSION['nom']); ?></span>
    <?php endif; ?>
    <a href="logout.php" style="float:right;">Déconnexion</a>
</nav>
<script src="toast.js"></script>
<style>
.global-menu {
    text-align: center;
    margin-bottom: 18px;
    font-size: 1.08em;
}
.global-menu a {
    color: #3b6eea;
    text-decoration: none;
    margin: 0 10px;
    font-weight: 500;
    transition: color 0.2s;
}
.global-menu a:hover {
    color: #1a3e8a;
}
.user {
    margin: 0 10px;
    font-weight: 400;
    color: #333;
}
</style>
