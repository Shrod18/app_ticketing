# 🎨 GUIDE DE BONNES PRATIQUES - Clean Code

## 📝 Conventions de code PHP

### 1. Nommage
```php
// ✅ BON
class IncidentController { }
public function createIncident() { }
private $incident_id;
const MAX_FILE_SIZE = 5242880;

// ❌ MAUVAIS
class incident_controller { }
public function create() { }
private $i_d;
const MAX = 5242880;
```

### 2. Structure des fichiers
```php
<?php
// 1. Déclaration namespace + imports
namespace App\Controllers;

// 2. Commentaire classe
/**
 * Gère l'authentification
 */
class AuthController {
    
    // 3. Propriétés
    private $db;
    
    // 4. Constructeur
    public function __construct($db) {
        $this->db = $db;
    }
    
    // 5. Méthodes publiques
    public function login() { }
    
    // 6. Méthodes privées
    private function validateInput() { }
}
?>
```

### 3. Commentaires
```php
// ✅ BON - Commentaires explicatifs
/**
 * Crée un nouvel incident
 * @param string $titre Le titre de l'incident
 * @param string $description La description détaillée
 * @return int|bool L'ID du nouvel incident ou false
 */
public function createIncident($titre, $description) {
    // Validation du titre
    if (empty($titre)) {
        return false;
    }
    // ...
}

// ❌ MAUVAIS - Commentaires inutiles
// Incrémenter i
$i++;

// ✅ BON - Code auto-documenté (pas besoin de commentaire)
$total_open_incidents = count(array_filter($incidents, fn($i) => $i['statut'] === 'ouvert'));
```

### 4. Gestion d'erreurs
```php
// ✅ BON - Try/catch avec logging
try {
    $db = $database->connect();
    if (!$db) {
        throw new Exception("Connexion BD échouée");
    }
} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
    $_SESSION['error'] = "Une erreur est survenue";
}

// ❌ MAUVAIS - Ignorer les erreurs
$db = $database->connect();
```

### 5. Validation des données
```php
// ✅ BON - Validation stricte
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email invalide";
}

if (strlen($password) < 6) {
    $errors[] = "Mot de passe trop court";
}

// ❌ MAUVAIS - Pas de validation
$email = $_POST['email'];
$password = $_POST['password'];
```

### 6. Échappement des données
```php
// ✅ BON - Échapper pour HTML
<p><?php echo htmlspecialchars($user_input); ?></p>

// ✅ BON - Requêtes préparées
$stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);

// ❌ MAUVAIS - Concaténation directe (SQL injection!)
$query = "SELECT * FROM users WHERE email = '$email'";

// ❌ MAUVAIS - XSS
<p><?php echo $user_input; ?></p>
```

---

## 🎯 Principes SOLID

### S - Single Responsibility
```php
// ✅ BON - Chaque classe une responsabilité
class User { }           // Logique utilisateur
class UserController { } // HTTP request handling
class UserRepository { } // BD access

// ❌ MAUVAIS
class User {
    public function authenticate() { }
    public function queryDB() { }
    public function sendEmail() { }
}
```

### O - Open/Closed
```php
// ✅ BON - Ouvert à l'extension, fermé à la modification
interface UserRepository {
    public function find($id);
}

class MySQLUserRepository implements UserRepository { }
class PostgresUserRepository implements UserRepository { }

// ❌ MAUVAIS - Modification requise pour chaque nouveau DB
if ($db_type === 'mysql') { }
if ($db_type === 'postgres') { }
```

### L - Liskov Substitution
```php
// ✅ BON - Substitutable
interface Engineer {
    public function assignIncident($id);
}

class SeniorEngineer implements Engineer { }
class JuniorEngineer implements Engineer { }

// Utilisation polymorphe
foreach ($engineers as $engineer) {
    $engineer->assignIncident($incident_id);
}
```

### I - Interface Segregation
```php
// ✅ BON - Interfaces spécifiques
interface IncidentCreatable {
    public function create();
}

interface IncidentClosable {
    public function close();
}

// ❌ MAUVAIS
interface IncidentService {
    public function create();
    public function close();
    public function transfer();
    public function comment();
    // ...50 autres méthodes
}
```

### D - Dependency Injection
```php
// ✅ BON - Injection
class IncidentController {
    public function __construct(Database $db) {
        $this->db = $db;
    }
}

$db = new Database();
$controller = new IncidentController($db);

// ❌ MAUVAIS - Couplage
class IncidentController {
    private $db;
    
    public function __construct() {
        $this->db = new Database(); // Hardcoded
    }
}
```

