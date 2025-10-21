# 🗳️ Site-Vote - Système de Vote Sécurisé pour Événements Universitaires

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-blue)](https://www.php.net/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-%3E%3D12-blue)](https://www.postgresql.org/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Security](https://img.shields.io/badge/security-hardened-brightgreen)](SECURITY.md)

Système de vote en ligne sécurisé pour les événements universitaires (élections BDE, associations étudiantes, etc.) avec authentification par email unique et vérification anonyme des votes.

## ✨ Fonctionnalités

### Pour les Organisateurs
- 🔐 **Authentification sécurisée** avec mots de passe hashés (bcrypt)
- 📊 **Dashboard moderne** avec statistiques en temps réel
- 🎯 **Gestion d'événements** multiples
- 📋 **Création de listes** de candidats avec photos
- 📈 **Suivi des votes** en direct
- 🔗 **Liens de partage** pour inscription des votants

### Pour les Votants
- ✉️ **Inscription par email** universitaire (format: prenom.nom@etu.universite)
- 🔒 **Liens uniques et sécurisés** (hash SHA-256)
- 🗳️ **Vote anonyme** et sécurisé
- ✅ **Vérification du vote** avec hash de confirmation
- 📱 **Interface responsive** (mobile-friendly)

## 🛡️ Sécurité

Ce projet implémente des **mesures de sécurité avancées** :

- ✅ **Protection CSRF** sur tous les formulaires
- ✅ **Rate Limiting** pour prévenir les abus
- ✅ **Protection XSS** avec échappement HTML
- ✅ **Validation stricte** des entrées utilisateur
- ✅ **Upload de fichiers sécurisé** (MIME type verification)
- ✅ **Logs de sécurité** détaillés
- ✅ **Headers HTTP sécurisés**
- ✅ **Hachage sécurisé** (bcrypt pour mots de passe, SHA-256 pour votes)

**📖 Voir [SECURITY.md](SECURITY.md) pour plus de détails**

## 🚀 Installation

### Prérequis

- PHP 7.4 ou supérieur
- PostgreSQL 12 ou supérieur
- Serveur web (Apache/Nginx)
- Composer (pour PHPMailer)
- Extension PHP: PDO, pdo_pgsql, mbstring, fileinfo

### Étapes d'Installation

1. **Cloner le repository**
```bash
git clone https://github.com/Rem7474/Site-Vote.git
cd Site-Vote
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Créer la base de données**
```bash
# Se connecter à PostgreSQL
psql -U postgres

# Créer la base de données
CREATE DATABASE vote_db;
\c vote_db

# Importer le schéma
\i database_schema.sql
```

4. **Configurer l'application**
```bash
# Copier le fichier de configuration exemple
cp private/parametres.ini.example private/parametres.ini

# Éditer avec vos paramètres
nano private/parametres.ini
```

Configuration à remplir :
- Identifiants PostgreSQL
- Identifiants SMTP pour envoi d'emails
- Mettre `$debug = false` en production

5. **Configurer les permissions**
```bash
# Permissions des fichiers
chmod 600 private/parametres.ini
chmod 755 images/
chmod 755 logs/
chmod 644 *.php

# Créer les dossiers nécessaires
mkdir -p images logs
```

6. **Configurer le serveur web**

**Apache (.htaccess)**
```apache
# Bloquer l'accès aux fichiers sensibles
<FilesMatch "\.(ini|log|md)$">
    Require all denied
</FilesMatch>

# Bloquer l'accès aux dossiers
RedirectMatch 403 ^/private/
RedirectMatch 403 ^/logs/
```

**Nginx**
```nginx
location ~ \.(ini|log|md)$ {
    deny all;
}

location ~ ^/(private|logs)/ {
    deny all;
}
```

7. **Tester l'installation**
- Accéder à `http://votre-domaine.com/register.php`
- Créer un compte organisateur
- Se connecter et créer un événement

## 📁 Structure du Projet

```
Site-Vote/
├── .github/
│   └── copilot-instructions.md    # Instructions pour AI agents
├── admin/                         # Zone administration
│   ├── dashboard.php              # Dashboard organisateur
│   ├── event.php                  # Gestion d'événement
│   ├── list.php                   # Gestion de liste
│   ├── login.php                  # Connexion organisateur
│   ├── register.php               # Inscription organisateur
│   ├── logout.php                 # Déconnexion
│   ├── gestionMembres.php         # Gestion membres d'équipe
│   ├── gestionUtilisateurs.php    # Gestion votants
│   ├── gestionLogo.php            # Gestion logo personnalisé
│   ├── statistiques.php           # Statistiques détaillées
│   ├── exportResultats.php        # Export résultats
│   ├── contact.php                # Contact support
│   └── superadmin.php             # Admin global
├── public/                        # Zone publique (votants)
│   ├── index.php                  # Inscription votant
│   ├── vote.php                   # Affichage bulletin de vote
│   ├── formulaireVote.php         # Formulaire de vote
│   ├── checkVote.php              # Vérification de vote
│   ├── confirmationInscription.php # Confirmation inscription
│   ├── confirmationVote.php       # Confirmation vote
│   ├── resultats.php              # Résultats publics
│   ├── erreur.html                # Page d'erreur
│   └── assets/                    # Assets publics
│       ├── css/
│       │   └── styles.css         # Styles CSS
│       └── images/
│           └── bgsharklo.jpg      # Image de fond
├── src/                           # Code source
│   ├── config/
│   │   └── database.php           # Configuration BDD
│   └── includes/
│       ├── fonctionsPHP.php       # Logique métier
│       ├── fonctionsBDD.php       # Accès base de données
│       ├── fonctionsSecurite.php  # Fonctions de sécurité
│       ├── security_headers.php   # Headers HTTP sécurisés
│       ├── inc_header.php         # Header commun
│       └── inc_admin_menu.php     # Menu admin
├── private/
│   ├── parametres.ini             # Configuration (non versionné)
│   └── parametres.ini.example     # Exemple de configuration
├── images/                        # Images uploadées (non versionné)
├── logs/                          # Logs de sécurité (non versionné)
├── vendor/                        # Dépendances Composer
├── docs/                          # Documentation
│   ├── SECURITY.md                # Documentation sécurité
│   └── CHANGELOG.md               # Historique des changements
├── index.php                      # Point d'entrée (redirige vers public/)
├── inc_footer.php                 # Footer avec version
├── fonctionsPHP.php               # Shim de compatibilité
├── database_schema.sql            # Schéma PostgreSQL
├── update_install.sh              # Script d'installation
├── README.md                      # Ce fichier
└── .gitignore                     # Fichiers à ignorer

```

## 🔧 Configuration

### Variables d'Environnement (parametres.ini)

```ini
# Domaine du site
domain = "vote.example.com"

# Base de données PostgreSQL
lehost = "localhost"
leport = "5432"
dbname = "vote_db"
user = "vote_user"
pass = "secure_password"
debug = false  # true en développement, false en production

# Configuration SMTP pour l'envoi d'emails
smtp_host = "smtp.gmail.com"
smtp_port = "587"
smtp_user = "your-email@gmail.com"
smtp_pass = "app-specific-password"

# Email de support (optionnel)
support_email = "support@vote.example.com"

# Configuration superadministrateur (accès complet à la plateforme)
superadmin_user = "superadmin"
superadmin_pass = "VotreMotDePasseSecurise2024!"
```

**Note**: Le domaine configuré dans `parametres.ini` est automatiquement utilisé dans :
- Les liens de vote envoyés par email
- Les assets (CSS, images) dans les emails
- L'email de support dans la page contact

## 📊 Utilisation

### Créer un Événement

1. S'inscrire comme organisateur sur `/admin/register.php`
2. Se connecter sur `/admin/login.php`
3. Créer un événement depuis le dashboard
4. Ajouter des listes de candidats
5. Partager le lien d'inscription aux votants

### Voter

1. Recevoir le lien d'inscription de l'organisateur
2. S'inscrire avec son login universitaire (prenom.nom)
3. Recevoir un email avec le lien de vote unique
4. Cliquer sur le lien et voter
5. Recevoir un hash de confirmation

### Vérifier son Vote

1. Aller sur `/public/checkVote.php`
2. Entrer le hash de confirmation
3. Voir pour quelle liste on a voté

## 🔍 Monitoring et Logs

Les logs de sécurité sont dans `./logs/security_YYYY-MM-DD.log`

```bash
# Voir les tentatives de CSRF
grep "CSRF_ATTEMPT" logs/security_*.log

# Voir les dépassements de rate limit
grep "RATE_LIMIT_EXCEEDED" logs/security_*.log

# Voir les connexions échouées
grep "LOGIN_FAILED" logs/security_*.log
```

## 🧪 Tests

### Tests de Sécurité Recommandés

```bash
# OWASP ZAP
zap-cli quick-scan https://votre-domaine.com

# Nikto
nikto -h https://votre-domaine.com
```

### Tests Manuels

- ✅ Tester la protection CSRF (modifier le token)
- ✅ Tester le rate limiting (spam de requêtes)
- ✅ Tester l'upload de fichiers malicieux
- ✅ Tester l'injection SQL
- ✅ Tester l'injection XSS

## 📈 Performance

### Optimisations Recommandées

- **Cache**: Utiliser Redis/Memcached pour les statistiques
- **CDN**: Servir les images statiques via CDN
- **Compression**: Activer gzip sur le serveur web
- **Indexes**: Le schéma SQL inclut déjà les index nécessaires

## 🐛 Bugs Connus et Limitations

- Les emails peuvent finir en spam (configurer SPF/DKIM/DMARC)
- Pas de graphiques pour visualiser les résultats
- Pas d'export CSV/PDF des résultats
- Pas de mode sombre pour l'interface

## 🛠️ Développement

### Pour Contribuer

1. Fork le projet
2. Créer une branche (`git checkout -b feature/amelioration`)
3. Commit les changements (`git commit -m 'Ajout fonctionnalité'`)
4. Push vers la branche (`git push origin feature/amelioration`)
5. Ouvrir une Pull Request

### Conventions de Code

- PSR-12 pour le style PHP
- Commentaires en français
- Documentation des fonctions
- Tests de sécurité obligatoires

## 📝 Changelog

Voir [CHANGELOG.md](CHANGELOG.md) pour l'historique complet des modifications.

## 🔐 Sécurité

Pour signaler une vulnérabilité de sécurité, **NE PAS** ouvrir une issue publique.
Contacter directement les mainteneurs.

Voir [SECURITY.md](SECURITY.md) pour plus d'informations.

## 📄 License

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 👥 Auteurs

- **Rem7474** - *Développement initial*

## 🙏 Remerciements

- PHPMailer pour l'envoi d'emails
- PostgreSQL pour la base de données robuste
- OWASP pour les recommandations de sécurité

## 📞 Support

- 📧 Email: support@votre-domaine.com
- 💬 Issues: [GitHub Issues](https://github.com/Rem7474/Site-Vote/issues)
- 📖 Documentation: Voir les fichiers `.md` dans le projet

---

**⚠️ Important**: Ce système gère des données sensibles (votes). Assurez-vous de suivre toutes les recommandations de sécurité dans [SECURITY.md](SECURITY.md) avant le déploiement en production.
