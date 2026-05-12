<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'ticketing_app';
    private $db_user = 'root';
    private $db_pass = '';
    private $connection;

    public function connect() {
        $this->connection = null;

        try {
            $this->connection = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->db_user,
                $this->db_pass
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->ensureSchema($this->connection);
        } catch(PDOException $e) {
            echo 'Erreur de connexion: ' . $e->getMessage();
        }

        return $this->connection;
    }

    private function ensureSchema(PDO $conn) {
        try {
            $result = $conn->query("SHOW COLUMNS FROM incidents LIKE 'progress_note'")->fetch();
            if (!$result) {
                $conn->exec("ALTER TABLE incidents ADD COLUMN progress_note TEXT NULL AFTER description");
            }
        } catch (PDOException $e) {
            // Si la table n'existe pas encore ou si la colonne est déjà présente, on ignore.
        }
    }
}
?>
