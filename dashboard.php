<?php
session_start();
if(!isset($_SESSION['id'])){
    header('location:login.php');
    exit();
}
include 'fonctionsPHP.php';
<<<<<<< HEAD

//récupération des evenements de l'utilisateur
$events = getEventsOrga($_SESSION['id'], $conn);
=======
// Gestion du logo personnalisé
$idOrga = $_SESSION['id'];
$logoPath = 'bgsharklo.jpg';
foreach(['jpg','png','webp'] as $ext) {
    $customLogo = './images/logo_' . $idOrga . '.' . $ext;
    if (file_exists($customLogo)) {
        $logoPath = $customLogo;
        break;
    }
}
// Gestion du changement de logo
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['logo']['tmp_name'];
    $fileType = mime_content_type($fileTmp);
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (in_array($fileType, $allowed)) {
        $ext = $fileType === 'image/png' ? 'png' : ($fileType === 'image/webp' ? 'webp' : 'jpg');
        $dest = './images/logo_' . $idOrga . '.' . $ext;
        move_uploaded_file($fileTmp, $dest);
        foreach(['jpg','png','webp'] as $e) {
            $old = './images/logo_' . $idOrga . '.' . $e;
            if ($old !== $dest && file_exists($old)) unlink($old);
        }
        echo "<script>window.toastMessage='Logo mis à jour avec succès';window.toastType='success';</script>";
        header('Refresh:1;url=dashboard.php');
        exit();
    } else {
        $msg = 'Format de logo non supporté.';
        echo "<script>window.toastMessage='Format de logo non supporté';window.toastType='error';</script>";
    }
}

// but de la page : afficher les evenements qu'il a créer, et affiche un formulaire pour créer un nouvel evenement
// quand il clique sur un evenement, il est redirigé vers la page event.php, ou il peut voir les détails de l'evenement
// il peut voir aussi les votes pour chaque evenement depuis cette page

//récupération des evenements de l'utilisateur
$events = getEventsOrga($_SESSION['id'], $conn);
//print de l'array $events;
//print_r($events);
//affiche les erreurs php
ini_set('display_errors', 1);

>>>>>>> origin/beta

