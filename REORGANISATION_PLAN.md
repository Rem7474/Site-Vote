# 📁 Plan de Réorganisation - Site-Vote v2.2

## 🎯 Objectif
Réorganiser le code avec une structure professionnelle MVC-like pour améliorer la maintenabilité et l'évolutivité.

---

## 📂 Nouvelle Structure de Dossiers

```
Site-Vote/
├── 📁 public/                    # Point d'entrée public
│   ├── index.php                 # Router principal
│   ├── vote.php                  # Page inscription vote
│   ├── checkVote.php             # Vérification vote
│   ├── resultats.php             # Résultats publics
│   ├── erreur.html               # Page erreur
│   └── assets/
│       ├── css/
│       │   └── styles.css        # CSS principal
│       ├── js/
│       │   ├── toast.js          # Toast notifications
│       │   └── darkmode.js       # Mode sombre
│       └── images/               # Images publiques
│           ├── logo/             # Logos organisateurs
│           ├── candidats/        # Photos candidats
│           └── favicon.png
│
├── 📁 admin/                     # Zone administration
│   ├── index.php                 # Redirect vers dashboard
│   ├── login.php                 # Connexion orga
│   ├── register.php              # Inscription orga
│   ├── logout.php                # Déconnexion
│   ├── dashboard.php             # Dashboard principal
│   ├── event.php                 # Gestion événement
│   ├── list.php                  # Gestion liste
│   ├── gestionMembres.php        # Gestion membres
│   ├── gestionUtilisateurs.php   # Gestion votants
│   ├── gestionLogo.php           # Upload logo
│   ├── exportResultats.php       # Export données
│   ├── statistiques.php          # Stats détaillées
│   ├── superadmin.php            # Super admin
│   └── contact.php               # Support
│
├── 📁 src/                       # Code source
│   ├── config/
│   │   ├── database.php          # Config BDD
│   │   └── constants.php         # Constantes
│   │
│   ├── includes/
│   │   ├── fonctionsBDD.php      # Fonctions DB
│   │   ├── fonctionsPHP.php      # Fonctions métier
│   │   ├── fonctionsSecurite.php # Sécurité
│   │   ├── FonctionsConnexion.php # Connexion DB
│   │   ├── inc_header.php        # Header template
│   │   └── inc_admin_menu.php    # Menu admin
│   │
│   └── templates/
│       ├── header.php            # Template header
│       ├── footer.php            # Template footer
│       └── nav.php               # Navigation
│
├── 📁 private/                   # Fichiers sensibles
│   └── parametres.ini            # Configuration (gitignore)
│
├── 📁 logs/                      # Logs sécurité
│   └── security_*.log            # Logs quotidiens
│
├── 📁 docs/                      # Documentation
│   ├── README.md                 # Doc principale
│   ├── SECURITY.md               # Guide sécurité
│   ├── CHANGELOG.md              # Historique
│   ├── MERGE_REPORT.md           # Rapport merge
│   ├── REFACTORING_REPORT.md     # Rapport refacto
│   └── API.md                    # Documentation API
│
├── 📁 .github/                   # GitHub config
│   └── copilot-instructions.md   # Instructions AI
│
├── .gitignore                    # Git ignore
├── composer.json                 # Dépendances PHP
├── database_schema.sql           # Schéma BDD
└── README.md                     # Readme racine

```

---

## 🔄 Migration des Fichiers

### Phase 1: Configuration & Includes

| Fichier actuel | Nouvelle destination | Statut |
|---------------|----------------------|--------|
| `FonctionsConnexion.php` | `src/config/database.php` | ✅ Renommer |
| `fonctionsBDD.php` | `src/includes/fonctionsBDD.php` | ✅ Déplacer |
| `fonctionsPHP.php` | `src/includes/fonctionsPHP.php` | ✅ Déplacer |
| `fonctionsSecurite.php` | `src/includes/fonctionsSecurite.php` | ✅ Déplacer |
| `inc_header.php` | `src/includes/inc_header.php` | ✅ Déplacer |
| `inc_admin_menu.php` | `src/includes/inc_admin_menu.php` | ✅ Déplacer |
| `security_headers.php` | `src/includes/security_headers.php` | ✅ Déplacer |

### Phase 2: Pages Publiques

