# ✅ CHECKLIST D'UTILISATION

## 🎯 Avant de commencer

- [ ] Base de données créée et schéma importé
- [ ] `src/config/Database.php` configuré avec vos identifiants MySQL
- [ ] Dossier `uploads/` avec permissions d'écriture (chmod 777)
- [ ] Serveur web configuré et fonctionnel
- [ ] PHP 7.4+ activé
- [ ] Extension PDO MySQL disponible

---

## 👨‍💼 GUIDE CLIENT - Signaler un Incident

### Étape 1: Créer un Compte
1. Accédez à la page d'accueil
2. Cliquez sur **"Inscription"**
3. Remplissez le formulaire:
   - Nom *
   - Prénom *
   - Email * (unique)
   - Mot de passe * (min 6 caractères)
   - Confirmez le mot de passe *
4. Cliquez sur **"Créer un Compte"**
5. Redirection vers login

### Étape 2: Se Connecter
1. Sur la page login, entrez vos identifiants
2. Cliquez sur **"Se Connecter"**
3. Accès au dashboard client

### Étape 3: Signaler un Incident
1. Sur le dashboard, cliquez sur **"✚ Créer un Incident"**
2. Remplissez le formulaire:
   - **Titre** * : Brève description du problème
   - **Description** * : Détails complets
   - **Criticité** * : Basse/Normale/Haute/Critique
   - **Fichiers** (optionnel) : Attachez jusqu'à plusieurs fichiers
3. Cliquez sur **"✓ Créer l'incident"**
4. Confirmation et redirection au dashboard

### Étape 4: Suivre l'Incident
1. Voyez l'incident dans votre dashboard avec statut
2. Cliquez sur **"Détails"** pour voir:
   - Description complète
   - État actuel
   - Fichiers joints
   - Ingénieur assigné (s'il y a)
3. Téléchargez les fichiers si nécessaire

---

## 👨‍💻 GUIDE INGÉNIEUR - Traiter les Incidents

### Étape 1: Se Connecter
1. Allez sur la page login
2. Entrez vos identifiants ingénieur:
   - Email: alice.dupont@company.com (ou autre)
   - Mot de passe: password123
3. Cliquez sur **"Se Connecter"**
4. Accès au dashboard ingénieur

### Étape 2: Vérifier les Demandes de Transfert
1. En haut du dashboard: **"📩 Demandes de Transfert en Attente"**
2. Pour chaque demande:
   - Visualisez l'incident
   - Client information
   - Demandeur
3. Cliquez sur:
   - **"✓ Accepter"** : Vous prendre en charge
   - **"✗ Refuser"** : Non disponible / Pas le temps

### Étape 3: Consulter et Filtrer les Incidents
1. **Filtres disponibles:**
   - **"Rechercher un incident..."** : Par titre/description
   - **"Mes incidents"** : Uniquement vos assignations
   - **"Non fermés"** : Ignorer les clôturés
   - Combinez les filtres!
2. Cliquez sur **"🔍 Filtrer"** ou **"Réinitialiser"**

### Étape 4: S'assigner un Incident
1. Sur le dashboard, cherchez un incident
2. Cliquez sur **"Voir"** pour voir les détails
3. Cliquez sur **"👤 M'assigner ce ticket"**
4. L'incident vous est assigné
5. Statut: "assigné"

### Étape 5: Traiter l'Incident
1. Lisez la description et les détails
2. Consultez les fichiers joints (téléchargez si besoin)
3. Effectuez vos actions de résolution

### Étape 6: Transférer le Ticket (Optionnel)
1. Cliquez sur **"↔️ Demander un transfert"**
2. Modal s'ouvre
3. Sélectionnez un collègue ingénieur
4. Ajoutez un commentaire (optionnel)
5. Cliquez sur **"Envoyer la demande"**
6. Le collègue reçoit la demande
7. En attente de son acceptation/refus

### Étape 7: Clôturer l'Incident
1. Vérifiez que l'incident est résolu
2. Cliquez sur **"✓ Clôturer l'incident"**
3. Confirmez dans la popup
4. Incident marqué "clôturé"
5. Date de clôture enregistrée

### Étape 8: Gérer les Transferts Reçus
1. Consultez la section **"📩 Demandes"**
2. Évaluez votre charge de travail
3. **Accepter**: L'incident devient vôtre
4. **Refuser**: Reste chez le demandeur

---

## 🔍 CAS D'USAGE COURANTS

### Cas 1: Client signale un problème critique
```
1. Client crée incident (criticité: critique)
2. Ingénieur filtre: [Non fermés] + recherche
3. Ingénieur s'assigne
4. Ingénieur traite d'urgence
5. Ingénieur clôture
6. Client voit "clôturé" dans son dashboard
```

### Cas 2: Ingénieur débordé
```
1. Ingénieur reçoit 2ème ticket
2. Charge trop importante
3. Demande transfert à collègue
4. Collègue accepte
5. Ticket réassigné
6. Ingénieur travaille sur 1er ticket
```

### Cas 3: Ingénieur en congé
```
1. Ses tickets sont transférés manuellement
2. Pas de transfert auto, doit être fait par manager
3. Ou: Les clients doivent relancer
```

---

## ⚠️ LIMITATIONS ACTUELLES

- Pas de comments sur les incidents
- Pas de notifications par email
- Pas de SLA/deadlines
- Pas de priorités
- Pas de multi-assignation
- Pas de statistiques
- Pas d'export PDF/CSV

---

## 🆘 DÉPANNAGE UTILISATEUR

### "Je ne peux pas créer de compte"
- Vérifier que l'email n'existe pas
- Vérifier le mot de passe (min 6 caractères)
- Les ingénieurs ne peuvent pas créer via l'interface

### "Je ne vois pas mes incidents"
- Vérifier que vous êtes client (pas ingénieur)
- Ingénieurs voient TOUS les incidents
- Clients voient leurs propres incidents

### "Je ne peux pas m'assigner un ticket"
- Vérifier que c'est déjà assigné
- Si oui, c'est normal (protégé)

### "Mon transfert reste en attente"
- Le collègue n'a pas répondu
- Attendez sa réponse
- Vous pouvez faire une nouvelle demande à un autre

### "Je ne peux pas télécharger un fichier"
- Vérifier que le fichier existe toujours
- Vérifier les permissions du dossier uploads/
- Navigateur peut bloquer (popup blocker)

---

## 📞 SUPPORT

Consultez:
- README.md pour architecture générale
- INSTALLATION.md pour configuration
- SQL_EXAMPLES.md pour requêtes
- Code source bien commenté

---

**Dernière mise à jour**: 2024
**Version**: 1.0
