<?php
session_start();

// Configuration
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
define('BASE_URL', $protocol . '://' . $host . ($scriptDir !== '/' ? $scriptDir : '') . '/');
define('ROOT_DIR', dirname(__DIR__) . '/');

// Autoloader
spl_autoload_register(function ($class) {
    $file = ROOT_DIR . 'src/Models/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }

    $file = ROOT_DIR . 'src/Controllers/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }

    $file = ROOT_DIR . 'src/config/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }
});

// Connexion à la base de données
$database = new Database();
$db = $database->connect();

// Routage
$page = $_GET['page'] ?? 'home';

// Vérifier l'authentification
$public_pages = ['login', 'register', 'home'];
$is_public = in_array($page, $public_pages);

if (!$is_public && !isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'index.php?page=login');
    exit;
}

// Traiter les actions
$action = $_GET['action'] ?? null;

try {
    switch ($page) {
        case 'home':
            if (isset($_SESSION['user_id'])) {
                if ($_SESSION['user_role'] === 'ingenieur') {
                    header('Location: ' . BASE_URL . 'index.php?page=engineer_dashboard');
                } else {
                    header('Location: ' . BASE_URL . 'index.php?page=client_dashboard');
                }
                exit;
            }
            require ROOT_DIR . 'templates/home.php';
            break;

        case 'register':
            $auth_controller = new AuthController($db);
            $result = $auth_controller->register();
            require ROOT_DIR . 'templates/register.php';
            break;

        case 'login':
            $auth_controller = new AuthController($db);
            $result = $auth_controller->login();
            require ROOT_DIR . 'templates/login.php';
            break;

        case 'logout':
            $auth_controller = new AuthController($db);
            $auth_controller->logout();
            break;

        case 'client_dashboard':
            if ($_SESSION['user_role'] !== 'client') {
                header('Location: ' . BASE_URL . 'index.php?page=engineer_dashboard');
                exit;
            }
            $incident_controller = new IncidentController($db);
            $incidents = $incident_controller->getClientIncidents();
            require ROOT_DIR . 'templates/client_dashboard.php';
            break;

        case 'create_incident':
            if ($_SESSION['user_role'] !== 'client') {
                header('Location: ' . BASE_URL . 'index.php?page=engineer_dashboard');
                exit;
            }
            $incident_controller = new IncidentController($db);
            $result = $incident_controller->createIncident();
            require ROOT_DIR . 'templates/create_incident.php';
            break;

        case 'incident_detail':
            $incident_id = $_GET['id'] ?? null;
            if (!$incident_id) {
                header('Location: ' . BASE_URL . 'index.php?page=client_dashboard');
                exit;
            }
            $incident_controller = new IncidentController($db);
            $incident = $incident_controller->getIncidentDetail($incident_id);
            if (!$incident) {
                header('Location: ' . BASE_URL . 'index.php?page=client_dashboard');
                exit;
            }
            require ROOT_DIR . 'templates/incident_detail.php';
            break;

        case 'engineer_dashboard':
            if ($_SESSION['user_role'] !== 'ingenieur') {
                header('Location: ' . BASE_URL . 'index.php?page=client_dashboard');
                exit;
            }
            $incident_controller = new IncidentController($db);
            
            $filters = [
                'my_incidents' => $_GET['my_incidents'] ?? false,
                'open_only' => $_GET['open_only'] ?? false,
                'search' => $_GET['search'] ?? ''
            ];
            
            $incidents = $incident_controller->getEngineerIncidents($filters);
            $pending_transfers = $incident_controller->getPendingTransfers();
            
            require ROOT_DIR . 'templates/engineer_dashboard.php';
            break;

        case 'engineer_incident_detail':
            if ($_SESSION['user_role'] !== 'ingenieur') {
                header('Location: ' . BASE_URL . 'index.php?page=client_dashboard');
                exit;
            }
            $incident_id = $_GET['id'] ?? null;
            if (!$incident_id) {
                header('Location: ' . BASE_URL . 'index.php?page=engineer_dashboard');
                exit;
            }
            $incident_controller = new IncidentController($db);
            $incident = $incident_controller->getIncidentDetail($incident_id);
            if (!$incident) {
                header('Location: ' . BASE_URL . 'index.php?page=engineer_dashboard');
                exit;
            }

            // Récupérer les autres ingénieurs pour le transfert
            $user_model = new User($db);
            $other_engineers = $user_model->getAllEngineers();
            $other_engineers = array_filter($other_engineers, function($e) {
                return $e['id'] !== $_SESSION['user_id'];
            });

            require ROOT_DIR . 'templates/engineer_incident_detail.php';
            break;

        case 'assign_incident':
            if ($_SESSION['user_role'] !== 'ingenieur' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('HTTP/1.0 403 Forbidden');
                exit;
            }
            $incident_id = $_POST['incident_id'] ?? null;
            $incident_controller = new IncidentController($db);
            
            if ($incident_controller->assignIncident($incident_id, $_SESSION['user_id'])) {
                $_SESSION['success'] = "Incident assigné avec succès";
            } else {
                $_SESSION['error'] = "Impossible d'assigner l'incident";
            }
            header('Location: ' . BASE_URL . 'index.php?page=engineer_incident_detail&id=' . $incident_id);
            exit;

        case 'close_incident':
            if ($_SESSION['user_role'] !== 'ingenieur' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('HTTP/1.0 403 Forbidden');
                exit;
            }
            $incident_id = $_POST['incident_id'] ?? null;
            $progress_note = $_POST['progress_note'] ?? null;
            $incident_controller = new IncidentController($db);
            
            if ($progress_note !== null) {
                $incident_controller->updateProgressNote($incident_id, $progress_note);
            }

            if ($incident_controller->closeIncident($incident_id)) {
                $_SESSION['success'] = "Incident clôturé avec succès";
            } else {
                $_SESSION['error'] = "Impossible de clôturer l'incident";
            }
            header('Location: ' . BASE_URL . 'index.php?page=engineer_incident_detail&id=' . $incident_id);
            exit;

        case 'update_progress':
            if ($_SESSION['user_role'] !== 'ingenieur' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('HTTP/1.0 403 Forbidden');
                exit;
            }
            $incident_id = $_POST['incident_id'] ?? null;
            $progress_note = $_POST['progress_note'] ?? null;
            $incident_controller = new IncidentController($db);

            if ($incident_controller->updateProgressNote($incident_id, $progress_note)) {
                $_SESSION['success'] = "Note de traitement enregistrée";
            } else {
                $_SESSION['error'] = "Impossible d'enregistrer la note de traitement";
            }
            header('Location: ' . BASE_URL . 'index.php?page=engineer_incident_detail&id=' . $incident_id);
            exit;

        case 'request_transfer':
            if ($_SESSION['user_role'] !== 'ingenieur' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('HTTP/1.0 403 Forbidden');
                exit;
            }
            $incident_id = $_POST['incident_id'] ?? null;
            $to_engineer_id = $_POST['to_engineer_id'] ?? null;
            $incident_controller = new IncidentController($db);
            
            if ($incident_controller->requestTransfer($incident_id, $to_engineer_id)) {
                $_SESSION['success'] = "Demande de transfert envoyée";
            } else {
                $_SESSION['error'] = "Impossible de demander le transfert";
            }
            header('Location: ' . BASE_URL . 'index.php?page=engineer_incident_detail&id=' . $incident_id);
            exit;

        case 'accept_transfer':
            if ($_SESSION['user_role'] !== 'ingenieur' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('HTTP/1.0 403 Forbidden');
                exit;
            }
            $transfer_id = $_POST['transfer_id'] ?? null;
            $incident_controller = new IncidentController($db);
            
            if ($incident_controller->acceptTransfer($transfer_id)) {
                $_SESSION['success'] = "Transfert accepté";
            } else {
                $_SESSION['error'] = "Impossible d'accepter le transfert";
            }
            header('Location: ' . BASE_URL . 'index.php?page=engineer_dashboard');
            exit;

        case 'reject_transfer':
            if ($_SESSION['user_role'] !== 'ingenieur' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('HTTP/1.0 403 Forbidden');
                exit;
            }
            $transfer_id = $_POST['transfer_id'] ?? null;
            $incident_controller = new IncidentController($db);
            
            if ($incident_controller->rejectTransfer($transfer_id)) {
                $_SESSION['success'] = "Transfert refusé";
            } else {
                $_SESSION['error'] = "Impossible de refuser le transfert";
            }
            header('Location: ' . BASE_URL . 'index.php?page=engineer_dashboard');
            exit;

        case 'download_file':
            $file_id = $_GET['id'] ?? null;
            $incident_controller = new IncidentController($db);
            $file = $incident_controller->downloadFile($file_id);
            
            if ($file) {
                $file_path = ROOT_DIR . $file['url_telechargement'];
                if (file_exists($file_path)) {
                    header('Content-Type: ' . $file['type']);
                    header('Content-Disposition: attachment; filename="' . $file['nom'] . '"');
                    header('Content-Length: ' . $file['size']);
                    readfile($file_path);
                    exit;
                }
            }
            
            header('HTTP/1.0 404 Not Found');
            exit;

        default:
            require ROOT_DIR . 'templates/home.php';
            break;
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Une erreur est survenue: " . $e->getMessage();
    header('Location: ' . BASE_URL . 'index.php?page=home');
    exit;
}
?>