| Fichier actuel | Nouvelle destination | Statut |
|---------------|----------------------|--------|
| `index.php` | `public/index.php` | ✅ Déplacer + Update paths |
| `vote.php` | `public/vote.php` | ✅ Déplacer + Update paths |
| `checkVote.php` | `public/checkVote.php` | ✅ Déplacer |
| `formulaireVote.php` | `public/formulaireVote.php` | ✅ Déplacer |
| `confirmationVote.php` | `public/confirmationVote.php` | ✅ Déplacer |
| `confirmationInscription.php` | `public/confirmationInscription.php` | ✅ Déplacer |
| `resultats.php` | `public/resultats.php` | ✅ Déplacer |
| `erreur.html` | `public/erreur.html` | ✅ Déplacer |

### Phase 3: Pages Admin

| Fichier actuel | Nouvelle destination | Statut |
|---------------|----------------------|--------|
| `login.php` | `admin/login.php` | ✅ Déplacer + Update paths |
| `register.php` | `admin/register.php` | ✅ Déplacer |
| `logout.php` | `admin/logout.php` | ✅ Déplacer |
| `dashboard.php` | `admin/dashboard.php` | ✅ Refaire (conflit) |
| `event.php` | `admin/event.php` | ✅ Déplacer |
| `list.php` | `admin/list.php` | ✅ Déplacer |
| `gestionMembres.php` | `admin/gestionMembres.php` | ✅ Déplacer |
| `gestionUtilisateurs.php` | `admin/gestionUtilisateurs.php` | ✅ Déplacer |
| `gestionLogo.php` | `admin/gestionLogo.php` | ✅ Déplacer |
| `exportResultats.php` | `admin/exportResultats.php` | ✅ Déplacer |
| `statistiques.php` | `admin/statistiques.php` | ✅ Déplacer |
| `superadmin.php` | `admin/superadmin.php` | ✅ Améliorer + Déplacer |
| `contact.php` | `admin/contact.php` | ✅ Déplacer |
| `orgaisateur.php` | ❌ Supprimer (doublon?) |

### Phase 4: Assets

| Fichier actuel | Nouvelle destination | Statut |
|---------------|----------------------|--------|
| `styles.css` | `public/assets/css/styles.css` | ✅ Déplacer |
| `toast.js` | `public/assets/js/toast.js` | ✅ Déplacer |
| `favicon.png` | `public/assets/favicon.png` | ✅ Déplacer |
| `bgsharklo.jpg` | `public/assets/images/logo-default.jpg` | ✅ Renommer |
| `candidat*.jpg` | `public/assets/images/candidats/` | ✅ Déplacer |
| `images/*` | `public/assets/images/` | ✅ Déplacer |

### Phase 5: Documentation

| Fichier actuel | Nouvelle destination | Statut |
|---------------|----------------------|--------|
| `README.md` | `docs/README.md` | ✅ Déplacer |
| `SECURITY.md` | `docs/SECURITY.md` | ✅ Déplacer |
| `CHANGELOG.md` | `docs/CHANGELOG.md` | ✅ Déplacer |
| `MERGE_REPORT.md` | `docs/MERGE_REPORT.md` | ✅ Déplacer |
| `REFACTORING_REPORT.md` | `docs/REFACTORING_REPORT.md` | ✅ Déplacer |
| `UNIFORMISATION_COMPLETE.md` | `docs/UNIFORMISATION.md` | ✅ Déplacer |
| `database_schema.sql` | `docs/database_schema.sql` | ✅ Déplacer |

---

## 🔧 Modifications Requises

### Mise à jour des chemins d'inclusion

**Ancien (racine):**
```php
include 'fonctionsPHP.php';
include 'fonctionsBDD.php';
```

**Nouveau (public/):**
```php
include '../src/includes/fonctionsPHP.php';
include '../src/includes/fonctionsBDD.php';
```

**Nouveau (admin/):**
```php
include '../src/includes/fonctionsPHP.php';
include '../src/includes/fonctionsBDD.php';
```

### Mise à jour des chemins CSS/JS

**Ancien:**
```html
<link rel="stylesheet" href="styles.css">
<script src="toast.js"></script>
```

**Nouveau (public/):**
```html
<link rel="stylesheet" href="assets/css/styles.css">
<script src="assets/js/toast.js"></script>
```

