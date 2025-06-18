<?php
// Menu de navigation admin à inclure dans le dashboard et les pages admin
?>
<nav class="admin-menu">
    <a href="dashboard.php">Dashboard</a> |
    <a href="contact.php">FAQ/Contact</a> |
    <a href="logout.php">Déconnexion</a>
</nav>
<style>
.admin-menu {
    text-align: center;
    margin-bottom: 18px;
    font-size: 1.08em;
}
.admin-menu a {
    color: #3b6eea;
    text-decoration: none;
    margin: 0 10px;
    font-weight: 500;
    transition: color 0.2s;
}
.admin-menu a:hover {
    color: #1a3e8a;
}
</style>
