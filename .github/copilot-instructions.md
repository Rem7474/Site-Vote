# Site-Vote: AI Coding Agent Instructions

## Project Overview
A PHP-based voting system for university events (e.g., BDE elections) with secure, hash-based voting links sent via email. Organizers create events and candidate lists; voters register with university emails and receive unique voting links.

**Security-First Approach**: This application implements comprehensive security measures including CSRF protection, rate limiting, XSS prevention, secure file uploads, and detailed security logging.

## Architecture

### File Organization
- **`admin/`** - Administration interface (organizers)
  - `dashboard.php`, `event.php`, `login.php`, `register.php`, etc.
- **`public/`** - Public interface (voters)
  - `index.php` (registration), `vote.php`, `checkVote.php`, etc.
- **`src/includes/`** - Core PHP logic
  - `fonctionsPHP.php` - Business logic
  - `fonctionsBDD.php` - Database access
  - `fonctionsSecurite.php` - Security functions
  - `inc_header.php`, `inc_admin_menu.php` - UI components
- **`src/config/`** - Configuration
  - `database.php` - Database connection
- **Root files**
  - `index.php` - Entry point (redirects to `public/index.php`)
  - `fonctionsPHP.php` - Compatibility shim (requires `src/includes/fonctionsPHP.php`)
  - `inc_footer.php` - Footer with version number

### Three-Layer Structure
1. **Entry Point Router** (`public/index.php`) - handles all URL routing via GET/POST parameters
2. **Business Logic** (`src/includes/fonctionsPHP.php`) - coordinates workflows and email sending
3. **Data Access** (`src/includes/fonctionsBDD.php`) - all PostgreSQL queries using PDO prepared statements
4. **Security Layer** (`src/includes/fonctionsSecurite.php`) - CSRF, rate limiting, validation, logging

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
1. User registers at `public/index.php?id={eventId}` with university login (format: `prenom.nom`)
2. System generates SHA-256 hash with timestamp salt → stored in `participants` table
3. Email sent with voting link containing hash
4. After vote submitted, participant hash deleted (one-time use)
5. New hash generated for vote verification, stored in `votes` table

## Development Conventions

### Include Pattern
```php
// For admin pages (admin/*.php)
require_once __DIR__ . '/../src/includes/fonctionsPHP.php';

// For public pages (public/*.php)
require_once __DIR__ . '/../src/includes/fonctionsPHP.php';

// For legacy root files (compatibility shim)
include 'fonctionsPHP.php';  // This requires src/includes/fonctionsPHP.php
```

### Database Connection Pattern
```php
// Connection is established in src/includes/fonctionsPHP.php
// $conn is available globally after including fonctionsPHP.php
```
- `$conn` is a **global variable** used throughout the codebase
- All DB functions expect `$conn` as last parameter
- Connection details in `private/parametres.ini` (PostgreSQL, port 5432)

### Function Naming & Organization
- Database functions in `src/includes/fonctionsBDD.php`: `addX()`, `getX()`, `deleteX()` pattern
- All DB functions use `returning id` for inserts
- Functions returning multiple rows MUST use `fetchAll()` not `fetch()`
- Business logic in `src/includes/fonctionsPHP.php`: `InscriptionVote()`, `EnregistrerVote()`, `SendMail()`

### Routing Pattern in public/index.php
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
Used in admin pages - organizer authentication with `password_verify()`

### Security Best Practices
- **ALWAYS use `htmlspecialchars()` when echoing user data** to prevent XSS
- **ALWAYS include CSRF token in forms** using `<?php echo csrfField(); ?>`
- **ALWAYS verify CSRF token** in form handlers: `verifyCSRFToken($_POST['csrf_token'])`
- **ALWAYS use rate limiting** for sensitive actions (login, vote, registration)
- **ALWAYS sanitize input** using `sanitizeInput()` before processing
- **ALWAYS validate file uploads** using `validateFileUpload()` with MIME type checking
- **ALWAYS log security events** using `logSecurityEvent()` for auditing
- Debug mode (`$debug = true`) should ONLY be in development (set in `parametres.ini`)
- Never use variable names that don't match function parameters
- Always use `exit()` after `header()` redirects

### Email System (PHPMailer)
- SMTP credentials in `private/parametres.ini` (variables: `smtp_host`, `smtp_user`, `smtp_pass`)
- Domain configured in `parametres.ini` (variable: `domain`) - used in email links and assets
- Support email configurable in `parametres.ini` (variable: `support_email`)
- HTML email templates inline in `SendMail()` function
- Voting links format: `https://{$DOMAIN}/index.php?hash={hash}` (domain from config)
- **NEVER hardcode domain URLs** - always use `$DOMAIN` global variable

## Common Tasks

### Adding CSRF Protection to a New Form
```php
// In the form HTML
<form method="post">
    <?php echo csrfField(); ?>
    <!-- other fields -->
</form>

// In the form handler
if (!verifyCSRFToken($_POST['csrf_token'])) {
    logSecurityEvent('CSRF_ATTEMPT', 'Description', 'WARNING');
    header('Location: erreur.html');
    exit();
}
```

