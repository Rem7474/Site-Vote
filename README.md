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

## Installation

### Prérequis
- Serveur web (Apache/Nginx) avec PHP >= 7.4
- Base de données PostgreSQL
- Composer (pour les dépendances PHP, notamment PHPMailer)

### Procédure
1. **Cloner le projet**
   ```bash
   git clone <repo> && cd Site-Vote
   ```
2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```
3. **Créer la base de données**
   - Importer le schéma SQL fourni (à adapter selon vos besoins).
   - Créer les tables : `organisateurs`, `evenements`, `listes`, `membres`, `participants`, `votes`, `utilisateurs`.

4. **Configurer les paramètres de connexion**
   - Copier le fichier d’exemple dans le dossier `private` :
     ```bash
     cp private/parametres.ini.example private/parametres.ini
     ```
   - Éditer `private/parametres.ini` pour renseigner :
     - `lehost` : hôte de la base de données
     - `dbname` : nom de la base
     - `leport` : port PostgreSQL
     - `user` / `pass` : identifiants
     - `debug` : true/false selon besoin
     - Paramètres SMTP pour l’envoi des mails (PHPMailer)

5. **Droits d’écriture**
   - Le dossier `images/` doit être accessible en écriture pour l’upload des logos et photos de listes.

6. **Accès**
   - Rendez-vous sur `index.php` pour l’inscription des votants.
   - Les organisateurs peuvent s’inscrire et se connecter via `register.php` et `login.php`.
   - Le dashboard admin est accessible après connexion.

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

## Sécurité
- Les actions sensibles nécessitent une authentification.
- Les suppressions sont confirmées côté client.
- Les votes sont anonymes et non traçables.

## Support
Pour toute question, consultez la page [contact.php](contact.php) ou contactez l’administrateur du site.

---

© 2025 – Projet BDE R&T
