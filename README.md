<<<<<<< HEAD
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
├── private/
│   ├── parametres.ini             # Configuration (non versionné)
│   └── parametres.ini.example     # Exemple de configuration
├── images/                        # Images uploadées (non versionné)
├── logs/                          # Logs de sécurité (non versionné)
├── vendor/                        # Dépendances Composer
├── index.php                      # Point d'entrée principal
├── dashboard.php                  # Dashboard organisateur
├── event.php                      # Gestion d'événement
├── login.php                      # Connexion organisateur
├── register.php                   # Inscription organisateur
├── vote.php                       # Page d'inscription vote
├── formulaireVote.php             # Formulaire de vote
├── checkVote.php                  # Vérification de vote
├── confirmationInscription.php    # Confirmation inscription
├── confirmationVote.php           # Confirmation vote
├── fonctionsPHP.php               # Logique métier
├── fonctionsBDD.php               # Accès base de données
├── fonctionsSecurite.php          # Fonctions de sécurité
├── FonctionsConnexion.php         # Connexion BDD
├── security_headers.php           # Headers HTTP sécurisés
├── logout.php                     # Déconnexion
├── styles.css                     # Styles CSS
├── database_schema.sql            # Schéma PostgreSQL
├── SECURITY.md                    # Documentation sécurité
├── CHANGELOG.md                   # Historique des changements
├── README.md                      # Ce fichier
└── .gitignore                     # Fichiers à ignorer

```

## 🔧 Configuration

### Variables d'Environnement (parametres.ini)

```php
// Base de données
$lehost = "localhost";
$leport = "5432";
$dbname = "vote_db";
$user = "vote_user";
$pass = "secure_password";
$debug = false;  // true en développement, false en production

// Email SMTP
$smtp_host = "smtp.gmail.com";
$smtp_username = "your-email@gmail.com";
$smtp_password = "app-specific-password";
```

### Domaine de Production

Le domaine est hardcodé dans plusieurs fichiers :
- `fonctionsPHP.php` (emails)
- `dashboard.php` (liens de partage)

Remplacer `vote.remcorp.fr` par votre domaine.

## 📊 Utilisation

### Créer un Événement

1. S'inscrire comme organisateur sur `/register.php`
2. Se connecter sur `/login.php`
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

1. Aller sur `/checkVote.php`
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
=======
# Site de Vote en Ligne – BDE R&T

## Présentation
Ce projet est une plateforme complète de gestion de votes en ligne, adaptée à des élections associatives, étudiantes ou tout autre événement nécessitant un scrutin sécurisé et transparent.

### Fonctionnalités principales
- **Inscription des votants** par login universitaire, avec envoi d’un lien de vote sécurisé par email.
- **Vote anonyme** via un hash unique, non traçable.
- **Vérification du vote** par hash après participation.
- **Gestion multi-événements** : chaque organisateur peut créer et gérer ses propres élections.
- **Gestion des listes/candidats** : ajout, modification, suppression, gestion des membres de chaque liste.
- **Tableau de bord organisateur** : accès rapide à tous les outils d’administration, graphiques de votes en temps réel, export des résultats, gestion des utilisateurs, etc.
- **Affichage dynamique des résultats** (pour chaque événement, avec graphiques et gagnant).
- **Export des résultats** au format CSV.
- **Statistiques avancées** : évolution temporelle des votes, répartition par liste, taux de participation, etc.
- **Gestion du logo personnalisé** pour chaque organisateur (affiché sur toutes ses pages).
- **Page de contact/FAQ**.
- **Sécurité** : authentification organisateur, confirmation avant suppression, gestion des erreurs, redirections sécurisées.

## Problèmes courants lors de l'installation/mise à jour

### Conflits git : "Your local changes to the following files would be overwritten by merge"

Si vous voyez ce message lors de l'exécution du script ou d'un `git pull` :

```
error: Your local changes to the following files would be overwritten by merge:
  ...
Please commit your changes or stash them before you merge.
error: The following untracked working tree files would be overwritten by merge:
  ...
