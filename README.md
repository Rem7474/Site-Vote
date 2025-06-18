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

## Installation rapide (recommandée)

### 1. Clonage et installation automatique
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

### 2. Création de la base de données
- Utilisez le script SQL fourni :
```bash
psql -U <user> -d <dbname> -f schema.sql
```

### 3. Droits d'accès
- Le dossier `images/` doit être accessible en écriture pour l’upload des logos et photos de listes.
- Le dossier `private/` doit être protégé (chmod 700 ou 770).

### 4. Accès
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

## Sécurité
- Les actions sensibles nécessitent une authentification.
- Les suppressions sont confirmées côté client.
- Les votes sont anonymes et non traçables.

## Support
Pour toute question, consultez la page [contact.php](contact.php) ou contactez l’administrateur du site.

---

© 2025 – Projet BDE R&T
