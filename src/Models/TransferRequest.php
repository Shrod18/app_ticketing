<?php

class TransferRequest {
    private $db;
    private $table = 'transfer_requests';

    public $id;
    public $incident_id;
    public $from_engineer_id;
    public $to_engineer_id;
    public $statut;
    public $date_demande;
    public $date_reponse;
    public $comment;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET incident_id = :incident_id, 
                      from_engineer_id = :from_engineer_id, 
                      to_engineer_id = :to_engineer_id, 
                      statut = 'pending', 
                      date_demande = NOW(),
                      comment = :comment";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':incident_id', $this->incident_id);
        $stmt->bindParam(':from_engineer_id', $this->from_engineer_id);
        $stmt->bindParam(':to_engineer_id', $this->to_engineer_id);
        $stmt->bindParam(':comment', $this->comment);

        return $stmt->execute();
    }

    public function getPendingTransfersForEngineer($engineer_id) {
        $query = "SELECT tr.*, i.titre as incident_titre, i.description as incident_description,
                  from_u.nom as from_nom, from_u.prenom as from_prenom,
                  to_u.nom as to_nom, to_u.prenom as to_prenom,
                  client_u.nom as client_nom, client_u.prenom as client_prenom
                  FROM " . $this->table . " tr
                  JOIN incidents i ON tr.incident_id = i.id
                  JOIN users from_u ON tr.from_engineer_id = from_u.id
                  JOIN users to_u ON tr.to_engineer_id = to_u.id
                  JOIN users client_u ON i.client_id = client_u.id
                  WHERE tr.to_engineer_id = :engineer_id AND tr.statut = 'pending'
                  ORDER BY tr.date_demande DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':engineer_id', $engineer_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTransferById($id) {
        $query = "SELECT tr.*, i.titre as incident_titre,
                  from_u.nom as from_nom, from_u.prenom as from_prenom,
                  to_u.nom as to_nom, to_u.prenom as to_prenom
                  FROM " . $this->table . " tr
                  JOIN incidents i ON tr.incident_id = i.id
                  JOIN users from_u ON tr.from_engineer_id = from_u.id
                  JOIN users to_u ON tr.to_engineer_id = to_u.id
                  WHERE tr.id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function accept($transfer_id) {
        $transfer = $this->getTransferById($transfer_id);
        
        if (!$transfer) {
            return false;
        }

        $query = "UPDATE " . $this->table . " 
                  SET statut = 'accepted', 
                      date_reponse = NOW()
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $transfer_id);
        
        if ($stmt->execute()) {
            // Update incident assignment
            $incident_model = new Incident($this->db);
            return $incident_model->assignTo($transfer['incident_id'], $transfer['to_engineer_id']);
        }

        return false;
    }

    public function reject($transfer_id) {
        $query = "UPDATE " . $this->table . " 
                  SET statut = 'rejected', 
                      date_reponse = NOW()
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $transfer_id);
        return $stmt->execute();
    }

    public function hasPendingTransfer($incident_id) {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE incident_id = :incident_id AND statut = 'pending'";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':incident_id', $incident_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