Please move or remove them before you merge.
Aborting
```

Cela signifie que vous avez des modifications locales non enregistrées ou des fichiers non suivis qui bloquent la mise à jour.

#### Solutions possibles :

1. **Sauvegarder et committer vos modifications locales**
   ```bash
   git add .
   git commit -m "Sauvegarde locale avant update"
   git pull origin beta
   ```
2. **Ou stasher vos modifications (temporairement)**
   ```bash
   git stash
   git pull origin beta
   git stash pop # pour récupérer vos modifs après
   ```
3. **Ou supprimer les fichiers non suivis qui bloquent**
   ```bash
   git clean -f
   # ou pour supprimer aussi les dossiers non suivis :
   git clean -fd
   ```

Veillez à bien sauvegarder tout travail important avant d'utiliser ces commandes.

## Installation rapide (recommandée)

### 1. Installation directe du script d'installation/mise à jour
Vous pouvez télécharger et exécuter uniquement le script d’installation/mise à jour sans cloner tout le dépôt :

```bash
curl -O https://raw.githubusercontent.com/Rem7474/Site-Vote/beta/update_install.sh
chmod +x update_install.sh
./update_install.sh
```

Ce script gère l’installation complète ou la mise à jour du site, en conservant le dossier `private` si présent.

### 2. Clonage et installation automatique (alternative)
Utilisez le script fourni pour installer ou mettre à jour le site. Ce script gère le clonage, l'installation des dépendances et la configuration initiale.

```bash
# Pour une première installation :
git clone -b beta https://github.com/Rem7474/Site-Vote.git
cd Site-Vote
chmod +x update_install.sh
./update_install.sh
```

- Lors de la première installation, le script vous demandera toutes les informations nécessaires pour générer le fichier `private/parametres.ini` (connexion BDD, SMTP, etc.).
- Pour une mise à jour, relancez simplement `./update_install.sh` dans le dossier du projet : le dossier `private` et vos paramètres seront conservés.

### 3. Création de la base de données
- Utilisez le script SQL fourni :
```bash
psql -U <user> -d <dbname> -f schema.sql
```

### 4. Droits d'accès
- Le dossier `images/` doit être accessible en écriture pour l’upload des logos et photos de listes.
- Le dossier `private/` doit être protégé (chmod 700 ou 770).

### 5. Accès
- Rendez-vous sur `index.php` pour l’inscription des votants.
- Les organisateurs peuvent s’inscrire et se connecter via `register.php` et `login.php`.
- Le dashboard admin est accessible après connexion.

## Mise à jour
Pour mettre à jour le site sans perdre la configuration :
```bash
./update_install.sh
```

## Arborescence des fichiers principaux
- `index.php` : point d’entrée général
- `dashboard.php` : portail admin
- `event.php` : gestion d’un événement
- `formulaireVote.php` : page de vote
- `resultats.php` : résultats dynamiques
- `statistiques.php` : statistiques avancées
- `gestionUtilisateurs.php` : gestion des votants
- `gestionMembres.php` : gestion des membres de liste
- `exportResultats.php` : export CSV
- `contact.php` : FAQ/Contact
- `styles.css` : design du site
- `private/parametres.ini` : configuration (à adapter)

## Personnalisation
- Chaque organisateur peut téléverser son logo depuis le dashboard.
- Les couleurs et le style sont modifiables dans `styles.css`.

## Accès Super Admin

Une interface super admin est disponible pour la gestion globale des organisateurs et de leurs événements.

- Accès : `superadmin.php`
- Authentification : les identifiants sont à définir dans le fichier `private/parametres.ini` :

```
superadmin_user = "admin"
superadmin_pass = "motdepasse"
```

Le super admin peut voir la liste de tous les organisateurs, leurs emails, et les événements associés.

## Personnalisation avancée
- Chaque organisateur peut téléverser son logo personnalisé (affiché sur toutes ses pages).
- Les couleurs principales du thème peuvent être modifiées dans `styles.css`.
- Le favicon peut être personnalisé par événement en ajoutant un fichier `favicon_<idOrga>.ico` dans le dossier `images/`.
- Un mode sombre est disponible (bouton en haut à droite du dashboard).

## Ergonomie et design
- Transitions et animations douces sur les boutons, notifications et menus.
- Menu burger et navigation simplifiée sur mobile.
- Double confirmation pour les actions sensibles (suppression, export, etc.).

## Sécurité
- Les actions sensibles nécessitent une authentification.
- Les suppressions sont confirmées côté client.
- Les votes sont anonymes et non traçables.

## Support
Pour toute question, consultez la page [contact.php](contact.php) ou contactez l’administrateur du site.

---

© 2025 – Projet BDE R&T
>>>>>>> origin/beta
