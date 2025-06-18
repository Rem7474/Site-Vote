#!/bin/bash
# Script d'installation/update du site de vote
# Conserve le dossier private (paramètres, secrets, etc.) lors d'une update
# Initialise la configuration lors d'une première installation

set -e
REPO_URL="https://github.com/Rem7474/Site-Vote.git"
BRANCH="beta"

# 0. Détection install ou update
if [ ! -d ".git" ]; then
  echo "Installation du site (clonage depuis $REPO_URL, branche $BRANCH)"
  git clone -b $BRANCH $REPO_URL .
  INSTALL_MODE=1
else
  echo "Mise à jour du code source..."
  git pull origin $BRANCH
  INSTALL_MODE=0
fi

# 1. Sauvegarde du dossier private si update
if [ $INSTALL_MODE -eq 0 ] && [ -d "private" ]; then
  echo "Sauvegarde du dossier private..."
  cp -r private private_backup_$(date +%Y%m%d_%H%M%S)
fi

# 2. Installation des dépendances PHP
if [ -f "composer.json" ]; then
  echo "Installation des dépendances PHP..."
  composer install
fi

# 3. Création/configuration du dossier private et parametres.ini si install
if [ $INSTALL_MODE -eq 1 ]; then
  mkdir -p private
  PARAM_FILE="private/parametres.ini"
  echo "Configuration initiale du fichier $PARAM_FILE :"
  read -p "Hôte de la base de données (lehost) : " lehost
  read -p "Nom de la base (dbname) : " dbname
  read -p "Port PostgreSQL (leport) [5432] : " leport
  leport=${leport:-5432}
  read -p "Utilisateur BDD (user) : " user
  read -s -p "Mot de passe BDD (pass) : " pass; echo
  read -p "Activer le debug PHP ? (true/false) [false] : " debug
  debug=${debug:-false}
  echo "\nConfiguration SMTP pour l'envoi des mails (PHPMailer) :"
  read -p "SMTP Host : " smtp_host
  read -p "SMTP Port : " smtp_port
  read -p "SMTP Username : " smtp_user
  read -s -p "SMTP Password : " smtp_pass; echo
  cat > $PARAM_FILE <<EOF
lehost = "$lehost"
dbname = "$dbname"
leport = "$leport"
user = "$user"
pass = "$pass"
debug = $debug
smtp_host = "$smtp_host"
smtp_port = "$smtp_port"
smtp_user = "$smtp_user"
smtp_pass = "$smtp_pass"
EOF
  echo "Fichier $PARAM_FILE généré."
fi

# 4. Droits d'accès
chmod -R 770 images/
chmod -R 700 private/

# 5. Message de fin
if [ $INSTALL_MODE -eq 1 ]; then
  echo "Installation terminée. Pensez à créer la base de données (voir README)."
else
  echo "Mise à jour terminée. Le dossier private est conservé."
fi
