# Site-Vote: AI Coding Agent Instructions

## Project Overview
A PHP-based voting system for university events (e.g., BDE elections) with secure, hash-based voting links sent via email. Organizers create events and candidate lists; voters register with university emails and receive unique voting links.

## Architecture

### Three-Layer Structure
1. **Entry Point Router** (`index.php`) - handles all URL routing via GET/POST parameters
2. **Business Logic** (`fonctionsPHP.php`) - coordinates workflows and email sending
3. **Data Access** (`fonctionsBDD.php`) - all PostgreSQL queries using PDO prepared statements

### Database Schema (PostgreSQL)
```
participants: id, hash, RefEvent
evenements: id, Nom, Univ, RefOrga
votes: id, RefListe, hash, RefEvent
utilisateurs: id, login, email, RefEvent
listes: id, RefEvent, Nom, Photo, Description
organisateurs: id, email, password, Nom, Prenom
membres: id, Nom, Prenom, Fonction, RefListe
```

### Critical Security Flow
1. User registers at `index.php?id={eventId}` with university login (format: `prenom.nom`)
2. System generates SHA-256 hash with timestamp salt → stored in `participants` table
3. Email sent with voting link containing hash
4. After vote submitted, participant hash deleted (one-time use)
5. New hash generated for vote verification, stored in `votes` table

## Development Conventions

### Database Connection Pattern
```php
include 'FonctionsConnexion.php';
include 'fonctionsBDD.php';
$conn = connexionBDD('./private/parametres.ini');
```
- `$conn` is a **global variable** used throughout the codebase
- All DB functions expect `$conn` as last parameter
- Connection details in `private/parametres.ini` (PostgreSQL, port 5432)

### Function Naming & Organization
- Database functions in `fonctionsBDD.php`: `addX()`, `getX()`, `deleteX()` pattern
- All DB functions use `returning id` for inserts
- Functions returning multiple rows MUST use `fetchAll()` not `fetch()`
- Business logic in `fonctionsPHP.php`: `InscriptionVote()`, `EnregistrerVote()`, `SendMail()`

### Routing Pattern in index.php
Routes based on POST/GET parameters in priority order:
1. `POST[login] + POST[event]` → registration workflow
2. `POST[vote] + POST[hash]` → vote submission
3. `GET[hash]` → redirect to vote form
4. `GET[id]` → show event registration page

### Session Management
```php
session_start();
$_SESSION['id'], $_SESSION['email'], $_SESSION['nom'], $_SESSION['prenom']
```
Used in `dashboard.php`, `event.php` - organizer authentication with `password_verify()`

### Security Best Practices
- **ALWAYS use `htmlspecialchars()` when echoing user data** to prevent XSS
- Debug mode (`ini_set('display_errors', 1)`) should ONLY be in development
- Never use variable names that don't match function parameters (e.g., `$event` vs `$IDevent`)
- Validate file uploads with extension whitelisting

### Email System (PHPMailer)
- SMTP credentials in `private/parametres.ini` (variables: `$smtp_host`, `$smtp_username`, `$smtp_password`)
- HTML email templates inline in `SendMail()` function
- Voting links format: `https://vote.remcorp.fr/index.php?hash={hash}`

## Common Tasks

### Adding a New Database Table
1. Add CRUD functions to `fonctionsBDD.php` following table comment pattern:
   ```php
   // ********** TABLE TABLENAME : id, field1, field2 **********
   ```
2. Use prepared statements with `:paramName` binding
3. All inserts return ID via `returning id`
4. Use `fetchAll()` for multiple rows, `fetch()` for single row

### Creating New Pages
- Include `fonctionsPHP.php` at top (auto-includes DB functions and connection)
- Use `header('Location: erreur.html')` for error redirects
- **Always add `exit();` after header redirects**
- Follow HTML structure: logo in `.header`, forms in `.container`
- Reference `styles.css` for consistent styling
- Use `htmlspecialchars()` for all user-generated content

### Debug Mode
Set `$debug = true` in `private/parametres.ini` to enable PHP error display via `ini_set()` in `FonctionsConnexion.php`

### Hash Validation Pattern
```php
$hash = $_GET['hash'];
$eventId = getHash($hash, $conn); // Returns RefEvent or null
if ($eventId) { /* valid */ } else { header('Location: erreur.html'); exit(); }
```

## File Upload Handling
See `event.php` for pattern:
- Extensions whitelist: `jpg, jpeg, png, gif, bmp, webp`
- File naming: `{listName}{eventId}.{extension}`
- Storage: `./images/` directory
- Always validate extension before `move_uploaded_file()`

## Dashboard Features
The dashboard (`dashboard.php`) provides:
- Global statistics (total events, votes, pending participants, lists)
- Event cards with preview images and vote counts
- Direct copy-to-clipboard for share links
- Responsive grid layout with modern UI
- Real-time vote tracking per event and per list

## Integration Points
- **PHPMailer**: Composer dependency (`vendor/autoload.php`)
- **PostgreSQL**: PDO with prepared statements exclusively
- **External domain**: Hardcoded `vote.remcorp.fr` in email templates and links

## Key Constraints
- University login format enforced: `/^[a-z]+\.[a-z]+$/` (prenom.nom)
- Email constructed as: `{login}@etu.{university}`
- One vote per participant (hash deleted after voting)
- Organizers can only access their own events (checked via `RefOrga` comparison)

## Recent Bug Fixes (Oct 2025)
1. ✅ Fixed `getUser()` - corrected variable name from `$event` to `$IDevent`
2. ✅ Fixed `getMembres()` - changed `fetch()` to `fetchAll()` for multiple rows
3. ✅ Fixed `getUsers()` - changed `fetch()` to `fetchAll()` for multiple rows
4. ✅ Fixed `getEquipeVote()` - removed hardcoded team names, now returns actual team name
5. ✅ Added XSS protection with `htmlspecialchars()` across all output
6. ✅ Removed debug mode from production files
7. ✅ Fixed share link in dashboard (was pointing to non-existent `vote.php`)

