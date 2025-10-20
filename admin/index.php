<?php
/**
 * Admin - Point d'entrée
 * Redirige vers le dashboard ou login
 */
session_start();
if(isset($_SESSION['id'])){
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit();
