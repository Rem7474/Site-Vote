<?php
// Connexion à la base de données
include __DIR__ . '/../config/database.php';
include __DIR__ . '/fonctionsBDD.php';
include __DIR__ . '/fonctionsSecurite.php';

// Configuration
$param = parse_ini_file(__DIR__ . '/../../private/parametres.ini');
$conn = ConnexionBDD(__DIR__ . '/../../private/parametres.ini');
$DEBUG = isset($param['debug']) && ($param['debug'] === true || $param['debug'] === 'true');

if ($DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Chargement de Composer autoload
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    if ($DEBUG) {
        echo '<b>Erreur :</b> vendor/autoload.php est manquant. Exécutez composer install.';
    }
    exit();
}
require $autoloadPath;
>>>>>>> origin/beta
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function InscriptionVote($login, $IDevent){
    global $conn;
    
    // Rate limiting - max 5 inscriptions par IP toutes les 5 minutes
    if (!checkRateLimit('inscription', 5, 300)) {
        logSecurityEvent('RATE_LIMIT_EXCEEDED', "Inscription - Event: $IDevent", 'WARNING');
        header('Location: erreur.html');
        exit();
    }
    
    //vérification de l'existence de l'événement dans la base de données
    if(!getEvent($IDevent, $conn)){
        logSecurityEvent('INVALID_EVENT', "Event ID: $IDevent", 'WARNING');
        header('Location: erreur.html');
        exit();
    }
    
    //mise en minuscule et nettoyage du login
    $login = strtolower(sanitizeInput($login));
    
    //vérification du login : prénom.nom
    if (!validateUniversityLogin($login)){
        logSecurityEvent('INVALID_LOGIN_FORMAT', "Login: $login - Event: $IDevent", 'WARNING');
        header('Location: erreur.html');
        exit();
    }
    
    //création du mail a partir de l'université définie dans la table event
    //récupération de l'université
    $universite = getUniversity($IDevent, $conn);
    //création du mail
    $email = $login."@etu.".$universite;
    
    //hashage sécurisé
    $hash = generateSecureHash($login);
    
    //envoi du mail de confirmation si la personne n'est pas déjà inscrite à l'événement
    $user=getUser($login, $IDevent, $conn);
    if($user){
        logSecurityEvent('DUPLICATE_REGISTRATION', "Login: $login - Event: $IDevent", 'INFO');
        header('Location: erreur.html');
        exit();
    }
    else{
        //récupération du nom de l'événement
        $nomEvent = getNomEvent($IDevent, $conn);
        $mailSent = SendMail($email, "[Vote ".$nomEvent."] Confirmation d'inscription", "https://vote.remcorp.fr/index.php?hash=".$hash, $nomEvent);
        
        if (!$mailSent) {
            logSecurityEvent('EMAIL_FAILED', "Email: $email - Event: $IDevent", 'ERROR');
            header('Location: erreur.html');
            exit();
        }
        
        //enregistrement du hash dans la base de données
        $ajout=addHash($hash, $IDevent, $conn);
        $ajout2=addUser($login, $email, $IDevent, $conn);
        
        //redirection vers une page de confirmation si l'ajout a réussi
        if($ajout){
            logSecurityEvent('REGISTRATION_SUCCESS', "Login: $login - Event: $IDevent", 'INFO');
            header('Location: confirmationInscription.php?mail='.$email);
            exit();
        }
        else{
            logSecurityEvent('REGISTRATION_FAILED', "Login: $login - Event: $IDevent", 'ERROR');
            header('Location: erreur.html');
            exit();
        }
    }
}

