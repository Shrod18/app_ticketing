<?php

class User {
    private $db;
    private $table = 'users';

    public $id;
    public $nom;
    public $prenom;
    public $email;
    public $password;
    public $role;
    public $date_inscription;
    public $date_derniere_connexion;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET nom = :nom, 
                      prenom = :prenom, 
                      email = :email, 
                      password = :password, 
                      role = :role, 
                      date_inscription = NOW()";

        $stmt = $this->db->prepare($query);

        // Hash password
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind values
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $this->role);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getUserByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getAllEngineers() {
        $query = "SELECT * FROM " . $this->table . " WHERE role = 'ingenieur' ORDER BY prenom ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateLastLogin($id) {
        $query = "UPDATE " . $this->table . " SET date_derniere_connexion = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
