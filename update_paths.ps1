# Script de mise à jour des chemins après réorganisation
Write-Host "=== Mise à jour des chemins d'inclusion ===" -ForegroundColor Cyan

# Fichiers publics
$publicFiles = Get-ChildItem -Path "public\*.php" -Recurse
foreach ($file in $publicFiles) {
    $content = Get-Content $file.FullName -Raw
    
    # Mise à jour des includes
    $content = $content -replace "include 'fonctionsPHP\.php';", "include '../src/includes/fonctionsPHP.php';"
    $content = $content -replace "include 'fonctionsBDD\.php';", "include '../src/includes/fonctionsBDD.php';"
    $content = $content -replace "include 'FonctionsConnexion\.php';", "include '../src/config/database.php';"
    $content = $content -replace "include 'inc_header\.php';", "include '../src/includes/inc_header.php';"
    
    # Mise à jour des chemins CSS/JS
    $content = $content -replace 'href="styles\.css"', 'href="assets/css/styles.css"'
    $content = $content -replace 'src="toast\.js"', 'src="assets/js/toast.js"'
    
    # Mise à jour des chemins images
    $content = $content -replace '\./images/', 'assets/images/'
    $content = $content -replace 'bgsharklo\.jpg', 'assets/images/logo-default.jpg'
    
    Set-Content $file.FullName -Value $content
    Write-Host "✓ $($file.Name)" -ForegroundColor Green
}

# Fichiers admin
$adminFiles = Get-ChildItem -Path "admin\*.php" -Recurse
foreach ($file in $adminFiles) {
    $content = Get-Content $file.FullName -Raw
    
    # Mise à jour des includes
    $content = $content -replace "include 'fonctionsPHP\.php';", "include '../src/includes/fonctionsPHP.php';"
    $content = $content -replace "include 'fonctionsBDD\.php';", "include '../src/includes/fonctionsBDD.php';"
    $content = $content -replace "include 'FonctionsConnexion\.php';", "include '../src/config/database.php';"
    $content = $content -replace "include 'inc_header\.php';", "include '../src/includes/inc_header.php';"
    $content = $content -replace "include 'inc_admin_menu\.php';", "include '../src/includes/inc_admin_menu.php';"
    
    # Mise à jour des chemins CSS/JS
    $content = $content -replace 'href="styles\.css"', 'href="../public/assets/css/styles.css"'
    $content = $content -replace 'src="toast\.js"', 'src="../public/assets/js/toast.js"'
    
    # Mise à jour des chemins images
    $content = $content -replace '\./images/', '../public/assets/images/'
    $content = $content -replace 'bgsharklo\.jpg', '../public/assets/images/logo-default.jpg'
    
    # Mise à jour des redirections
    $content = $content -replace "header\('location:login\.php'\);", "header('location:login.php');"
    $content = $content -replace "header\('location:dashboard\.php'\);", "header('location:dashboard.php');"
    $content = $content -replace "header\('Location: erreur\.html'\);", "header('Location: ../public/erreur.html');"
    
    Set-Content $file.FullName -Value $content
    Write-Host "✓ $($file.Name)" -ForegroundColor Green
}

Write-Host "`n=== Mise à jour terminée ===" -ForegroundColor Cyan
Write-Host "Vérifiez manuellement les fichiers critiques :" -ForegroundColor Yellow
Write-Host "- admin/dashboard.php" -ForegroundColor Yellow
Write-Host "- admin/login.php" -ForegroundColor Yellow
Write-Host "- public/index.php" -ForegroundColor Yellow
Write-Host "- public/formulaireVote.php" -ForegroundColor Yellow