### Implementing Rate Limiting
```php
// Max 5 attempts every 10 minutes (600 seconds)
if (!checkRateLimit('action_name', 5, 600)) {
    logSecurityEvent('RATE_LIMIT_EXCEEDED', 'Details', 'WARNING');
    header('Location: erreur.html');
    exit();
}
```

### Validating File Uploads
```php
$validation = validateFileUpload($_FILES['photo']);
if (!$validation['valid']) {
    echo htmlspecialchars($validation['error']);
    exit();
}
// File is safe to process
```

### Logging Security Events
```php
// INFO: Normal operation
logSecurityEvent('USER_LOGIN', "Email: $email", 'INFO');

// WARNING: Suspicious activity
logSecurityEvent('INVALID_HASH', "Hash not found", 'WARNING');

// ERROR: System errors
logSecurityEvent('EMAIL_FAILED', "Email: $email", 'ERROR');
```

### Adding a New Database Table
1. Add CRUD functions to `src/includes/fonctionsBDD.php` following table comment pattern:
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
Set `$debug = true` in `private/parametres.ini` to enable PHP error display via `ini_set()` in `src/config/database.php`

### Hash Validation Pattern
```php
$hash = $_GET['hash'];
$eventId = getHash($hash, $conn); // Returns RefEvent or null
if ($eventId) { /* valid */ } else { header('Location: erreur.html'); exit(); }
```

## File Upload Handling
See `admin/event.php` for pattern:
- Extensions whitelist: `jpg, jpeg, png, gif, bmp, webp`
- File naming: `{listName}{eventId}.{extension}`
- Storage: `./images/` directory
- Always validate extension before `move_uploaded_file()`

## Dashboard Features
The dashboard (`admin/dashboard.php`) provides:
- Global statistics (total events, votes, pending participants, lists)
- Event cards with preview images and vote counts
- Direct copy-to-clipboard for share links
- Responsive grid layout with modern UI
- Real-time vote tracking per event and per list

## Integration Points
- **PHPMailer**: Composer dependency (`vendor/autoload.php`)
- **PostgreSQL**: PDO with prepared statements exclusively
- **Domain configuration**: Set via `domain` parameter in `parametres.ini` (available as `$DOMAIN` global variable)

## Key Constraints
- University login format enforced: `/^[a-z]+\.[a-z]+$/` (prenom.nom)
- Email constructed as: `{login}@etu.{university}`
- One vote per participant (hash deleted after voting)
- Organizers can only access their own events (checked via `RefOrga` comparison)
- File uploads limited to 5MB and image types only (with MIME verification)
- Rate limiting enforced on all sensitive operations
- All forms protected with CSRF tokens

## Security Features

### File Structure
```
src/includes/fonctionsSecurite.php  - Security functions (CSRF, rate limit, validation, logging)
src/includes/security_headers.php   - HTTP security headers
docs/SECURITY.md                    - Complete security documentation
database_schema.sql                 - PostgreSQL schema with security comments
logs/                               - Security event logs (not in git)
private/parametres.ini              - Configuration (not in git)
```

### Rate Limits
- Login: 5 attempts / 15 minutes
- Registration (organizer): 3 attempts / 30 minutes  
- Registration (voter): 5 attempts / 5 minutes
- Vote: 3 attempts / 10 minutes

### Security Logs Location
- `./logs/security_YYYY-MM-DD.log`
- Format: `[timestamp] [level] IP: x.x.x.x | Action: ... | Details: ... | User-Agent: ...`
- Levels: INFO, WARNING, ERROR

### Protected Actions
All forms include CSRF protection and are logged:
- User login/registration
- Event creation
- List creation  
- Vote submission
- Voter registration

## Recent Bug Fixes (Oct 2025)
1. ✅ Fixed `getUser()` - corrected variable name from `$event` to `$IDevent`
2. ✅ Fixed `getMembres()` - changed `fetch()` to `fetchAll()` for multiple rows
3. ✅ Fixed `getUsers()` - changed `fetch()` to `fetchAll()` for multiple rows
4. ✅ Fixed `getEquipeVote()` - removed hardcoded team names, now returns actual team name
5. ✅ Added XSS protection with `htmlspecialchars()` across all output
6. ✅ Removed debug mode from production files
7. ✅ Reorganized file structure with `admin/` and `public/` folders
8. ✅ Centralized includes in `src/includes/` and `src/config/`
9. ✅ Removed redundant root redirect files (`dashboard.php`, `event.php`, `vote.php`)
10. ✅ Added footer with version number (`inc_footer.php`)

## Security Improvements (Oct 2025)
1. ✅ Added CSRF protection on all forms
2. ✅ Implemented rate limiting on sensitive actions
3. ✅ Added comprehensive security logging (`./logs/`)
4. ✅ Enhanced file upload validation (MIME type checking)
5. ✅ Improved hash generation with `random_bytes()`
6. ✅ Added input sanitization functions
7. ✅ Created security headers configuration
8. ✅ Added `.gitignore` for sensitive files
9. ✅ Created `SECURITY.md` documentation
10. ✅ Added database schema with security comments
6. ✅ Added input sanitization functions
7. ✅ Created security headers configuration
8. ✅ Added `.gitignore` for sensitive files
9. ✅ Created `SECURITY.md` documentation
10. ✅ Added database schema with security comments