---

## 🧹 Refactorisation - Avant/Après

### Exemple 1: Trop long
```php
// ❌ AVANT - 50 lignes, complexe
public function handleIncidentCreation() {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    // ... validation inline
    // ... upload fichiers
    // ... query BD
    // ... logs
}

// ✅ APRÈS - Séparation des responsabilités
public function createIncident() {
    $data = $this->validateData($_POST);
    $incident_id = $this->createIncidentRecord($data);
    $this->handleFileUploads($incident_id);
    return $incident_id;
}
```

### Exemple 2: Conditions complexes
```php
// ❌ AVANT
if ($incident['statut'] !== 'clôturé' && $incident['assigned_to'] === $current_user 
    && (!isset($_POST['transfer_pending']) || !$_POST['transfer_pending'])) {
    // Cet incident peut être fermé
}

// ✅ APRÈS - Extraire en méthode
if ($this->canCloseIncident($incident, $current_user)) {
    // Cet incident peut être fermé
}

private function canCloseIncident($incident, $engineer_id) {
    return $incident['statut'] !== 'clôturé'
        && $incident['assigned_to'] === $engineer_id
        && !$this->hasPendingTransfer($incident['id']);
}
```

### Exemple 3: Gestion des erreurs
```php
// ❌ AVANT - Pas d'erreurs
$result = $this->db->query($sql);
$data = $result->fetch();

// ✅ APRÈS - Gestion explicite
try {
    $result = $this->db->query($sql);
    if (!$result) {
        throw new Exception("Erreur base de données");
    }
    $data = $result->fetch();
    return $data;
} catch (Exception $e) {
    error_log("DB Error: " . $e->getMessage());
    return null;
}
```

---

## 🧪 Tests à implémenter

### Tests unitaires
```php
// ✅ À tester
class UserTest extends TestCase {
    public function testPasswordHashing() {
        $user = new User();
        $hashed = password_hash('test123', PASSWORD_DEFAULT);
        $this->assertTrue(password_verify('test123', $hashed));
    }
    
    public function testEmailValidation() {
        $this->assertTrue(Utils::validateEmail('test@example.com'));
        $this->assertFalse(Utils::validateEmail('invalid-email'));
    }
}
```

### Tests fonctionnels
```php
// ✅ À tester
function testClientCanCreateIncident() {
    // 1. Login
    POST /login, ['email' => 'client@test.com', 'password' => 'pass123']
    // 2. Navigate to create
    GET /create_incident
    // 3. Submit form
    POST /create_incident, [...]
    // 4. Assert appears in dashboard
    GET /client_dashboard
    assertContains('New Incident Title')
}
```

---

## 📋 Checklist avant commit

- [ ] Code formaté correctement (indentation 4 espaces)
- [ ] Noms explicites (variables, fonctions, classes)
- [ ] Pas de code commenté ou debug (var_dump, console.log)
- [ ] Fonctions < 20 lignes
- [ ] Pas de variables globales
- [ ] Gestion des erreurs present
- [ ] Validation des entrées
- [ ] SQL: Requêtes préparées utilisées
- [ ] Security: htmlspecialchars, password_hash
- [ ] Documentation: PHPDoc pour fonctions publiques
- [ ] DRY: Pas de duplication
- [ ] Tests: Scenarii courants testé
- [ ] Performance: Pas de N+1 queries
- [ ] Logs: Messages d'erreur informatifs

---

## 🚀 Amélioration continue

### Refactoring courants
1. Extraire les constantes magiques
2. Réduire les niveaux d'imbrication
3. Réduire la complexité cyclomatique
4. Appliquer DRY
5. Utiliser des design patterns
6. Ajouter des types
7. Améliorer les noms
8. Diviser les fonctions longues

### Outils d'aide
- PHP_CodeSniffer (PSR-12)
- PHPStan (analyse statique)
- PHPUnit (tests)
- Psalm (type checker)

---

## 📚 Ressources

- Clean Code (Robert Martin)
- Design Patterns (Gang of Four)
- SOLID Principles
- PSR-12 Code Style Guide
- OWASP Security Guidelines

---

**Applications dans ce projet**:
- ✅ PSR-4 Autoloading
- ✅ SOLID Principles
- ✅ DRY (Don't Repeat Yourself)
- ✅ KISS (Keep It Simple)
- ✅ Security First
- ✅ Validation des entrées
- ✅ Error Handling
- ✅ Documentation
