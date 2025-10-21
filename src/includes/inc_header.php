<?php
// Inclusion à placer en haut de chaque page principale
if (session_status() === PHP_SESSION_NONE) session_start();
include_once __DIR__ . '/fonctionsPHP.php';

// Détermination du logo à afficher
$orgaId = null;
if (isset($_SESSION['id'])) {
    $orgaId = $_SESSION['id'];
} elseif (isset($event) && isset($event['reforga'])) {
    $orgaId = $event['reforga'];
}

$logoHref = assetPath('assets/images/logo-default.jpg');
if ($orgaId) {
    foreach(['jpg','png','webp'] as $ext) {
        $fs = __DIR__ . '/../../public/assets/images/logo_' . $orgaId . '.' . $ext;
        if (file_exists($fs)) {
            $logoHref = assetPath('assets/images/logo_' . $orgaId . '.' . $ext);
            break;
        }
    }
}

// Affichage du bouton retour uniquement si on n'est PAS sur le dashboard
$current = basename($_SERVER['PHP_SELF']);
$showRetour = $current !== 'dashboard.php';

// Détection du contexte (admin ou public) pour les liens
$isPublicPage = strpos($_SERVER['PHP_SELF'], '/public/') !== false;
$dashboardLink = $isPublicPage ? '../admin/dashboard.php' : 'dashboard.php';
$logoutLink = $isPublicPage ? '../admin/logout.php' : 'logout.php';
?>
<?php printFaviconTag(); addDarkModeScript(); ?>
<header class="modern-header">
    <div class="header-container">
        <div class="header-left">
            <a href="<?php echo $dashboardLink; ?>" class="logo-link">
                <img src="<?php echo htmlspecialchars($logoHref, ENT_QUOTES, 'UTF-8'); ?>" alt="Logo" class="header-logo">
            </a>
            <div class="header-info">
                <?php if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])): ?>
                    <span class="welcome-text">Bienvenue, <strong><?php echo htmlspecialchars($_SESSION['prenom'].' '.$_SESSION['nom']); ?></strong></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="header-right">
            <button class="theme-toggle" onclick="toggleDarkMode()" title="Changer le thème">
                🌓
            </button>
            <?php if ($showRetour): ?>
                <a href="<?php echo $dashboardLink; ?>" class="btn-back">← Dashboard</a>
            <?php endif; ?>
            <form action="<?php echo $logoutLink; ?>" method="post" style="display:inline;margin:0;">
                <button type="submit" class="btn-logout">Déconnexion</button>
            </form>
        </div>
    </div>
</header>

<style>
.modern-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 15px 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    margin-bottom: 30px;
}

.header-container {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 20px;
    flex: 1;
}

.logo-link {
    display: block;
    transition: transform 0.3s ease;
}

.logo-link:hover {
    transform: scale(1.05);
}

.header-logo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 3px solid rgba(255,255,255,0.3);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    object-fit: cover;
    background: white;
}

.header-info {
    flex: 1;
}

.welcome-text {
    color: white;
    font-size: 1.1em;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.welcome-text strong {
    font-weight: 600;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.theme-toggle {
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    font-size: 1.3em;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.theme-toggle:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(180deg);
}

.btn-back {
    background: rgba(255,255,255,0.95);
    color: #667eea;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    white-space: nowrap;
}

.btn-back:hover {
    background: white;
    border-color: rgba(255,255,255,0.5);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.btn-logout {
    background: rgba(255,255,255,0.15);
    color: white;
    padding: 10px 20px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.btn-logout:hover {
    background: rgba(255,255,255,0.25);
    border-color: rgba(255,255,255,0.5);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Responsive */
@media (max-width: 768px) {
    .modern-header {
        padding: 12px 15px;
    }
    
    .header-container {
        flex-direction: column;
        gap: 15px;
    }
    
    .header-left {
        width: 100%;
        justify-content: center;
        text-align: center;
    }
    
    .header-right {
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .header-logo {
        width: 50px;
        height: 50px;
    }
    
    .welcome-text {
        font-size: 1em;
    }
}

@media (max-width: 480px) {
    .header-logo {
        width: 45px;
        height: 45px;
    }
    
    .welcome-text {
        font-size: 0.95em;
    }
    
    .btn-back, .btn-logout {
        padding: 8px 15px;
        font-size: 0.9em;
    }
    
    .theme-toggle {
        width: 40px;
        height: 40px;
        font-size: 1.1em;
    }
}
</style>