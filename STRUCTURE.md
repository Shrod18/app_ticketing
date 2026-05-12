# 📂 STRUCTURE COMPLÈTE DU PROJET

```
app-ticketing/
│
├── 📄 README.md                          Documentation générale
├── 📄 INSTALLATION.md                    Guide d'installation complet
├── 📄 GUIDE_UTILISATEUR.md               Guide d'utilisation client/ingénieur
├── 📄 RESUME.md                          Résumé du projet
├── 📄 SQL_EXAMPLES.md                    Exemples de requêtes SQL
├── 📄 database.sql                       Schéma BD et données initiales
├── 📄 CHANGELOG.md                       Historique des modifications
│
├── 📁 public/                            Dossier public (racine web)
│   │
│   ├── 📄 index.php                      Point d'entrée - Routeur principal
│   ├── 📄 .htaccess                      Règles réécriture Apache
│   │
│   ├── 📁 css/
│   │   └── 📄 style.css                  Styles globaux (responsive)
│   │
│   ├── 📁 js/
│   │   └── 📄 main.js                    JavaScript côté client
│   │
│   └── 📁 uploads/                       Fichiers uploadés par clients
│       └── (fichiers joints)
│
├── 📁 src/                               Code source backend
│   │
│   ├── 📁 config/                        Configuration et utilitaires
│   │   ├── 📄 Database.php               Classe connexion PDO MySQL
│   │   └── 📄 Utils.php                  Utilitaires communs
│   │
│   ├── 📁 Models/                        Modèles de données
│   │   ├── 📄 User.php                   Gestion des utilisateurs
│   │   ├── 📄 Incident.php               Gestion des incidents
│   │   ├── 📄 TransferRequest.php        Gestion des transferts
│   │   └── 📄 IncidentFile.php           Gestion des fichiers joints
│   │
│   └── 📁 Controllers/                   Contrôleurs métier
│       ├── 📄 AuthController.php         Login/Logout/Register
│       └── 📄 IncidentController.php     Création, assignation, transfert
│
└── 📁 templates/                         Templates HTML
    ├── 📄 home.php                       Accueil (sans auth)
    ├── 📄 login.php                      Page de connexion
    ├── 📄 register.php                   Page d'inscription
    │
    ├── 📄 client_dashboard.php           Dashboard client (liste incidents)
    ├── 📄 create_incident.php            Création d'incident (client)
    ├── 📄 incident_detail.php            Détail incident (client)
    │
    ├── 📄 engineer_dashboard.php         Dashboard ingénieur (tous incidents)
    └── 📄 engineer_incident_detail.php   Détail + actions ingénieur
```

## 📊 Flux de fichiers

### Requête utilisateur
```
HTTP Request
    ↓
public/index.php (Routeur)
    ↓
Sélection du contrôleur
    ↓
AuthController.php OU IncidentController.php
    ↓
Appel au Model (User, Incident, etc.)
    ↓
Database.php (Requête SQL via PDO)
    ↓
MySQL Database
    ↓
Résultat → Rendu template
    ↓
HTML + CSS + JS
    ↓
HTTP Response → Navigateur
```

## 🔄 Architecture MVC Simplifiée

```
┌─────────────────────────────────────────┐
│           Couche Présentation            │
│     templates/ (HTML + CSS + JS)       │
└─────────────────────────────────────────┘
              ↓ Interaction
┌─────────────────────────────────────────┐
│      Couche Métier (Contrôleurs)        │
│  AuthController, IncidentController     │
└─────────────────────────────────────────┘
              ↓ Manipulation
┌─────────────────────────────────────────┐
│      Couche Modèle (Données)            │
│  User, Incident, TransferRequest, etc.  │
└─────────────────────────────────────────┘
              ↓ Persistance
┌─────────────────────────────────────────┐
│      Couche Base de Données             │
│         MySQL / MariaDB                 │
└─────────────────────────────────────────┘
```

## 📈 Chemins des pages principales