function EnregistrerVote($vote, $hash){
    global $conn;
    
    // Rate limiting - max 3 votes par IP toutes les 10 minutes
    if (!checkRateLimit('vote', 3, 600)) {
        logSecurityEvent('RATE_LIMIT_EXCEEDED', "Vote attempt with hash", 'WARNING');
        header('Location: erreur.html');
        exit();
    }
    
    // Nettoyage et validation
    $vote = sanitizeInput($vote);
    $hash = sanitizeInput($hash);
    
    //vérification de l'existence du hash dans la base de données
    //récupération de l'event a partir du hash, puis des listes qui se sont inscrites a cet event
    $event=getHash($hash, $conn);
    if (!isset($event) || $event === false){
        logSecurityEvent('INVALID_HASH', "Hash not found", 'WARNING');
        header('Location: erreur.html');
        exit();
    }
    
    $listes=getListes($event, $conn);
    //récupération des id des listes, pour les mettre dans un tableau
    $idListes=array();
    foreach($listes as $liste){
        array_push($idListes, $liste['id']);
    }
    
    //vérification si vote est dans les id des listes
    if(in_array($vote, $idListes)){
        //création d'un nouveau hash aléatoire sécurisé
        $participation = hash('sha256', random_bytes(32));
        
        //enregistrement du vote dans la base de données
        $result=addVote($vote, $participation, $event, $conn);
        if ($result){
            //suppression du hash dans la base de données
            $result2=deleteHash($hash, $conn);
            if(!$result2){
                logSecurityEvent('HASH_DELETE_FAILED', "Event: $event", 'ERROR');
                header('Location: erreur.html');
                exit();
            }
            else{
                logSecurityEvent('VOTE_SUCCESS', "Event: $event - Liste: $vote", 'INFO');
                header('Location: confirmationVote.php?hash='.$participation);
                exit();
            }
        }
        else{
            logSecurityEvent('VOTE_FAILED', "Event: $event - Liste: $vote", 'ERROR');
            header('Location: erreur.html');
            exit();
        }
    }
    else{
        logSecurityEvent('INVALID_VOTE', "Invalid list ID: $vote for event: $event", 'WARNING');
        header('Location: erreur.html');
        exit();
    }
}
function resultats($equipe){
    global $conn;
    $result=getVotes($equipe, $conn);
    return $result;
}
function SendMail($to, $subject, $lien, $event){
    $param = parse_ini_file('./private/parametres.ini');
    $smtp_host = $param['smtp_host'] ?? '';
    $smtp_port = $param['smtp_port'] ?? 587;
    $smtp_user = $param['smtp_user'] ?? '';
    $smtp_pass = $param['smtp_pass'] ?? '';
    $message = "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='utf-8'>
        <title>Inscription pour les votes</title>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' type='text/css' href='https://vote.remcorp.fr/styles.css'>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <img src='https://vote.remcorp.fr/bgsharklo.jpg' alt='Logo du site'>
            </div>
            <h1>Inscription pour les votes de : ".$eventEscaped."</h1>
            <p class='reussite'>Cliquez sur ce lien pour effectuer votre vote : <a href='".$lienEscaped."'>Accéder au vote</a></p>
            <p style='color: #666; font-size: 12px; margin-top: 20px;'>
                Ce lien est à usage unique et personnel. Ne le partagez pas.
            </p>
        </div>
    </body>
    </html>";
    
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_user;
        $mail->Password = $smtp_pass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtp_port;
        //Recipients
        $mail->setFrom('vote@remcorp.fr', 'Système de Vote');
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = "Cliquez sur ce lien pour effectuer votre vote : ".$lien;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        //redirection vers une page d'erreur
        global $DEBUG;
        if ($DEBUG) {
            echo '<b>Erreur d\'envoi de mail :</b> ' . $e->getMessage();
        }
        header('Location: erreur.html');
    }   
}
function deleteaccent($string){
    $search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
	$replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');
	$string = str_replace($search, $replace, $string);
    return $string;
}
function getEquipeVote($hash){
    global $conn;
    $result=getEquipe($hash, $conn);
    if ($result){
        return $result;
    }
    else{
        return "";
    }
}
// Ajout du mode sombre
function addDarkModeScript() {
    echo '<script>
    if (localStorage.getItem("theme") === "dark") {
        document.documentElement.classList.add("dark-mode");
    } else {
        document.documentElement.classList.remove("dark-mode");
    }
    window.toggleDarkMode = function() {
        document.documentElement.classList.toggle("dark-mode");
        localStorage.setItem("theme", document.documentElement.classList.contains("dark-mode") ? "dark" : "light");
    }
    </script>';
}
// Ajout d'une fonction de double confirmation JS
function doubleConfirm($message = 'Êtes-vous sûr de vouloir effectuer cette action ?') {
    echo "<script>
    function doubleConfirmAction(e) {
        if (!confirm('$message')) { e.preventDefault(); return false; }
        if (!confirm('Merci de confirmer à nouveau.')) { e.preventDefault(); return false; }
    }
    document.querySelectorAll('.double-confirm').forEach(function(btn) {
        btn.addEventListener('click', doubleConfirmAction);
    });
    </script>";
}
function printFaviconTag() {
    $idOrga = null;
    if (isset($_SESSION['id'])) {
        $idOrga = $_SESSION['id'];
    } elseif (isset($event) && isset($event['reforga'])) {
        $idOrga = $event['reforga'];
    }
    if ($idOrga) {
        foreach(['ico','png'] as $ext) {
            $customFavicon = './images/favicon_' . $idOrga . '.' . $ext;
            if (file_exists($customFavicon)) {
                echo '<link rel="icon" type="image/'.$ext.'" href="'.$customFavicon.'">';
                return;
            }
        }
    }
    // Fallback
    echo '<link rel="icon" type="image/png" href="favicon.png">';
}
// Ajout d'une fonction pour récupérer une liste par son id
function getListeById($id, $conn) {
    $sql = "SELECT * FROM listes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch();
}
?>