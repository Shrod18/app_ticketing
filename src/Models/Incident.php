<?php

class Incident {
    private $db;
    private $table = 'incidents';

    public $id;
    public $client_id;
    public $titre;
    public $description;
    public $criticite;
    public $statut;
    public $assigned_to;
    public $date_creation;
    public $date_cloture;
    public $date_modification;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET client_id = :client_id, 
                      titre = :titre, 
                      description = :description, 
                      criticite = :criticite, 
                      statut = 'ouvert', 
                      date_creation = NOW(),
                      date_modification = NOW()";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':client_id', $this->client_id);
        $stmt->bindParam(':titre', $this->titre);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':criticite', $this->criticite);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function getIncidentsByClient($client_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE client_id = :client_id ORDER BY date_creation DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':client_id', $client_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllIncidents() {
        $query = "SELECT i.*, u.nom, u.prenom FROM " . $this->table . " i
                  LEFT JOIN users u ON i.client_id = u.id
                  ORDER BY i.date_creation DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getIncidentById($id) {
        $query = "SELECT i.*, u.nom as client_nom, u.prenom as client_prenom, u.email as client_email,
                  eng.nom as engineer_nom, eng.prenom as engineer_prenom
                  FROM " . $this->table . " i
                  LEFT JOIN users u ON i.client_id = u.id
                  LEFT JOIN users eng ON i.assigned_to = eng.id
                  WHERE i.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function assignTo($incident_id, $engineer_id) {
        $query = "UPDATE " . $this->table . " 
                  SET assigned_to = :engineer_id, 
                      statut = 'assigné',
                      date_modification = NOW()
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $incident_id);
        $stmt->bindParam(':engineer_id', $engineer_id);
        
        return $stmt->execute();
    }

    public function unassign($incident_id) {
        $query = "UPDATE " . $this->table . " 
                  SET assigned_to = NULL, 
                      statut = 'ouvert',
                      date_modification = NOW()
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $incident_id);
        
        return $stmt->execute();
    }

    public function close($incident_id) {
        $query = "UPDATE " . $this->table . " 
                  SET statut = 'clôturé',
                      date_cloture = NOW(),
                      date_modification = NOW()
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $incident_id);
        
        return $stmt->execute();
    }

    public function getIncidentsByEngineer($engineer_id, $filters = []) {
        $query = "SELECT i.*, u.nom as client_nom, u.prenom as client_prenom 
                  FROM " . $this->table . " i
                  LEFT JOIN users u ON i.client_id = u.id
                  WHERE i.assigned_to = :engineer_id";

        if (isset($filters['open_only']) && $filters['open_only']) {
            $query .= " AND i.statut != 'clôturé'";
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= " AND (i.titre LIKE :search OR i.description LIKE :search)";
        }

        $query .= " ORDER BY i.date_creation DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':engineer_id', $engineer_id);
        
        if (isset($filters['search']) && !empty($filters['search'])) {
            $stmt->bindParam(':search', $search);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllIncidentsFiltered($filters = []) {
        $query = "SELECT i.*, u.nom as client_nom, u.prenom as client_prenom, 
                  eng.nom as engineer_nom, eng.prenom as engineer_prenom
                  FROM " . $this->table . " i
                  LEFT JOIN users u ON i.client_id = u.id
                  LEFT JOIN users eng ON i.assigned_to = eng.id
                  WHERE 1=1";

        if (isset($filters['my_incidents']) && $filters['my_incidents']) {
            $query .= " AND i.assigned_to = :engineer_id";
        }

        if (isset($filters['open_only']) && $filters['open_only']) {
            $query .= " AND i.statut != 'clôturé'";
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= " AND (i.titre LIKE :search OR i.description LIKE :search)";
        }

        $query .= " ORDER BY i.date_creation DESC";

        $stmt = $this->db->prepare($query);
        
        if (isset($filters['my_incidents']) && $filters['my_incidents']) {
            $stmt->bindParam(':engineer_id', $filters['engineer_id']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $stmt->bindParam(':search', $search);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function isAssignedToEngineer($incident_id, $engineer_id) {
        $query = "SELECT id FROM " . $this->table . " WHERE id = :id AND assigned_to = :engineer_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $incident_id);
        $stmt->bindParam(':engineer_id', $engineer_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function isAssigned($incident_id) {
        $query = "SELECT assigned_to FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $incident_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result && !empty($result['assigned_to']);
    }

    public function updateProgressNote($incident_id, $note) {
        $query = "UPDATE " . $this->table . " 
                  SET progress_note = :note, 
                      date_modification = NOW()
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':note', $note);
        $stmt->bindParam(':id', $incident_id);
        return $stmt->execute();
    }
}
?>