### Pages publiques
- `/` → `home.php`
- `?page=login` → `login.php`
- `?page=register` → `register.php`

### Pages client
- `?page=client_dashboard` → `client_dashboard.php` (liste)
- `?page=create_incident` → `create_incident.php` (création)
- `?page=incident_detail&id=X` → `incident_detail.php` (détail)

### Pages ingénieur
- `?page=engineer_dashboard` → `engineer_dashboard.php` (liste avec filtres)
- `?page=engineer_incident_detail&id=X` → `engineer_incident_detail.php` (détail + actions)

### Actions POST
- `?page=assign_incident` → Assigner
- `?page=close_incident` → Clôturer
- `?page=request_transfer` → Demander transfert
- `?page=accept_transfer` → Accepter transfert
- `?page=reject_transfer` → Refuser transfert
- `?page=logout` → Déconnexion
- `?page=download_file&id=X` → Télécharger fichier

## 🗄️ Matrice des droits d'accès

| Fonctionnalité | Client | Ingénieur | Admin |
|---|---|---|---|
| Voir ses incidents | ✓ | - | - |
| Voir tous incidents | - | ✓ | ✓ |
| Créer incident | ✓ | - | - |
| S'assigner | - | ✓ | ✓ |
| Transfert | - | ✓ | ✓ |
| Clôturer | - | ✓ | ✓ |
| Télécharger fichiers | ✓ | ✓ | ✓ |
| Éditer incident | ✗ | ✗ | ✗ |
| Supprimer incident | ✗ | ✗ | ✗ |

## 💾 Tailles estimées

| Fichier | Lignes | Taille |
|---------|--------|---------|
| index.php | 150 | 6 KB |
| AuthController.php | 90 | 3.5 KB |
| IncidentController.php | 180 | 7 KB |
| User.php | 80 | 3 KB |
| Incident.php | 120 | 4.5 KB |
| style.css | 600 | 25 KB |
| **Total backend** | **~500** | **~20 KB** |
| **Total frontend** | **~1500** | **~50 KB** |
| database.sql | 100 | 4 KB |
| Documentation | ~1000 | ~50 KB |

## 🔐 Principaux contrôles de sécurité

```php
// Dans chaque template
<?php if ($_SESSION['user_role'] !== 'client') exit; ?>

// Requêtes SQL
$stmt = $this->db->prepare("SELECT * FROM incidents WHERE id = :id");
$stmt->bindParam(':id', $id);

// Échappement HTML
<?php echo htmlspecialchars($variable); ?>

// Hash passwords
password_hash($password, PASSWORD_DEFAULT)
password_verify($input, $hash)
```

## 📋 Checklist de développement complète

### Phase 1 - Base (✅ FAIT)
- [x] Structure MVC
- [x] Connexion BD
- [x] Models CRUD
- [x] Authentification
- [x] Templates bootstrap

### Phase 2 - Fonctionnalités clients (✅ FAIT)
- [x] Création incident
- [x] Upload fichiers
- [x] Dashboard personnel
- [x] Détails incidents

### Phase 3 - Fonctionnalités ingénieurs (✅ FAIT)
- [x] Vue tous incidents
- [x] Assignation
- [x] Transferts
- [x] Clôture
- [x] Filtres

### Phase 4 - UX/Design (✅ FAIT)
- [x] CSS responsive
- [x] Modales
- [x] Alerts
- [x] Navigation

### Phase 5 - Documentation (✅ FAIT)
- [x] README
- [x] Installation
- [x] Guide utilisateur
- [x] Exemples SQL

## 🚀 Prochaines étapes (Optionnel)

1. **Notifications** - Email alerts
2. **Comments** - Discussion sur tickets
3. **SLA** - Deadlines/escalade
4. **Stats** - Dashboards analytiques
5. **API** - REST endpoints
6. **Export** - PDF/CSV
7. **Audit** - Logs complets
8. **2FA** - Authentification 2 facteurs

---

**Projet**: Application de Ticketing  
**Statut**: ✅ Version 1.0 complète  
**Date**: 2024
