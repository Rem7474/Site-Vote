# Guide de Sécurité - Site-Vote

## 🔐 Vue d'Ensemble des Mesures de Sécurité

Ce document décrit toutes les mesures de sécurité implémentées dans le système de vote.

## 🛡️ Mesures de Sécurité Implémentées

### 1. Protection CSRF (Cross-Site Request Forgery)
- **Fichier**: `fonctionsSecurite.php`
- **Fonctions**: `generateCSRFToken()`, `verifyCSRFToken()`, `csrfField()`
- **Utilisation**: Tous les formulaires incluent un token CSRF via `<?php echo csrfField(); ?>`
- **Validation**: Chaque soumission de formulaire vérifie le token

### 2. Rate Limiting (Limitation de Taux)
- **Fonction**: `checkRateLimit($action, $max_attempts, $time_window)`
- **Actions protégées**:
  - Login: 5 tentatives / 15 minutes
  - Inscription organisateur: 3 tentatives / 30 minutes
  - Inscription vote: 5 tentatives / 5 minutes
  - Vote: 3 tentatives / 10 minutes

### 3. Protection XSS (Cross-Site Scripting)
- **Toutes** les données utilisateur sont échappées avec `htmlspecialchars()`
- Validation stricte des entrées
- Content Security Policy dans les headers HTTP

### 4. Validation des Entrées
- **Login universitaire**: Format `prenom.nom` (regex: `/^[a-z]+\.[a-z]+$/`)
- **Email**: Validation avec `filter_var()` et `FILTER_VALIDATE_EMAIL`
- **Mots de passe**: Minimum 8 caractères
- **Fichiers**: Validation de l'extension ET du type MIME

