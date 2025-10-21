<?php
// Activer le mode debug en premier pour voir toutes les erreurs
require_once __DIR__ . '/../src/config/debug.php';

session_start();
if(!isset($_SESSION['id'])){
    header('location:login.php');
    exit();
}

include '../src/includes/fonctionsPHP.php';

$idOrga = $_SESSION['id'];

// Gestion de la création d'événement
if(isset($_POST['nom']) && isset($_POST['universite'])){
    if(!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        logSecurityEvent('CSRF_ATTEMPT', 'Dashboard event creation', 'WARNING');
        header('location:../public/erreur.html');
        exit();
    }
    addEvent(sanitizeInput($_POST['nom']), sanitizeInput($_POST['universite']), $idOrga, $conn);
    logSecurityEvent('EVENT_CREATED', "Event: {$_POST['nom']}", 'INFO');
    header('location:dashboard.php');
    exit();
}

// Gestion du logo personnalisé
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    if(!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        logSecurityEvent('CSRF_ATTEMPT', 'Logo upload', 'WARNING');
        header('location:../public/erreur.html');
        exit();
    }
    
    $validation = validateFileUpload($_FILES['logo']);
    if ($validation['valid']) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $dest = '../public/assets/images/logo_' . $idOrga . '.' . $ext;
        move_uploaded_file($_FILES['logo']['tmp_name'], $dest);
        
        // Supprimer anciens logos
        foreach(['jpg','png','webp','jpeg'] as $e) {
            $old = '../public/assets/images/logo_' . $idOrga . '.' . $e;
            if ($old !== $dest && file_exists($old)) unlink($old);
        }
        
        logSecurityEvent('LOGO_UPDATED', "Organizer ID: $idOrga", 'INFO');
        echo "<script>window.toastMessage='Logo mis à jour avec succès';window.toastType='success';setTimeout(()=>location.reload(),800);</script>";
    } else {
        echo "<script>window.toastMessage='".htmlspecialchars($validation['error'])."';window.toastType='error';</script>";
    }
}

// Gestion du favicon personnalisé
if (isset($_POST['upload_favicon']) && isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
    if(!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        logSecurityEvent('CSRF_ATTEMPT', 'Favicon upload', 'WARNING');
        header('location:../public/erreur.html');
        exit();
    }
    
    $fileTmp = $_FILES['favicon']['tmp_name'];
    $fileType = mime_content_type($fileTmp);
    $allowed = ['image/x-icon', 'image/vnd.microsoft.icon', 'image/png'];
    
    if (in_array($fileType, $allowed) && $_FILES['favicon']['size'] <= 5242880) {
        $ext = $fileType === 'image/png' ? 'png' : 'ico';
        $dest = '../public/assets/images/favicon_' . $idOrga . '.' . $ext;
        move_uploaded_file($fileTmp, $dest);
        
        // Supprimer anciens favicons
        foreach(['ico','png'] as $e) {
            $old = '../public/assets/images/favicon_' . $idOrga . '.' . $e;
            if ($old !== $dest && file_exists($old)) unlink($old);
        }
        
        logSecurityEvent('FAVICON_UPDATED', "Organizer ID: $idOrga", 'INFO');
        echo "<script>window.toastMessage='Favicon mis à jour avec succès';window.toastType='success';setTimeout(()=>location.reload(),800);</script>";
    } else {
        echo "<script>window.toastMessage='Format de favicon non supporté';window.toastType='error';</script>";
    }
}

// Récupération des événements de l'utilisateur
$events = getEventsOrga($idOrga, $conn);

// Calcul des statistiques globales
$totalVotes = 0;
$totalParticipants = 0;
$totalListes = 0;
$votesParJour = [];

foreach($events as $event) {
    $eventId = $event['id'];
    $nbVotes = getNbVotes($eventId, $conn);
    $nbParticipants = getNbParticipants($eventId, $conn);
    $listes = getListes($eventId, $conn);
    
    $totalVotes += $nbVotes;
    $totalParticipants += $nbParticipants;
    $totalListes += count($listes);
    
    // Calculer votes par jour (derniers 7 jours)
    // Note: pour l'instant on compte tous les votes aujourd'hui
    // TODO: améliorer pour avoir l'historique réel par date
    $date = date('Y-m-d');
    if(!isset($votesParJour[$date])) {
        $votesParJour[$date] = 0;
    }
    $votesParJour[$date] += $nbVotes;
}

