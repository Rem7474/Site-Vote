#!/bin/bash
# Script d'installation/update du site de vote
# Conserve le dossier private (paramètres, secrets, etc.) lors d'une update
# Initialise la configuration lors d'une première installation

set -e
REPO_URL="https://github.com/Rem7474/Site-Vote.git"
BRANCH="main"
INSTALL_MODE=""

# 0. Détection install ou update
if [ ! -d ".git" ]; then
  echo "Installation du site (clonage depuis $REPO_URL, branche $BRANCH)"
  git clone -b $BRANCH $REPO_URL .
  INSTALL_MODE=1
else
  echo "Mise à jour du code source..."
  INSTALL_MODE=0
  TMPDIR=$(mktemp -d)
  git clone -b $BRANCH $REPO_URL "$TMPDIR"
  rsync -av --exclude='private' --exclude='.git' --exclude='vendor' "$TMPDIR"/ ./
  rm -rf "$TMPDIR"
fi

# 1. Sauvegarde du dossier private si update
if [ "$INSTALL_MODE" = "0" ] && [ -d "private" ]; then
  echo "Sauvegarde du dossier private..."
  cp -r private private_backup_$(date +%Y%m%d_%H%M%S)
fi

# 2. Création/configuration du dossier private et parametres.ini si install
if [ "$INSTALL_MODE" = "1" ]; then
  mkdir -p private
  PARAM_FILE="private/parametres.ini"
  echo "Configuration initiale du fichier $PARAM_FILE :"
  read -p "Nom de domaine du site (ex: vote.example.com) : " domain
  read -p "Hôte de la base de données (lehost) : " lehost
  read -p "Nom de la base (dbname) : " dbname
  read -p "Port PostgreSQL (leport) [5432] : " leport
  leport=${leport:-5432}
  read -p "Utilisateur BDD (user) : " user
  read -s -p "Mot de passe BDD (pass) : " pass; echo
  read -p "Activer le debug PHP ? (true/false) [false] : " debug
  debug=${debug:-false}
  echo -e "\nConfiguration SMTP pour l'envoi des mails (PHPMailer) :"
  read -p "SMTP Host : " smtp_host
  read -p "SMTP Port : " smtp_port
  read -p "SMTP Username : " smtp_user
  read -s -p "SMTP Password : " smtp_pass; echo
  read -p "Email de support (optionnel, ex: support@$domain) : " support_email
  support_email=${support_email:-support@$domain}
  cat > $PARAM_FILE <<EOF
domain = "$domain"
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
support_email = "$support_email"
EOF
  echo "Fichier $PARAM_FILE généré."
fi

# 3. Création du dossier images si nécessaire et droits d'accès
mkdir -p images
chmod -R 755 images/
chmod -R 755 private/
chmod 755 private/parametres.ini 2>/dev/null || true
chmod 755 "$(pwd)"
chmod +x "$0"

# 4. Vérifications automatiques
# Vérification de la présence de PHP
if ! command -v php >/dev/null 2>&1; then
  echo "Erreur : PHP n'est pas installé. Installez PHP avant de continuer." >&2
  exit 1
fi
# Vérification de la présence de Composer
if ! command -v composer >/dev/null 2>&1; then
  echo "Erreur : Composer n'est pas installé. Installez Composer avant de continuer." >&2
  exit 1
fi
# Vérification des extensions PHP nécessaires
php -m | grep -q pdo_pgsql || { echo "Erreur : l'extension PHP pdo_pgsql est requise."; exit 1; }

# Vérification des droits d'accès
if [ ! -w images/ ]; then
  echo "Attention : le dossier images/ n'est pas accessible en écriture !"
fi
if [ ! -w private/ ]; then
  echo "Attention : le dossier private/ n'est pas accessible en écriture !"
fi

# Vérification de vendor/autoload.php et installation des dépendances si besoin
if [ ! -f vendor/autoload.php ]; then
  echo "Installation des dépendances PHP (composer install)..."
  composer install
fi
if [ ! -f vendor/autoload.php ]; then
  echo "Erreur : vendor/autoload.php est manquant après installation. Vérifiez composer.json et votre installation Composer." >&2
  exit 1
fi

# Vérification de la présence de private/parametres.ini
if [ ! -f private/parametres.ini ]; then
  echo "Erreur : le fichier private/parametres.ini est manquant. Relancez le script en mode installation ou créez-le manuellement." >&2
  exit 1
fi
# Vérification de la complétude de parametres.ini (exemple sur lehost/dbname/user/pass)
grep -q 'lehost' private/parametres.ini || { echo "Erreur : lehost manquant dans parametres.ini"; exit 1; }
grep -q 'dbname' private/parametres.ini || { echo "Erreur : dbname manquant dans parametres.ini"; exit 1; }
grep -q 'user' private/parametres.ini || { echo "Erreur : user manquant dans parametres.ini"; exit 1; }
grep -q 'pass' private/parametres.ini || { echo "Erreur : pass manquant dans parametres.ini"; exit 1; }

# 5. Message de fin
if [ "$INSTALL_MODE" = "1" ]; then
  echo "Installation terminée. Pensez à créer la base de données (voir README)."
else
  echo "Mise à jour terminée. Le dossier private est conservé."
fi
