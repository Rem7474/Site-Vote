<?php
session_start();
if(!isset($_SESSION['id'])){
    header('location:login.php');
    exit();
}
include 'fonctionsPHP.php';

//récupération des evenements de l'utilisateur
$events = getEventsOrga($_SESSION['id'], $conn);

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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Gestion des Votes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
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
</body>
</html>