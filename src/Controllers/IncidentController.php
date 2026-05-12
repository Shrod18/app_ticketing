<?php

class IncidentController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Cette méthode gère la création d'un incident par un client
    public function createIncident() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $criticite = trim($_POST['criticite'] ?? 'normale');

            $errors = [];

            if (empty($titre)) $errors[] = "Le titre est requis";
            if (empty($description)) $errors[] = "La description est requise";
            if (!in_array($criticite, ['basse', 'normale', 'haute', 'critique'])) {
                $criticite = 'normale';
            }

            if (empty($errors)) {
                $incident_model = new Incident($this->db);
                $incident_model->client_id = $_SESSION['user_id'];
                $incident_model->titre = $titre;
                $incident_model->description = $description;
                $incident_model->criticite = $criticite;

                $incident_id = $incident_model->create();

                if ($incident_id) {
                    // Gérer les fichiers joints
                    $this->handleFileUploads($incident_id);

                    $_SESSION['success'] = "Incident créé avec succès";
                    header('Location: ' . BASE_URL . 'index.php?page=client_dashboard');
                    exit;
                } else {
                    $errors[] = "Une erreur est survenue lors de la création de l'incident";
                }
            }

            return ['errors' => $errors];
        }

        return ['errors' => []];
    }

    // Cette méthode gère les fichiers joints lors de la création d'un incident
    public function handleFileUploads($incident_id) {
        if (empty($_FILES['files']['name'][0])) {
            return true;
        }

        $file_model = new IncidentFile($this->db);
        $upload_dir = dirname(__DIR__) . '/../uploads/';

        // Créer le dossier s'il n'existe pas
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'text/plain', 
                         'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                         'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        $max_file_size = 5 * 1024 * 1024; // 5 MB

        foreach ($_FILES['files']['name'] as $key => $filename) {
            if (empty($filename)) continue;

            $file_tmp = $_FILES['files']['tmp_name'][$key];
            $file_type = $_FILES['files']['type'][$key];
            $file_size = $_FILES['files']['size'][$key];
            $file_error = $_FILES['files']['error'][$key];

            // Vérifications
            if ($file_error !== UPLOAD_ERR_OK) {
                continue;
            }

            if (!in_array($file_type, $allowed_types)) {
                continue;
            }

            if ($file_size > $max_file_size) {
                continue;
            }

            // Générer un nom de fichier unique et sécurisé
            $safe_filename = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($filename));
            $unique_name = time() . '_' . bin2hex(random_bytes(6)) . '_' . $safe_filename;
            $file_path = $upload_dir . $unique_name;

            if (move_uploaded_file($file_tmp, $file_path)) {
                $file_model = new IncidentFile($this->db);
                $file_model->incident_id = $incident_id;
                $file_model->nom = $filename;
                $file_model->type = $file_type;
                $file_model->url_telechargement = 'uploads/' . $unique_name;
                $file_model->size = $file_size;
                $file_model->create();
            }
        }

        return true;
    }

    // Cette méthode récupère les incidents d'un client pour son tableau de bord
    public function getClientIncidents() {
        $incident_model = new Incident($this->db);
        return $incident_model->getIncidentsByClient($_SESSION['user_id']);
    }

    // Cette méthode récupère les détails d'un incident, y compris les fichiers joints et les demandes de transfert
    public function getIncidentDetail($incident_id) {
        $incident_model = new Incident($this->db);
        $incident = $incident_model->getIncidentById($incident_id);

        if (!$incident) {
            return null;
        }

        // Vérifier les permissions
        if ($_SESSION['user_role'] === 'client' && $incident['client_id'] !== $_SESSION['user_id']) {
            return null;
        }

        // Récupérer les fichiers
        $file_model = new IncidentFile($this->db);
        $incident['files'] = $file_model->getFilesByIncident($incident_id);

        // Récupérer les demandes de transfert en attente
        $transfer_model = new TransferRequest($this->db);
        $incident['has_pending_transfer'] = $transfer_model->hasPendingTransfer($incident_id);

        return $incident;
    }

    // Cette méthode permet à un ingénieur de s'assigner un incident non assigné
    public function assignIncident($incident_id, $engineer_id) {
        if ($_SESSION['user_role'] !== 'ingenieur') {
            return false;
        }

        $incident_model = new Incident($this->db);

        // Vérifier que l'incident n'est pas déjà assigné
        if ($incident_model->isAssigned($incident_id)) {
            return false;
        }

        return $incident_model->assignTo($incident_id, $engineer_id);
    }

    // Cette méthode permet à un ingénieur de clôturer un incident, mais seulement s'il l'a assigné et qu'il a ajouté une note de suivi
    public function closeIncident($incident_id) {
        if ($_SESSION['user_role'] !== 'ingenieur') {
            return false;
        }

        $incident_model = new Incident($this->db);

        // Vérifier que l'ingénieur l'a assigné
        if (!$incident_model->isAssignedToEngineer($incident_id, $_SESSION['user_id'])) {
            return false;
        }

        $incident = $incident_model->getIncidentById($incident_id);
        if (empty(trim($incident['progress_note'] ?? ''))) {
            return false;
        }

        return $incident_model->close($incident_id);
    }

    public function requestTransfer($incident_id, $to_engineer_id) {
        if ($_SESSION['user_role'] !== 'ingenieur') {
            return false;
        }

        $incident_model = new Incident($this->db);

        // Vérifier que l'ingénieur l'a assigné
        if (!$incident_model->isAssignedToEngineer($incident_id, $_SESSION['user_id'])) {
            return false;
        }

        $transfer_model = new TransferRequest($this->db);

        // Vérifier qu'il n'y a pas déjà une demande de transfert en attente
        if ($transfer_model->hasPendingTransfer($incident_id)) {
            return false;
        }

        $transfer_model->incident_id = $incident_id;
        $transfer_model->from_engineer_id = $_SESSION['user_id'];
        $transfer_model->to_engineer_id = $to_engineer_id;
        $transfer_model->comment = trim($_POST['comment'] ?? '');

        return $transfer_model->create();
    }

    // Cette méthode récupère les incidents assignés à un ingénieur pour son tableau de bord
    public function getEngineerIncidents($filters = []) {
        $incident_model = new Incident($this->db);
        $filters['engineer_id'] = $_SESSION['user_id'];
        return $incident_model->getAllIncidentsFiltered($filters);
    }

    // Cette méthode récupère les demandes de transfert en attente pour un ingénieur
    public function getPendingTransfers() {
        $transfer_model = new TransferRequest($this->db);
        return $transfer_model->getPendingTransfersForEngineer($_SESSION['user_id']);
    }

    public function updateProgressNote($incident_id, $note) {
        if ($_SESSION['user_role'] !== 'ingenieur') {
            return false;
        }

        $incident_model = new Incident($this->db);
        if (!$incident_model->isAssignedToEngineer($incident_id, $_SESSION['user_id'])) {
            return false;
        }

        return $incident_model->updateProgressNote($incident_id, trim($note));
    }

    public function acceptTransfer($transfer_id) {
        if ($_SESSION['user_role'] !== 'ingenieur') {
            return false;
        }

        $transfer_model = new TransferRequest($this->db);
        $transfer = $transfer_model->getTransferById($transfer_id);

        if (!$transfer || $transfer['to_engineer_id'] !== $_SESSION['user_id']) {
            return false;
        }

        return $transfer_model->accept($transfer_id);
    }

    public function rejectTransfer($transfer_id) {
        if ($_SESSION['user_role'] !== 'ingenieur') {
            return false;
        }

        $transfer_model = new TransferRequest($this->db);
        $transfer = $transfer_model->getTransferById($transfer_id);

        if (!$transfer || $transfer['to_engineer_id'] !== $_SESSION['user_id']) {
            return false;
        }

        return $transfer_model->reject($transfer_id);
    }

    // Cette méthode permet à un ingénieur de télécharger un fichier joint à un incident, mais seulement s'il est assigné à l'incident ou s'il est le client qui a créé l'incident
    public function downloadFile($file_id) {
        $file_model = new IncidentFile($this->db);
        $file = $file_model->getFileById($file_id);

        if (!$file) {
            return false;
        }

        // Vérifier les permissions
        $incident = $this->getIncidentDetail($file['incident_id']);
        if (!$incident) {
            return false;
        }

        return $file;
    }
}
?>