### 5. Upload de Fichiers Sécurisé
- **Fonction**: `validateFileUpload()`
- **Vérifications**:
  - Taille maximale: 5 MB
  - Extensions autorisées: jpg, jpeg, png, gif, bmp, webp
  - Vérification du type MIME réel (pas seulement l'extension)
  - Nom de fichier sécurisé (caractères alphanumériques uniquement)

### 6. Hachage Sécurisé
- **Mots de passe**: `password_hash()` avec PASSWORD_DEFAULT (bcrypt)
- **Tokens de vote**: SHA-256 avec `random_bytes(32)`
- **Fonction**: `generateSecureHash()` - utilise random_bytes pour le sel

### 7. Logging de Sécurité
- **Fonction**: `logSecurityEvent($action, $details, $level)`
- **Fichiers**: `./logs/security_YYYY-MM-DD.log`
- **Événements loggés**:
  - Tentatives de connexion (succès/échec)
  - Inscriptions
  - Votes
  - Tentatives CSRF
  - Dépassements de rate limit
  - Accès non autorisés

### 8. Gestion de Session Sécurisée
- Session PHP standard
- Vérification de l'authentification: `requireLogin()`
- Destruction complète à la déconnexion
- Vérification des droits d'accès aux événements

### 9. Headers HTTP de Sécurité
- **Fichier**: `security_headers.php`
- Content-Security-Policy
- X-Content-Type-Options: nosniff
- X-Frame-Options: DENY
- X-XSS-Protection
- Referrer-Policy
- Permissions-Policy
- (Strict-Transport-Security pour HTTPS)

### 10. Protection des Données Sensibles
- **Fichier**: `.gitignore`
- Exclusion de `private/parametres.ini`
- Exclusion des logs
- Exclusion des images uploadées
- Fichier d'exemple: `parametres.ini.example`

## 📋 Checklist de Déploiement en Production

### Avant le Déploiement

- [ ] **Configurer `parametres.ini`**
  - [ ] Copier `parametres.ini.example` vers `parametres.ini`
  - [ ] Remplir les identifiants de base de données
  - [ ] Remplir les identifiants SMTP
  - [ ] **Mettre `$debug = false`**

- [ ] **Permissions des Fichiers**
  - [ ] `chmod 600 private/parametres.ini` (lecture seule propriétaire)
  - [ ] `chmod 755 images/` (lecture/écriture pour uploads)
  - [ ] `chmod 755 logs/` (écriture pour logs)
  - [ ] `chmod 644` pour tous les fichiers PHP

- [ ] **Base de Données**
  - [ ] Créer la base de données PostgreSQL
  - [ ] Créer les tables (voir `database_schema.sql`)
  - [ ] Utiliser un utilisateur avec droits minimaux
  - [ ] Sauvegardes automatiques configurées

- [ ] **Serveur Web**
  - [ ] Activer HTTPS (certificat SSL/TLS)
  - [ ] Décommenter le forcing HTTPS dans `security_headers.php`
  - [ ] Décommenter Strict-Transport-Security
  - [ ] Bloquer l'accès direct au dossier `private/`
  - [ ] Bloquer l'accès direct au dossier `logs/`
  - [ ] Configurer les logs d'erreur Apache/Nginx

- [ ] **PHP**
  - [ ] `display_errors = Off` dans php.ini
  - [ ] `log_errors = On`
  - [ ] `error_log = /path/to/logs/php_errors.log`
  - [ ] Désactiver les fonctions dangereuses (exec, shell_exec, etc.)

- [ ] **Email**
  - [ ] Tester l'envoi d'emails
  - [ ] Configurer SPF, DKIM, DMARC pour éviter le spam
  - [ ] Vérifier que les emails arrivent bien

### Après le Déploiement

- [ ] **Tests de Sécurité**
  - [ ] Tester la protection CSRF
  - [ ] Tester le rate limiting
  - [ ] Vérifier les logs de sécurité
  - [ ] Scanner avec OWASP ZAP ou Burp Suite
  - [ ] Tester l'upload de fichiers malicieux

- [ ] **Monitoring**
  - [ ] Configurer des alertes sur les logs
  - [ ] Surveiller les tentatives d'attaque
  - [ ] Monitorer l'espace disque (logs + images)

- [ ] **Maintenance**
  - [ ] Rotation des logs (logrotate)
  - [ ] Nettoyage des sessions expirées
  - [ ] Mises à jour de sécurité PHP/PostgreSQL

## 🚨 Que Faire en Cas d'Incident

### Attaque Détectée

1. **Bloquer l'IP** dans le pare-feu
2. **Analyser les logs** (`./logs/security_*.log`)
3. **Identifier le vecteur d'attaque**
4. **Corriger la faille** si elle existe
5. **Notifier les utilisateurs** si nécessaire

### Fuite de Données

1. **Changer immédiatement** tous les mots de passe (BDD, SMTP)
2. **Régénérer** le fichier `parametres.ini`
3. **Analyser** comment la fuite s'est produite
4. **Notifier** les utilisateurs concernés (RGPD)
5. **Documenter** l'incident

### Logs à Surveiller

```bash
# Tentatives de CSRF
grep "CSRF_ATTEMPT" logs/security_*.log

# Rate limiting dépassé
grep "RATE_LIMIT_EXCEEDED" logs/security_*.log

# Accès non autorisés
grep "UNAUTHORIZED_ACCESS" logs/security_*.log

# Échecs de connexion
grep "LOGIN_FAILED" logs/security_*.log
```

## 📊 Analyse des Logs

### Format des Logs
```
[YYYY-MM-DD HH:MM:SS] [LEVEL] IP: x.x.x.x | Action: ACTION_NAME | Details: ... | User-Agent: ...
```

### Niveaux de Sévérité
- **INFO**: Événement normal (connexion réussie, vote enregistré)
- **WARNING**: Événement suspect (tentative CSRF, rate limit)
- **ERROR**: Erreur système (échec email, erreur BDD)

## 🔍 Tests de Sécurité Recommandés

### Tests Manuels
1. Tester la protection CSRF (modifier le token)
2. Tester le rate limiting (spam de requêtes)
3. Tester l'upload de fichiers (PHP, exe, scripts)
4. Tester l'injection SQL (prepared statements)
5. Tester l'injection XSS (htmlspecialchars)

### Tests Automatisés
```bash
# OWASP ZAP
zap-cli quick-scan https://vote.remcorp.fr

# SQLMap (injection SQL)
sqlmap -u "https://vote.remcorp.fr/index.php?id=1"

# Nikto (scan de vulnérabilités)
nikto -h https://vote.remcorp.fr
```

## 📚 Ressources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)
- [RGPD](https://www.cnil.fr/)
- [Guide ANSSI](https://www.ssi.gouv.fr/)

## 🆘 Support

En cas de problème de sécurité, contacter immédiatement l'administrateur système.
