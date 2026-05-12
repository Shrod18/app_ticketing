# 📋 EXEMPLES DE REQUÊTES SQL

## Requêtes Courantes

### 1. Lister les incidents non fermés
```sql
SELECT i.id, i.titre, i.criticite, i.statut, 
       u.prenom, u.nom
FROM incidents i
LEFT JOIN users u ON i.client_id = u.id
WHERE i.statut != 'clôturé'
ORDER BY i.date_creation DESC;
```

### 2. Incidents assignés à un ingénieur
```sql
SELECT i.id, i.titre, i.criticite, i.statut,
       client.prenom as client_prenom, client.nom as client_nom
FROM incidents i
JOIN users client ON i.client_id = client.id
WHERE i.assigned_to = ? AND i.statut != 'clôturé';
```

### 3. Demandes de transfert en attente
```sql
SELECT tr.id, tr.incident_id, tr.from_engineer_id, tr.to_engineer_id,
       i.titre as incident_titre,
       from_eng.prenom as from_prenom, from_eng.nom as from_nom,
       to_eng.prenom as to_prenom, to_eng.nom as to_nom
FROM transfer_requests tr
JOIN incidents i ON tr.incident_id = i.id
JOIN users from_eng ON tr.from_engineer_id = from_eng.id
JOIN users to_eng ON tr.to_engineer_id = to_eng.id
WHERE tr.to_engineer_id = ? AND tr.statut = 'pending'
ORDER BY tr.date_demande DESC;
```

### 4. Statistiques par ingénieur
```sql
SELECT 
    u.prenom, u.nom,
    COUNT(CASE WHEN i.statut = 'ouvert' THEN 1 END) as incidents_ouverts,
    COUNT(CASE WHEN i.statut = 'assigné' THEN 1 END) as incidents_assignes,
    COUNT(CASE WHEN i.statut = 'clôturé' THEN 1 END) as incidents_clotures
FROM users u
LEFT JOIN incidents i ON u.id = i.assigned_to
WHERE u.role = 'ingenieur'
GROUP BY u.id;
```

### 5. Incidents par criticité
```sql
SELECT criticite, COUNT(*) as total
FROM incidents
GROUP BY criticite
ORDER BY 
    CASE criticite
        WHEN 'critique' THEN 1
        WHEN 'haute' THEN 2
        WHEN 'normale' THEN 3
        WHEN 'basse' THEN 4
    END;
```

### 6. Fichiers d'un incident
```sql
SELECT * FROM incident_files
WHERE incident_id = ?
ORDER BY date_ajout DESC;
```

### 7. Transferts acceptés
```sql
SELECT tr.id, tr.incident_id, tr.date_demande, tr.date_reponse,
       i.titre,
       from_eng.prenom as from_prenom, from_eng.nom as from_nom,
       to_eng.prenom as to_prenom, to_eng.nom as to_nom
FROM transfer_requests tr
JOIN incidents i ON tr.incident_id = i.id
JOIN users from_eng ON tr.from_engineer_id = from_eng.id
JOIN users to_eng ON tr.to_engineer_id = to_eng.id
WHERE tr.statut = 'accepted'
ORDER BY tr.date_reponse DESC;
```

### 8. Incidents en attente depuis longtemps
```sql
SELECT i.id, i.titre, DATEDIFF(NOW(), i.date_creation) as jours_attente,
       u.prenom, u.nom, i.criticite
FROM incidents i
JOIN users u ON i.client_id = u.id
WHERE i.statut = 'ouvert'
  AND DATEDIFF(NOW(), i.date_creation) > 7
ORDER BY i.date_creation ASC;
```

## Opérations de Maintenance

### Nettoyer les demandes de transfert anciennes
```sql
DELETE FROM transfer_requests
WHERE statut = 'rejected' AND DATEDIFF(NOW(), date_reponse) > 30;
```

### Compter les utilisateurs
```sql
SELECT 
    role,
    COUNT(*) as total
FROM users
GROUP BY role;
```

### Vérifier l'intégrité des données
```sql
-- Incidents avec client inexistant
SELECT i.id FROM incidents i
LEFT JOIN users u ON i.client_id = u.id
WHERE u.id IS NULL;

-- Assignations avec ingénieur inexistant
SELECT i.id FROM incidents i
LEFT JOIN users u ON i.assigned_to = u.id
WHERE i.assigned_to IS NOT NULL AND u.id IS NULL;
```

### Réassigner les incidents d'un ingénieur qui part
```sql
UPDATE incidents
SET assigned_to = NULL, statut = 'ouvert'
WHERE assigned_to = ? 
  AND statut != 'clôturé';
```

---

Utilisez toujours des requêtes préparées (PDO) avec des paramètres liés pour éviter l'injection SQL!
