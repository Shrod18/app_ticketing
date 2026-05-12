-- Création de la base de données
CREATE DATABASE IF NOT EXISTS ticketing_app;
USE ticketing_app;

-- Table des utilisateurs
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('client', 'ingenieur') NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_derniere_connexion DATETIME NULL
);

-- Table des incidents
CREATE TABLE incidents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT NOT NULL,
    titre VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    progress_note TEXT NULL,
    criticite ENUM('basse', 'normale', 'haute', 'critique') DEFAULT 'normale',
    statut ENUM('ouvert', 'assigné', 'clôturé') DEFAULT 'ouvert',
    assigned_to INT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_cloture DATETIME NULL,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des fichiers joints
CREATE TABLE incident_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    incident_id INT NOT NULL,
    nom VARCHAR(255) NOT NULL,
    type VARCHAR(100) NOT NULL,
    url_telechargement VARCHAR(500) NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    size BIGINT NOT NULL,
    FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE
);

-- Table des demandes de transfert
CREATE TABLE transfer_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    incident_id INT NOT NULL,
    from_engineer_id INT NOT NULL,
    to_engineer_id INT NOT NULL,
    statut ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    date_demande DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_reponse DATETIME NULL,
    comment TEXT,
    FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE,
    FOREIGN KEY (from_engineer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (to_engineer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Ajouter des ingénieurs de test (mots de passe : password123)
INSERT INTO users (nom, prenom, email, password, role) VALUES
('Dupont', 'Alice', 'alice.dupont@company.com', '$2y$12$zg2GSQuKq3OeRdRa2hLcyuN4DOoNmUGrxX84wTROViY1.vJkBqlGS', 'ingenieur'),
('Martin', 'Bob', 'bob.martin@company.com', '$2y$12$zg2GSQuKq3OeRdRa2hLcyuN4DOoNmUGrxX84wTROViY1.vJkBqlGS', 'ingenieur'),
('Durand', 'Charlie', 'charlie.durand@company.com', '$2y$12$zg2GSQuKq3OeRdRa2hLcyuN4DOoNmUGrxX84wTROViY1.vJkBqlGS', 'ingenieur');

-- Créer les indices
CREATE INDEX idx_incidents_client ON incidents(client_id);
CREATE INDEX idx_incidents_assigned ON incidents(assigned_to);
CREATE INDEX idx_incidents_statut ON incidents(statut);
CREATE INDEX idx_files_incident ON incident_files(incident_id);
CREATE INDEX idx_transfers_incident ON transfer_requests(incident_id);
CREATE INDEX idx_transfers_engineer ON transfer_requests(to_engineer_id);
