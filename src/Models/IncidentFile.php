<?php

class IncidentFile {
    private $db;
    private $table = 'incident_files';

    public $id;
    public $incident_id;
    public $nom;
    public $type;
    public $url_telechargement;
    public $date_ajout;
    public $size;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET incident_id = :incident_id, 
                      nom = :nom, 
                      type = :type, 
                      url_telechargement = :url_telechargement, 
                      date_ajout = NOW(),
                      size = :size";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':incident_id', $this->incident_id);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':url_telechargement', $this->url_telechargement);
        $stmt->bindParam(':size', $this->size);

        return $stmt->execute();
    }

    public function getFilesByIncident($incident_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE incident_id = :incident_id ORDER BY date_ajout DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':incident_id', $incident_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFileById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