**Nouveau (admin/):**
```html
<link rel="stylesheet" href="../public/assets/css/styles.css">
<script src="../public/assets/js/toast.js"></script>
```

### Mise à jour des chemins images

**Ancien:**
```php
<img src="bgsharklo.jpg">
<img src="./images/photo.jpg">
```

**Nouveau:**
```php
<img src="assets/images/logo-default.jpg">
<img src="assets/images/candidats/photo.jpg">
```

---

## ✨ Améliorations Dashboard

### Nouvelles Fonctionnalités

1. **Statistiques Globales en Temps Réel**
   - Total événements
   - Total votes tous événements
   - Participants en attente
   - Total listes

2. **Graphiques (Chart.js)**
   - Évolution votes par jour
   - Répartition votes par événement
   - Taux de participation

3. **Actions Rapides**
   - Créer événement (modal)
   - Copier lien partage
   - Export CSV résultats
   - Voir statistiques détaillées

4. **Filtres et Recherche**
   - Recherche événements
   - Filtre par statut (actif/terminé)
   - Tri par date/votes

5. **Gestion Logo**
   - Upload logo personnalisé
   - Prévisualisation
   - Suppression

---

## ✨ Améliorations Superadmin

### Nouvelles Fonctionnalités

1. **Dashboard Statistiques**
   - Total organisateurs
   - Total événements global
   - Total votes global
   - Activité récente

2. **Gestion Organisateurs**
   - Liste avec recherche
   - Désactiver/Activer compte
   - Voir détails (événements, votes)
   - Supprimer organisateur
   - Envoyer email

3. **Gestion Événements**
   - Vue tous événements
   - Forcer clôture événement
   - Supprimer événement
   - Export données

4. **Logs de Sécurité**
   - Visualisation logs
   - Filtres par niveau (INFO/WARNING/ERROR)
   - Recherche par IP/email
   - Export logs

5. **Statistiques Globales**
   - Graphiques activité
   - Top événements
   - Taux de participation moyen
   - Pics d'utilisation

---

## 🔒 Sécurité Renforcée

### Nouvelles Mesures

1. **CSRF Protection** ✅ Déjà implémenté
2. **Rate Limiting** ✅ Déjà implémenté
3. **Validation Stricte** ✅ Déjà implémenté
4. **Logs Sécurité** ✅ Déjà implémenté

### À Ajouter

5. **Protection Directories**
   - `.htaccess` dans `/logs/`, `/private/`, `/src/`
   - Interdire accès direct

6. **Headers Sécurité**
   - Inclure `security_headers.php` partout
   - HSTS en production

7. **Session Sécurisée**
   - Cookie httponly
   - Cookie secure (HTTPS)
   - Régénération ID session

---

## 📋 Checklist de Migration

### Préparation
- [ ] Backup BDD complète
- [ ] Backup fichiers complets
- [ ] Git commit avant migration
- [ ] Tests locaux complets

### Exécution
- [ ] Créer structure dossiers
- [ ] Déplacer fichiers includes
- [ ] Déplacer fichiers public
- [ ] Déplacer fichiers admin
- [ ] Déplacer assets
- [ ] Déplacer documentation
- [ ] Mettre à jour tous les chemins
- [ ] Créer .htaccess protection
- [ ] Créer index.php redirections

### Validation
- [ ] Tester toutes les pages publiques
- [ ] Tester toutes les pages admin
- [ ] Tester upload fichiers
- [ ] Tester connexion BDD
- [ ] Vérifier logs
- [ ] Scan sécurité

### Finalisation
- [ ] Mettre à jour README
- [ ] Mettre à jour .gitignore
- [ ] Documentation complète
- [ ] Git commit "v2.2 - Restructuration"

---

## 🚀 Prochaines Étapes

1. **Phase 1** - Créer dashboard propre sans conflit
2. **Phase 2** - Améliorer superadmin avec fonctionnalités
3. **Phase 3** - Migration fichiers progressive
4. **Phase 4** - Tests complets
5. **Phase 5** - Déploiement

---

**Version cible**: 2.2.0  
**Objectif**: Structure professionnelle MVC-like  
**Impact**: Meilleure maintenabilité, évolutivité, sécurité