$tauxParticipation = $totalParticipants > 0 ? round(($totalVotes / $totalParticipants) * 100, 1) : 0;
?><!DOCTYPE html>
<html lang="fr">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../public/assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php printFaviconTag(); addDarkModeScript(); ?>
</head>
<body>
<?php include '../src/includes/inc_header.php'; ?>
<?php include '../src/includes/inc_admin_menu.php'; ?>
<div class="container card">
    <h1>Dashboard</h1>
    <!-- Statistiques globales -->
    <div class="card" style="margin-bottom:20px;">
        <h2>📊 Statistiques globales</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;margin-bottom:20px;">
            <div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px;border-radius:12px;box-shadow:0 4px 12px rgba(102,126,234,0.3);">
                <div style="font-size:2em;font-weight:bold;"><?php echo count($events); ?></div>
                <div style="opacity:0.9;">Événements</div>
            </div>
            <div style="background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);color:white;padding:20px;border-radius:12px;box-shadow:0 4px 12px rgba(240,147,251,0.3);">
                <div style="font-size:2em;font-weight:bold;"><?php echo $totalVotes; ?></div>
                <div style="opacity:0.9;">Votes totaux</div>
            </div>
            <div style="background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%);color:white;padding:20px;border-radius:12px;box-shadow:0 4px 12px rgba(79,172,254,0.3);">
                <div style="font-size:2em;font-weight:bold;"><?php echo $totalListes; ?></div>
                <div style="opacity:0.9;">Listes créées</div>
            </div>
            <div style="background:linear-gradient(135deg,#43e97b 0%,#38f9d7 100%);color:white;padding:20px;border-radius:12px;box-shadow:0 4px 12px rgba(67,233,123,0.3);">
                <div style="font-size:2em;font-weight:bold;"><?php echo $tauxParticipation; ?>%</div>
                <div style="opacity:0.9;">Taux participation</div>
            </div>
        </div>
        <div style="background:white;padding:20px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="margin-top:0;">Évolution des votes</h3>
            <canvas id="evolutionVotes" style="max-height:200px;"></canvas>
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
                    <tr><th>Nom</th><th>Université</th><th>Lien d'inscription</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach($events as $event): ?>
                    <?php 
                    $inscriptionLink = 'https://' . $DOMAIN . '/index.php?id=' . $event['id'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['nom']); ?></td>
                        <td><?php echo htmlspecialchars($event['Univ'] ?? getUniversity($event['id'], $conn) ?? ''); ?></td>
                        <td>
                            <button class="btn" onclick="navigator.clipboard.writeText('<?php echo $inscriptionLink; ?>'); this.innerHTML='✅ Copié!'; setTimeout(() => this.innerHTML='📋 Copier le lien', 1500);" style="font-size:0.9em;padding:8px 15px;">📋 Copier le lien</button>
                        </td>
                        <td>
                            <a href="event.php?id=<?php echo $event['id']; ?>" class="btn">Détails</a>
                            <a href="event.php?id=<?php echo $event['id']; ?>&edit=1" class="btn">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <div style="background:#e0f2fe;border-left:4px solid #0ea5e9;padding:15px;margin-top:15px;border-radius:6px;">
                <strong>ℹ️ Comment ça marche ?</strong>
                <ol style="margin:10px 0 0 20px;line-height:1.8;">
                    <li>Partagez le <strong>lien d'inscription</strong> avec les votants</li>
                    <li>Ils s'inscrivent avec leur login universitaire (prenom.nom)</li>
                    <li>Un <strong>email leur est envoyé</strong> avec leur lien de vote unique</li>
                    <li>Ils cliquent sur le lien dans l'email pour voter</li>
                    <li>Après le vote, ils reçoivent un email de confirmation</li>
                </ol>
            </div>
        <?php endif; ?>
        <h3>➕ Créer un nouvel événement</h3>
        <form method="post" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
            <?php echo csrfField(); ?>
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
                <h3 style="margin-bottom:10px;color:#2d3a4b;font-size:1.08em;">🎨 Logo personnalisé</h3>
                <form method="post" enctype="multipart/form-data" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:center;">
                    <?php echo csrfField(); ?>
                    <label for="logo" style="font-weight:bold;">Logo&nbsp;:</label>
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" required style="width:auto;">
                    <input type="submit" value="Mettre à jour" class="btn" style="width:auto;">
                    <?php
                    $idOrga = $_SESSION['id'];
                    $logoPath = 'bgsharklo.jpg';
                    foreach(['jpg','png','webp'] as $ext) {
                        $customLogo = '../public/assets/images/logo_' . $idOrga . '.' . $ext;
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
                        $dest = '../public/assets/images/logo_' . $idOrga . '.' . $ext;
                        move_uploaded_file($fileTmp, $dest);
                        foreach(['jpg','png','webp'] as $e) {
                            $old = '../public/assets/images/logo_' . $idOrga . '.' . $e;
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
                <h3 style="margin-bottom:10px;color:#2d3a4b;font-size:1.08em;">🖼️ Favicon personnalisé</h3>
                <form method="post" enctype="multipart/form-data" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:center;">
                    <?php echo csrfField(); ?>
                    <label for="favicon" style="font-weight:bold;">Favicon&nbsp;:</label>
                    <input type="file" name="favicon" accept="image/x-icon,image/png" required style="width:auto;">
                    <input type="submit" name="upload_favicon" value="Mettre à jour" class="btn" style="width:auto;">
                    <?php
                    $faviconPath = null;
                    foreach(['ico','png'] as $ext) {
                        $customFavicon = '../public/assets/images/favicon_' . $idOrga . '.' . $ext;
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
                        $dest = '../public/assets/images/favicon_' . $idOrga . '.' . $ext;
                        move_uploaded_file($fileTmp, $dest);
                        foreach(['ico','png'] as $e) {
                            $old = '../public/assets/images/favicon_' . $idOrga . '.' . $e;
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

<script>
// Graphique Chart.js - Évolution des votes
const ctx = document.getElementById('evolutionVotes').getContext('2d');
const votesData = <?php echo json_encode(array_values($votesParJour)); ?>;
const votesLabels = <?php echo json_encode(array_keys($votesParJour)); ?>;

new Chart(ctx, {
    type: 'line',
    data: {
        labels: votesLabels.length > 0 ? votesLabels : ['Aucune donnée'],
        datasets: [{
            label: 'Nombre de votes',
            data: votesData.length > 0 ? votesData : [0],
            backgroundColor: 'rgba(102, 126, 234, 0.2)',
            borderColor: 'rgba(102, 126, 234, 1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
</body>
</html>
