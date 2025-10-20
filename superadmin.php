<?php
session_start();
include 'fonctionsPHP.php';

$param = parse_ini_file('./private/parametres.ini');
$superadmin_user = $param['superadmin_user'] ?? '';
$superadmin_pass = $param['superadmin_pass'] ?? '';

// Gestion de la déconnexion
if (isset($_GET['logout'])) {
    unset($_SESSION['superadmin']);
    header('Location: superadmin.php');
    exit();
}

// Authentification super admin
if (!isset($_SESSION['superadmin'])) {
    if (isset($_POST['login']) && isset($_POST['password'])) {
        // Rate limiting
        if (!checkRateLimit('superadmin_login', 3, 900)) {
            logSecurityEvent('RATE_LIMIT_EXCEEDED', 'Superadmin login attempts', 'WARNING');
            $error = 'Trop de tentatives. Réessayez dans 15 minutes.';
        } elseif ($_POST['login'] === $superadmin_user && $_POST['password'] === $superadmin_pass) {
            $_SESSION['superadmin'] = true;
            logSecurityEvent('SUPERADMIN_LOGIN', 'Superadmin logged in', 'INFO');
            header('Location: superadmin.php');
            exit();
        } else {
            logSecurityEvent('SUPERADMIN_LOGIN_FAILED', 'Invalid credentials', 'WARNING');
            $error = 'Identifiants incorrects';
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>🔐 Connexion Super Admin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="styles.css">
        <?php printFaviconTag(); addDarkModeScript(); ?>
    </head>
    <body>
        <div class="container card">
            <div class="header">
                <img src="bgsharklo.jpg" alt="Logo" style="max-width: 100px;">
            </div>
            <h1>🔐 Super Admin</h1>
            <?php if (isset($error)): ?>
                <div class="card erreur" style="margin: 20px 0;">
                    <p style="margin: 0;">⚠️ <?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>
            <form method="post">
                <label for="login">👤 Identifiant</label>
                <input type="text" id="login" name="login" placeholder="superadmin" required autofocus>
                <label for="password">🔒 Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <input type="submit" value="🚀 Accéder au panneau" class="btn">
            </form>
            <div class="footer" style="margin-top: 30px;">
                <p><a href="login.php">← Retour à la connexion organisateur</a></p>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Actions administratives
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = intval($_POST['id'] ?? 0);
    
    switch ($action) {
        case 'delete_organizer':
            // Supprimer l'organisateur et tous ses événements
            $stmt = $conn->prepare('DELETE FROM organisateurs WHERE id = ?');
            $stmt->execute([$id]);
            logSecurityEvent('SUPERADMIN_DELETE_ORGANIZER', "Organizer ID: $id", 'INFO');
            $success = 'Organisateur supprimé avec succès';
            break;
            
        case 'delete_event':
            // Supprimer l'événement
            $stmt = $conn->prepare('DELETE FROM evenements WHERE id = ?');
            $stmt->execute([$id]);
            logSecurityEvent('SUPERADMIN_DELETE_EVENT', "Event ID: $id", 'INFO');
            $success = 'Événement supprimé avec succès';
            break;
    }
}

// Statistiques globales
$stats = [
    'total_organisateurs' => $conn->query('SELECT COUNT(*) FROM organisateurs')->fetchColumn(),
    'total_evenements' => $conn->query('SELECT COUNT(*) FROM evenements')->fetchColumn(),
    'total_listes' => $conn->query('SELECT COUNT(*) FROM listes')->fetchColumn(),
    'total_votes' => $conn->query('SELECT COUNT(*) FROM votes')->fetchColumn(),
    'total_participants' => $conn->query('SELECT COUNT(*) FROM participants')->fetchColumn(),
];

// Récupération des organisateurs et de leurs événements
$organisateurs = $conn->query('SELECT * FROM organisateurs ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

// Logs de sécurité récents (les 50 derniers)
$logs = [];
$logFile = './logs/security_' . date('Y-m-d') . '.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $logs = array_slice(array_reverse($lines), 0, 50);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>👑 Super Admin - Tableau de Bord</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <?php printFaviconTag(); addDarkModeScript(); ?>
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .stat-box h3 {
            margin: 0 0 10px 0;
            font-size: 0.9em;
            opacity: 0.9;
            color: white;
        }
        .stat-box .number {
            font-size: 2.5em;
            font-weight: bold;
            margin: 0;
            color: white;
        }
        .action-btn {
            padding: 5px 10px;
            font-size: 0.85em;
            margin: 2px;
        }
        .tab-buttons {
            display: flex;
            gap: 10px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .tab-btn {
            padding: 12px 24px;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .tab-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .log-entry {
            padding: 10px;
            margin: 5px 0;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.85em;
        }
        .log-info { background: #e3f2fd; border-left: 4px solid #2196f3; }
        .log-warning { background: #fff3cd; border-left: 4px solid #ffc107; }
        .log-error { background: #f8d7da; border-left: 4px solid #dc3545; }
    </style>
</head>
<body>
<div class="container card">
    <div class="header" style="text-align: center;">
        <img src="bgsharklo.jpg" alt="Logo" style="max-width: 80px;">
        <h1 style="margin: 15px 0;">👑 Panneau Super Admin</h1>
        <p style="color: #666;">Gestion globale de la plateforme</p>
    </div>

    <?php if (isset($success)): ?>
        <div class="card" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-left: 5px solid #28a745; margin: 20px 0;">
            <p style="margin: 0; color: #155724;">✅ <?php echo htmlspecialchars($success); ?></p>
        </div>
    <?php endif; ?>

    <!-- Statistiques Globales -->
    <div class="stats-grid">
        <div class="stat-box">
            <h3>👥 Organisateurs</h3>
            <p class="number"><?php echo $stats['total_organisateurs']; ?></p>
        </div>
        <div class="stat-box">
            <h3>📅 Événements</h3>
            <p class="number"><?php echo $stats['total_evenements']; ?></p>
        </div>
        <div class="stat-box">
            <h3>📋 Listes</h3>
            <p class="number"><?php echo $stats['total_listes']; ?></p>
        </div>
        <div class="stat-box">
            <h3>🗳️ Votes</h3>
            <p class="number"><?php echo $stats['total_votes']; ?></p>
        </div>
        <div class="stat-box">
            <h3>⏳ En attente</h3>
            <p class="number"><?php echo $stats['total_participants']; ?></p>
        </div>
    </div>

    <!-- Navigation par onglets -->
    <div class="tab-buttons">
        <button class="tab-btn active" onclick="showTab('organisateurs')">👥 Organisateurs</button>
        <button class="tab-btn" onclick="showTab('evenements')">📅 Événements</button>
        <button class="tab-btn" onclick="showTab('logs')">📜 Logs Sécurité</button>
    </div>

    <!-- Onglet Organisateurs -->
    <div id="tab-organisateurs" class="tab-content active">
        <h2>👥 Gestion des Organisateurs</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Événements</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($organisateurs as $orga):
                        $stmt = $conn->prepare('SELECT * FROM evenements WHERE reforga = ?');
                        $stmt->execute([$orga['id']]);
                        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <tr>
                        <td><?php echo $orga['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($orga['nom']); ?></strong></td>
                        <td><?php echo htmlspecialchars($orga['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($orga['email']); ?></td>
                        <td>
                            <?php if ($events): ?>
                                <strong><?php echo count($events); ?></strong> événement(s)
                                <ul style="margin: 5px 0; padding-left: 20px;">
                                    <?php foreach ($events as $event): ?>
                                        <li><?php echo htmlspecialchars($event['nom']); ?> (<?php echo htmlspecialchars($event['univ']); ?>)</li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <span style="color: #999;">Aucun événement</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post" style="display: inline;" onsubmit="return confirm('⚠️ Supprimer cet organisateur ET tous ses événements ?');">
                                <input type="hidden" name="action" value="delete_organizer">
                                <input type="hidden" name="id" value="<?php echo $orga['id']; ?>">
                                <button type="submit" class="btn action-btn" style="background: #ef4444;">🗑️ Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Onglet Événements -->
    <div id="tab-evenements" class="tab-content">
        <h2>📅 Tous les Événements</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Université</th>
                        <th>Organisateur</th>
                        <th>Listes</th>
                        <th>Votes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $allEvents = $conn->query('SELECT e.*, o.nom as orga_nom, o.prenom as orga_prenom, o.email as orga_email FROM evenements e JOIN organisateurs o ON e.reforga = o.id ORDER BY e.id DESC')->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($allEvents as $event):
                        $nbListes = $conn->prepare('SELECT COUNT(*) FROM listes WHERE refevent = ?');
                        $nbListes->execute([$event['id']]);
                        $nbListes = $nbListes->fetchColumn();
                        
                        $nbVotes = $conn->prepare('SELECT COUNT(*) FROM votes WHERE refevent = ?');
                        $nbVotes->execute([$event['id']]);
                        $nbVotes = $nbVotes->fetchColumn();
                    ?>
                    <tr>
                        <td><?php echo $event['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($event['nom']); ?></strong></td>
                        <td><?php echo htmlspecialchars($event['univ']); ?></td>
                        <td><?php echo htmlspecialchars($event['orga_prenom'] . ' ' . $event['orga_nom']); ?><br>
                            <small style="color: #666;"><?php echo htmlspecialchars($event['orga_email']); ?></small>
                        </td>
                        <td><?php echo $nbListes; ?></td>
                        <td><strong><?php echo $nbVotes; ?></strong></td>
                        <td>
                            <a href="resultats.php?id=<?php echo $event['id']; ?>" class="btn action-btn" target="_blank">📊 Résultats</a>
                            <form method="post" style="display: inline;" onsubmit="return confirm('⚠️ Supprimer cet événement ?');">
                                <input type="hidden" name="action" value="delete_event">
                                <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                                <button type="submit" class="btn action-btn" style="background: #ef4444;">🗑️ Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Onglet Logs -->
    <div id="tab-logs" class="tab-content">
        <h2>📜 Logs de Sécurité (Aujourd'hui)</h2>
        <p style="color: #666;">Les 50 dernières entrées</p>
        <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $log): 
                $level = 'info';
                if (strpos($log, 'WARNING') !== false) $level = 'warning';
                if (strpos($log, 'ERROR') !== false) $level = 'error';
            ?>
                <div class="log-entry log-<?php echo $level; ?>">
                    <?php echo htmlspecialchars($log); ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #999;">Aucun log pour aujourd'hui</p>
        <?php endif; ?>
    </div>

    <!-- Déconnexion -->
    <div class="footer" style="margin-top: 40px; text-align: center;">
        <a href="?logout=1" class="btn" style="background: #ef4444;">🚪 Déconnexion</a>
    </div>
</div>

<script>
function showTab(tabName) {
    // Cacher tous les onglets
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Afficher l'onglet sélectionné
    document.getElementById('tab-' + tabName).classList.add('active');
    event.target.classList.add('active');
}
</script>
</body>
</html>
