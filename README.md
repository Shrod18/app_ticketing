# Application de Ticketing en PHP/JavaScript

## 📋 Description

Application web de gestion d'incidents permettant aux clients de signaler des problèmes et aux ingénieurs de les traiter. Développée en PHP et JavaScript avec une base de données MySQL.

## ✨ Fonctionnalités

### Pour les Clients
- ✓ Créer un compte simplement
- ✓ Signaler des incidents avec titre, description, niveau de criticité
- ✓ Joindre des fichiers (PDF, images, documents)
- ✓ Consulter la liste de leurs incidents
- ✓ Suivre le statut en temps réel
- ✓ Voir les détails et l'historique

### Pour les Ingénieurs
- ✓ Consulter tous les incidents (déjà enregistrés, authentification requise)
- ✓ S'assigner un incident d'un clic
- ✓ Clôturer les incidents traités
- ✓ Demander le transfert d'un ticket à un collègue
- ✓ Accepter/Refuser les demandes de transfert
- ✓ Filtrer les incidents (mes incidents, non fermés, recherche)
- ✓ Télécharger les fichiers joints

## 🛠️ Installation

### 1. Configuration initiale

#### Prérequis
- PHP 7.4+ avec support PDO
- MySQL 5.7+ ou MariaDB
- Serveur web (Apache avec mod_rewrite ou Nginx)

#### Dossier uploads
```bash
# Le dossier uploads doit avoir les permissions d'écriture
chmod 777 uploads/
```

#### Base de données
1. Créer une base de données MySQL :
```bash
mysql -u root -p < database.sql
```

2. Ou manuellement via phpMyAdmin:
   - Créer la base de données `ticketing_app`
   - Importer le fichier `database.sql`

### 2. Configuration de l'application

Éditer `src/config/Database.php` avec vos identifiants :

```php
private $host = 'localhost';
private $db_name = 'ticketing_app';
private $db_user = 'root';         // Votre utilisateur MySQL
private $db_pass = '';             // Votre mot de passe MySQL
```

### 3. Configurez votre serveur web

#### Apache (.htaccess)
Assurez-vous que `mod_rewrite` est activé et créez un `.htaccess` à la racine :

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

#### Nginx
Configurez le bloc server :

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 🚀 Utilisation

### Accès à l'application

1. Ouvrez votre navigateur et allez à: `http://localhost/app-ticketing/public/`
2. Choisissez de créer un compte (clients) ou de vous connecter (ingénieurs)

### Comptes de test

Des ingénieurs de test sont pré-créés:

| Email | Rôle | Mot de passe |
|-------|------|--------------|
| alice.dupont@company.com | Ingénieur | password123 |
| bob.martin@company.com | Ingénieur | password123 |
| charlie.durand@company.com | Ingénieur | password123 |

### Flux d'utilisation Client

1. **Inscription** → Créer un compte client
2. **Création d'incident** → Remplir le formulaire avec détails
3. **Suivi** → Consulter la liste et les détails des incidents
4. **Téléchargement** → Télécharger les fichiers attachés

### Flux d'utilisation Ingénieur

1. **Connexion** → Se connecter avec identifiants
2. **Dashboard** → Voir tous les incidents
3. **Assigner** → Cliquer sur "Voir" puis "M'assigner ce ticket"
4. **Traiter** → Examiner les détails et fichiers
5. **Transférer** (optionnel) → Demander un transfert à un collègue
6. **Clôturer** → Marquer comme résolu

## 📁 Structure du projet

```
app-ticketing/
├── public/
│   ├── index.php              # Point d'entrée principal
│   ├── css/
│   │   └── style.css          # Styles CSS
│   ├── js/
│   │   └── main.js            # JavaScript côté client
│   └── uploads/               # Fichiers uploadés
├── src/
│   ├── config/
│   │   └── Database.php       # Connexion PDO
│   ├── Models/
│   │   ├── User.php
│   │   ├── Incident.php
│   │   ├── TransferRequest.php
│   │   └── IncidentFile.php
│   └── Controllers/
│       ├── AuthController.php
│       └── IncidentController.php
├── templates/
│   ├── home.php
│   ├── login.php
│   ├── register.php
│   ├── client_dashboard.php
│   ├── create_incident.php
│   ├── incident_detail.php
│   ├── engineer_dashboard.php
│   └── engineer_incident_detail.php
├── database.sql               # Schéma de base de données
└── README.md                  # Cette documentation
```

## 🔐 Sécurité

### Implémentée
- ✓ Hachage des mots de passe avec `password_hash()` (bcrypt)
- ✓ Vérification avec `password_verify()`
- ✓ Sessions PHP pour l'authentification
- ✓ Protection contre l'injection SQL avec requêtes préparées (PDO)
- ✓ Échappement des données avec `htmlspecialchars()`
- ✓ Validation des fichiers (MIME type, taille)
- ✓ Contrôle d'accès basé sur les rôles (RBAC)

### À améliorer pour la production
- [ ] Ajouter les tokens CSRF
- [ ] Implémenter HTTPS/TLS
- [ ] Ajouter un système de rate limiting
- [ ] Implémenter les logs d'audit complets
- [ ] Chiffrement des données sensibles
- [ ] Politique de sécurité du contenu (CSP)
- [ ] Certificat SSL/TLS

## 📊 Modèle de données

### Users
- id, nom, prenom, email, password, role, date_inscription, date_derniere_connexion

### Incidents  
- id, client_id, titre, description, criticite, statut, assigned_to, date_creation, date_clôture

### Transfer Requests
- id, incident_id, from_engineer_id, to_engineer_id, statut, date_demande, date_reponse, comment

### Incident Files
- id, incident_id, nom, type, url_telechargement, date_ajout, size

## 🎨 Interface utilisateur

- Design responsive (mobile, tablette, desktop)
- Thème clair avec couleurs professionnelles
- Indicateurs visuels clairs (badges, statuts)
- Navigation intuitive
- Messages de confirmation/erreur

## 📈 Améliorations possibles

1. **Notifications par email** → Alerter lors des transferts
2. **Commentaires** → Ajouter des commentaires sur les incidents
3. **Historique** → Tracker toutes les modifications
4. **Statistiques** → Tableaux de bord analytiques
5. **Priorités** → Système de files d'attente
6. **SLA** → Suivi des délais de résolution
7. **API REST** → Intégrations tierces
8. **Pagination** → Gestion des longs listes
9. **Export** → PDF, CSV des incidents
10. **Recherche avancée** → Filtres plus complexes

## 👨‍💻 Support et contribution

Pour des questions ou pour contribuer:
1. Vérifiez la documentation
2. Consultez les commentaires du code
3. Testez les fonctionnalités de base

## 📝 Licence

Ce projet est fourni à titre d'exemple éducatif.

---

**Version**: 1.0  
**Dernière mise à jour**: 2024
