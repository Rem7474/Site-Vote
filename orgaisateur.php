<?php
session_start();
include 'fonctionsPHP.php';
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}