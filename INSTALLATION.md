# GUIDE D'INSTALLATION - Application de Ticketing

## ✅ Vérification des prérequis

- [ ] PHP 7.4+
- [ ] MySQL 5.7+ ou MariaDB
- [ ] Serveur web (Apache/Nginx)
- [ ] Extension PHP PDO pour MySQL

## 📋 Étapes d'installation

### 1️⃣ Préparation de la base de données

```bash
# Connectez-vous à MySQL
mysql -u root -p

# Créez la base de données
CREATE DATABASE IF NOT EXISTS ticketing_app;
USE ticketing_app;

# Importez le schéma
source /chemin/vers/database.sql;
```

Ou importez le fichier `database.sql` via phpMyAdmin

### 2️⃣ Configuration PHP

Éditez `src/config/Database.php`:

```php
private $host = 'localhost';        // Serveur MySQL
private $db_name = 'ticketing_app'; // Nom DB
private $db_user = 'root';          // Utilisateur MySQL
private $db_pass = '';              // Mot de passe MySQL
```

### 3️⃣ Permissions des dossiers

```bash
# Le dossier uploads doit être accessible en écriture
chmod 755 app-ticketing
chmod 777 app-ticketing/uploads
chmod 755 app-ticketing/public
chmod 755 app-ticketing/templates
```

### 4️⃣ Configuration Apache

Assurez-vous que `mod_rewrite` est activé:

```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Le fichier `.htaccess` dans `public/` gère les redirections.

### 5️⃣ Accès à l'application

Ouvrez votre navigateur:
- **URL locale**: `http://localhost/app-ticketing/public/`
- **URL IP**: `http://192.168.x.x/app-ticketing/public/`

## 🧪 Test

### Comptes disponibles

**Ingénieurs** (déjà créés):
```
Email: alice.dupont@company.com
Mot de passe: password123
Rôle: Ingénieur

Email: bob.martin@company.com
Mot de passe: password123
Rôle: Ingénieur

Email: charlie.durand@company.com
Mot de passe: password123
Rôle: Ingénieur
```

### Scénario de test client

1. Allez sur la page d'accueil
2. Cliquez sur "Inscription"
3. Créez un compte avec des données fictives
4. Connexion avec ce compte
5. Créez un incident avec titre, description, criticité
6. Attachez un fichier (optionnel)
7. Visualisez l'incident dans votre dashboard

### Scénario de test ingénieur

1. Accédez à la page de connexion
2. Utilisez les différents comptes d'ingénieur
3. Consultez la liste des incidents
4. Assignez-vous un incident
5. Demandez un transfert à un collègue
6. Acceptez/Refusez un transfert reçu
7. Clôturez un incident

## 🐛 Dépannage

### Erreur: "PDO driver not enabled for MySQL"
- Vérifiez que l'extension `php_pdo_mysql` est activée dans `php.ini`
- Redémarrez le serveur web

### Erreur: "Access denied for user 'root'@'localhost'"
- Vérifiez les identifiants MySQL dans `Database.php`
- Assurez-vous que l'utilisateur a les droits sur la base de données

### Les fichiers uploadés ne s'enregistrent pas
- Vérifiez les permissions du dossier `uploads/` (chmod 777)
- Vérifiez que le dossier existe

### Le CSS/JS ne charge pas
- Vérifiez le chemin BASE_URL dans `index.php`
- Appelez ce chemin depuis le navigateur

### Les formulaires POST ne fonctionnent pas
- Assurez-vous que `mod_rewrite` est activé (Apache)
- Vérifiez le fichier `.htaccess` dans `public/`

## 📞 Support

En cas de problème:
1. Vérifiez les logs du serveur web (Apache/Nginx)
2. Consultez les erreurs PHP dans les logs
3. Vérifiez la connexion à la base de données
4. Testez les permissions des fichiers

---

**Installation Complète!** ✨

Votre application est maintenant prête à être utilisée.
