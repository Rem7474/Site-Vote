<?php
/**
 * Fonctions de sécurité pour le système de vote
 */

/**
 * Génère un token CSRF et le stocke en session
 * @return string Le token généré
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie la validité du token CSRF
 * @param string $token Le token à vérifier
 * @return bool True si le token est valide, false sinon
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Génère un champ input hidden pour le token CSRF
 * @return string Le HTML du champ input
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Rate limiting basique - limite le nombre de tentatives par IP
 * @param string $action L'action à limiter (ex: 'login', 'register', 'vote')
 * @param int $max_attempts Nombre maximum de tentatives
 * @param int $time_window Fenêtre de temps en secondes
 * @return bool True si l'action est autorisée, false si la limite est atteinte
 */
function checkRateLimit($action, $max_attempts = 5, $time_window = 300) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = 'rate_limit_' . $action . '_' . $ip;
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'first_attempt' => time()];
    }
    
    $data = $_SESSION[$key];
    
    // Réinitialiser si la fenêtre de temps est dépassée
    if (time() - $data['first_attempt'] > $time_window) {
        $_SESSION[$key] = ['count' => 1, 'first_attempt' => time()];
        return true;
    }
    
    // Vérifier si la limite est atteinte
    if ($data['count'] >= $max_attempts) {
        return false;
    }
    
    // Incrémenter le compteur
    $_SESSION[$key]['count']++;
    return true;
}

/**
 * Nettoie et valide une entrée utilisateur
 * @param string $data Les données à nettoyer
 * @return string Les données nettoyées
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

/**
 * Valide un format d'email
 * @param string $email L'email à valider
 * @return bool True si l'email est valide
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Log une action de sécurité
 * @param string $action L'action effectuée
 * @param string $details Détails supplémentaires
 * @param string $level Niveau de log (INFO, WARNING, ERROR)
 */
function logSecurityEvent($action, $details = '', $level = 'INFO') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $timestamp = date('Y-m-d H:i:s');
    
    $log_entry = sprintf(
        "[%s] [%s] IP: %s | Action: %s | Details: %s | User-Agent: %s\n",
        $timestamp,
        $level,
        $ip,
        $action,
        $details,
        $user_agent
    );
    
    // Créer le dossier logs si il n'existe pas
    if (!file_exists('./logs')) {
        mkdir('./logs', 0755, true);
    }
    
    error_log($log_entry, 3, './logs/security_' . date('Y-m-d') . '.log');
}

/**
 * Vérifie si l'utilisateur est connecté en tant qu'organisateur
 * @return bool True si connecté, false sinon
 */
function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['id']) && !empty($_SESSION['id']);
}

/**
 * Redirige vers la page de login si non connecté
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Valide le format du login universitaire
 * @param string $login Le login à valider
 * @return bool True si le format est valide
 */
function validateUniversityLogin($login) {
    // Format: prenom.nom (lettres minuscules uniquement)
    return preg_match('/^[a-z]+\.[a-z]+$/', $login) === 1;
}

/**
 * Génère un hash sécurisé pour un vote
 * @param string $login Le login de l'utilisateur
 * @return string Le hash généré
 */
function generateSecureHash($login) {
    $salt = random_bytes(16);
    $timestamp = time();
    $data = $login . $timestamp . bin2hex($salt);
    return hash('sha256', $data);
}

/**
 * Vérifie si un fichier uploadé est sécurisé
 * @param array $file Le fichier ($_FILES['field'])
 * @param array $allowed_extensions Extensions autorisées
 * @param int $max_size Taille maximale en octets
 * @return array ['valid' => bool, 'error' => string|null]
 */
function validateFileUpload($file, $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'], $max_size = 5242880) {
    // Vérifier si le fichier a été uploadé
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['valid' => false, 'error' => 'Erreur lors de l\'upload du fichier'];
    }
    
    // Vérifier la taille
    if ($file['size'] > $max_size) {
        return ['valid' => false, 'error' => 'Le fichier est trop volumineux (max 5MB)'];
    }
    
    // Vérifier l'extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed_extensions)) {
        return ['valid' => false, 'error' => 'Extension de fichier non autorisée'];
    }
    
    // Vérifier le type MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed_mimes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/bmp',
        'image/webp'
    ];
    
    if (!in_array($mime_type, $allowed_mimes)) {
        return ['valid' => false, 'error' => 'Type de fichier non autorisé'];
    }
    
    return ['valid' => true, 'error' => null];
}
?>
