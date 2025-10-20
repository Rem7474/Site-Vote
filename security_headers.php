<?php
/**
 * Configuration des headers de sécurité HTTP
 * Inclure ce fichier au début de chaque page publique
 */

// Forcer HTTPS en production (décommenter en production)
// if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
//     header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
//     exit();
// }

// Content Security Policy - Protège contre XSS
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self'; frame-ancestors 'none';");

// Empêche le navigateur de deviner le type MIME
header("X-Content-Type-Options: nosniff");

// Empêche l'affichage dans une iframe (protection contre clickjacking)
header("X-Frame-Options: DENY");

// Active la protection XSS du navigateur
header("X-XSS-Protection: 1; mode=block");

// Référer Policy - Contrôle les informations envoyées dans le header Referer
header("Referrer-Policy: strict-origin-when-cross-origin");

// Permissions Policy - Contrôle les fonctionnalités du navigateur
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

// Strict Transport Security - Force HTTPS (activer uniquement en production avec HTTPS)
// header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");

// Cache control pour les pages sensibles
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Pragma: no-cache");
?>