//création d'un nouvel evenement
if(isset($_POST['nom']) && isset($_POST['universite'])){
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        logSecurityEvent('CSRF_ATTEMPT', 'Create event', 'WARNING');
        header('location:erreur.html');
        exit();
    }
    
    $nom = sanitizeInput($_POST['nom']);
    $universite = sanitizeInput($_POST['universite']);
    
    addEvent($nom, $universite, $_SESSION['id'], $conn);
    logSecurityEvent('EVENT_CREATED', "Event: $nom", 'INFO');
    header('location:dashboard.php');
    exit();
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Gestion des Votes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
<<<<<<< HEAD
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header-bar {
            background: white;
            border-radius: 12px;
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header-bar h1 {
            margin: 0;
            color: #333;
            font-size: 28px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-name {
            color: #666;
            font-weight: 500;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-logout {
            background: #ef4444;
            color: white;
        }
        
        .btn-logout:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #10b981;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #059669;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        
        .section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .section h2 {
            margin-top: 0;
            color: #333;
            font-size: 24px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        
        .event-grid {
            display: grid;
            gap: 20px;
        }
        
        .event-card {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 25px;
            transition: all 0.3s;
            background: #fafafa;
        }
        
        .event-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }
        
        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .event-title {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        
        .event-university {
            color: #666;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .event-stats {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        
        .event-stat {
            display: flex;
            flex-direction: column;
        }
        
        .event-stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .event-stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        
        .lists-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .list-item {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            background: white;
            text-align: center;
        }
        
        .list-item img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid #667eea;
        }
        
        .list-item-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .list-item-votes {
            color: #10b981;
            font-size: 18px;
            font-weight: bold;
        }
        
        .event-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .share-link {
            background: #f3f4f6;
            padding: 12px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 14px;
            color: #666;
            margin-top: 10px;
            word-break: break-all;
        }
        
        .form-grid {
            display: grid;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea {
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        @media (max-width: 768px) {
            .header-bar {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .lists-preview {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="header-bar">
            <div>
                <h1>📊 Tableau de Bord</h1>
                <div class="user-name">👤 <?php echo htmlspecialchars($_SESSION['prenom'].' '.$_SESSION['nom']); ?></div>
            </div>
            <form action="logout.php" method="post" style="margin: 0;">
                <button type="submit" class="btn btn-logout">🚪 Déconnexion</button>
            </form>
        </div>

        <!-- Statistiques globales -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Événements</h3>
                <p class="stat-value"><?php echo count($events); ?></p>
            </div>
            <div class="stat-card" style="border-left-color: #10b981;">
                <h3>Total Votes</h3>
                <p class="stat-value">
                    <?php 
                    $totalVotes = 0;
                    foreach($events as $event) {
                        $totalVotes += getNbVotes($event['id'], $conn);
                    }
                    echo $totalVotes;
                    ?>
                </p>
            </div>
            <div class="stat-card" style="border-left-color: #f59e0b;">
                <h3>En Attente</h3>
                <p class="stat-value">
                    <?php 
                    $totalPending = 0;
                    foreach($events as $event) {
                        $totalPending += getNbParticipants($event['id'], $conn);
                    }
                    echo $totalPending;
                    ?>
                </p>
            </div>
            <div class="stat-card" style="border-left-color: #ef4444;">
                <h3>Total Listes</h3>
                <p class="stat-value">
                    <?php 
                    $totalListes = 0;
                    foreach($events as $event) {
                        $totalListes += count(getListes($event['id'], $conn));
                    }
                    echo $totalListes;
                    ?>
                </p>
            </div>
        </div>

        <!-- Liste des événements -->
        <div class="section">
            <h2>🎯 Mes Événements</h2>
            
            <?php if (!empty($events)): ?>
                <div class="event-grid">
                    <?php foreach ($events as $event): 
                        $listes = getListes($event['id'], $conn);
                        $nbVotes = getNbVotes($event['id'], $conn);
                        $nbPending = getNbParticipants($event['id'], $conn);
                    ?>
                        <div class="event-card">
                            <div class="event-header">
                                <div>
                                    <h3 class="event-title"><?php echo htmlspecialchars($event['nom']); ?></h3>
                                    <p class="event-university">🎓 <?php echo htmlspecialchars($event['univ']); ?></p>
                                </div>
                            </div>

                            <div class="event-stats">
                                <div class="event-stat">
                                    <span class="event-stat-label">Votes reçus</span>
                                    <span class="event-stat-value"><?php echo $nbVotes; ?></span>
                                </div>
                                <div class="event-stat">
                                    <span class="event-stat-label">En attente</span>
                                    <span class="event-stat-value" style="color: #f59e0b;"><?php echo $nbPending; ?></span>
                                </div>
                                <div class="event-stat">
                                    <span class="event-stat-label">Listes</span>
                                    <span class="event-stat-value" style="color: #10b981;"><?php echo count($listes); ?></span>
                                </div>
                            </div>

                            <?php if (!empty($listes)): ?>
                                <div class="lists-preview">
                                    <?php foreach ($listes as $liste): 
                                        $votesListe = getVotes($liste['id'], $conn);
                                    ?>
                                        <div class="list-item">
                                            <img src="./images/<?php echo htmlspecialchars($liste['photo']); ?>" 
                                                 alt="<?php echo htmlspecialchars($liste['nom']); ?>"
                                                 onerror="this.src='bgsharklo.jpg'">
                                            <div class="list-item-name"><?php echo htmlspecialchars($liste['nom']); ?></div>
                                            <div class="list-item-votes">🗳️ <?php echo $votesListe; ?> votes</div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p style="color: #666; font-style: italic;">Aucune liste créée pour cet événement.</p>
                            <?php endif; ?>

                            <div class="share-link">
                                📤 Lien d'inscription : https://vote.remcorp.fr/index.php?id=<?php echo $event['id']; ?>
                            </div>

                            <div class="event-actions">
                                <a href="event.php?id=<?php echo $event['id']; ?>" class="btn btn-primary">
                                    ⚙️ Gérer l'événement
                                </a>
                                <button onclick="copyLink('https://vote.remcorp.fr/index.php?id=<?php echo $event['id']; ?>')" 
                                        class="btn btn-secondary">
                                    📋 Copier le lien
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h3>Aucun événement créé</h3>
                    <p>Créez votre premier événement de vote ci-dessous pour commencer.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Formulaire de création d'événement -->
        <div class="section">
            <h2>➕ Créer un Nouvel Événement</h2>
            <form method="post" action="dashboard.php">
                <?php echo csrfField(); ?>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nom">📝 Nom de l'événement</label>
                        <input type="text" 
                               id="nom" 
                               name="nom" 
                               placeholder="Ex: Élection BDE 2025" 
                               required>
                    </div>
                    <div class="form-group">
                        <label for="universite">🏫 Domaine de l'université</label>
                        <input type="text" 
                               id="universite" 
                               name="universite" 
                               placeholder="Ex: univ-smb.fr" 
                               required>
                        <small style="color: #666; margin-top: 5px;">
                            ℹ️ Les emails seront au format: prenom.nom@etu.votre-domaine
                        </small>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top: 20px;">
                    ✨ Créer l'événement
                </button>
            </form>
        </div>

        <!-- Lien vers inscription organisateur -->
        <div style="text-align: center; margin-top: 20px;">
            <a href="register.php" style="color: white; text-decoration: underline;">
                Créer un compte organisateur
            </a>
        </div>
    </div>

    <script>
        function copyLink(link) {
            navigator.clipboard.writeText(link).then(function() {
                alert('✅ Lien copié dans le presse-papiers !');
            }, function() {
                alert('❌ Erreur lors de la copie du lien');
            });
        }
    </script>
=======
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php printFaviconTag(); addDarkModeScript(); ?>
</head>
<body>
<?php include 'inc_header.php'; ?>
<?php include 'inc_admin_menu.php'; ?>
<div class="container card">
    <h1>Dashboard</h1>
    <!-- Statistiques avancées -->
    <div class="card" style="margin-bottom:20px;">
        <h2>Statistiques globales</h2>
        <div style="display:flex;flex-wrap:wrap;gap:30px;align-items:center;">
            <div>
                <strong>Taux de participation :</strong> <span id="taux-participation">Calcul...</span><br>
                <strong>Nombre total de votes :</strong> <span id="total-votes">Calcul...</span>
            </div>
            <canvas id="evolutionVotes" width="320" height="120"></canvas>
        </div>
    </div>
    <!-- Mes événements -->
    <div class="card" style="margin-bottom:30px;">
        <h2>Mes événements</h2>
        <?php if (empty($events)) : ?>
            <p>Vous n'avez pas encore créé d'événement.</p>
        <?php else : ?>
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr><th>Nom</th><th>Université</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach($events as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['nom']); ?></td>
                        <td><?php echo htmlspecialchars($event['Univ'] ?? getUniversity($event['id'], $conn) ?? ''); ?></td>
                        <td>
                            <a href="event.php?id=<?php echo $event['id']; ?>" class="btn">Détails</a>
                            <a href="event.php?id=<?php echo $event['id']; ?>&edit=1" class="btn">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
        <h3>Créer un nouvel événement</h3>
        <form method="post" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
            <input type="text" name="nom" placeholder="Nom de l'événement" required class="input" style="flex:1;min-width:180px;">
            <input type="text" name="universite" placeholder="Université" required class="input" style="flex:1;min-width:180px;">
            <input type="submit" value="Créer" class="btn">
        </form>
    </div>
    <!-- Onglets de personnalisation en bas de page -->
    <div class="card card-personnalisation" style="margin:40px auto 0 auto;max-width:520px;box-shadow:0 4px 24px rgba(60,90,200,0.13);border-radius:18px;border:1.5px solid #dbeafe;background:linear-gradient(135deg,#f4f7ff 0%,#e0e7ff 100%);padding:32px 28px 28px 28px;">
        <h2 style="margin-bottom:18px;text-align:center;color:#3b6eea;font-size:1.25em;letter-spacing:0.5px;">Personnalisation</h2>
        <div style="display:flex;flex-direction:column;gap:30px;">
            <!-- Logo -->
            <div style="background:#f7faff;border-radius:12px;padding:18px 14px 14px 14px;box-shadow:0 2px 8px rgba(60,90,200,0.07);">
                <h3 style="margin-bottom:10px;color:#2d3a4b;font-size:1.08em;">Logo personnalisé</h3>
                <form method="post" enctype="multipart/form-data" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:center;">
                    <label for="logo" style="font-weight:bold;">Logo&nbsp;:</label>
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" style="width:auto;">
                    <input type="submit" value="Mettre à jour" class="btn" style="width:auto;">
                    <?php
                    $idOrga = $_SESSION['id'];
                    $logoPath = 'bgsharklo.jpg';
                    foreach(['jpg','png','webp'] as $ext) {
                        $customLogo = './images/logo_' . $idOrga . '.' . $ext;
                        if (file_exists($customLogo)) {
                            $logoPath = $customLogo;
                            break;
                        }
                    }
                    if ($logoPath) {
                        echo '<img src="'.$logoPath.'" alt="Logo actuel" style="max-width:60px;max-height:60px;vertical-align:middle;border-radius:50%;box-shadow:0 1px 4px rgba(0,0,0,0.07);">';
                    }
                    ?>
                </form>
                <?php
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $fileTmp = $_FILES['logo']['tmp_name'];
                    $fileType = mime_content_type($fileTmp);
                    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                    if (in_array($fileType, $allowed)) {
                        $ext = $fileType === 'image/png' ? 'png' : ($fileType === 'image/webp' ? 'webp' : 'jpg');
                        $dest = './images/logo_' . $idOrga . '.' . $ext;
                        move_uploaded_file($fileTmp, $dest);
                        foreach(['jpg','png','webp'] as $e) {
                            $old = './images/logo_' . $idOrga . '.' . $e;
                            if ($old !== $dest && file_exists($old)) unlink($old);
                        }
                        echo '<script>window.toastMessage="Logo mis à jour avec succès";window.toastType="success";setTimeout(()=>location.reload(),800);</script>';
                    } else {
                        echo '<p class="erreur">Format de logo non supporté (jpg, png, webp uniquement).</p>';
                    }
                }
                ?>
                <small style="color:#3b6eea;">Le logo personnalisé s’affichera sur toutes vos pages. Format accepté : .jpg, .png, .webp.</small>
            </div>
            <!-- Favicon -->
            <div style="background:#f7faff;border-radius:12px;padding:18px 14px 14px 14px;box-shadow:0 2px 8px rgba(60,90,200,0.07);">
                <h3 style="margin-bottom:10px;color:#2d3a4b;font-size:1.08em;">Favicon personnalisé</h3>
                <form method="post" enctype="multipart/form-data" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:center;">
                    <label for="favicon" style="font-weight:bold;">Favicon&nbsp;:</label>
                    <input type="file" name="favicon" accept="image/x-icon,image/png" style="width:auto;">
                    <input type="submit" name="upload_favicon" value="Mettre à jour" class="btn" style="width:auto;">
                    <?php
                    $faviconPath = null;
                    foreach(['ico','png'] as $ext) {
                        $customFavicon = './images/favicon_' . $idOrga . '.' . $ext;
                        if (file_exists($customFavicon)) {
                            $faviconPath = $customFavicon;
                            break;
                        }
                    }
                    if ($faviconPath) {
                        echo '<img src="'.$faviconPath.'" alt="Favicon actuel" style="max-width:32px;max-height:32px;vertical-align:middle;">';
                    }
                    ?>
                </form>
                <?php
                if (isset($_POST['upload_favicon']) && isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
                    $fileTmp = $_FILES['favicon']['tmp_name'];
                    $fileType = mime_content_type($fileTmp);
                    $allowed = ['image/x-icon', 'image/vnd.microsoft.icon', 'image/png'];
                    if (in_array($fileType, $allowed)) {
                        $ext = $fileType === 'image/png' ? 'png' : 'ico';
                        $dest = './images/favicon_' . $idOrga . '.' . $ext;
                        move_uploaded_file($fileTmp, $dest);
                        foreach(['ico','png'] as $e) {
                            $old = './images/favicon_' . $idOrga . '.' . $e;
                            if ($old !== $dest && file_exists($old)) unlink($old);
                        }
                        echo '<script>window.toastMessage="Favicon mis à jour avec succès";window.toastType="success";setTimeout(()=>location.reload(),800);</script>';
                    } else {
                        echo '<p class="erreur">Format de favicon non supporté (ico, png uniquement).</p>';
                    }
                }
                ?>
                <small style="color:#3b6eea;">Le favicon personnalisé s’affichera sur toutes vos pages. Format accepté : .ico ou .png (32x32 recommandé).</small>
            </div>
        </div>
    </div>
</div>
>>>>>>> origin/beta
</body>
</html>