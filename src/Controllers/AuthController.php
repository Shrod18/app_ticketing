<?php

class AuthController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');

            $errors = [];

            // Validation
            if (empty($nom)) $errors[] = "Le nom est requis";
            if (empty($prenom)) $errors[] = "Le prénom est requis";
            if (empty($email)) $errors[] = "L'email est requis";
            if (empty($password)) $errors[] = "Le mot de passe est requis";
            if ($password !== $confirm_password) $errors[] = "Les mots de passe ne correspondent pas";
            if (strlen($password) < 6) $errors[] = "Le mot de passe doit contenir au moins 6 caractères";

            if (empty($errors)) {
                $user_model = new User($this->db);
                
                if ($user_model->emailExists($email)) {
                    $errors[] = "Cet email est déjà utilisé";
                } else {
                    $user_model->nom = $nom;
                    $user_model->prenom = $prenom;
                    $user_model->email = $email;
                    $user_model->password = $password;
                    $user_model->role = 'client'; // Les clients créent leur compte

                    if ($user_model->create()) {
                        $_SESSION['success'] = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
                        header('Location: ' . BASE_URL . 'index.php?page=login');
                        exit;
                    } else {
                        $errors[] = "Une erreur est survenue lors de la création du compte";
                    }
                }
            }

            return ['errors' => $errors];
        }

        return ['errors' => []];
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $errors = [];

            if (empty($email)) $errors[] = "L'email est requis";
            if (empty($password)) $errors[] = "Le mot de passe est requis";

            if (empty($errors)) {
                $user_model = new User($this->db);
                $user = $user_model->getUserByEmail($email);

                if ($user && password_verify($password, $user['password'])) {
                    // Authentification réussie
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                    $_SESSION['user_email'] = $user['email'];

                    // Mettre à jour la dernière connexion
                    $user_model->updateLastLogin($user['id']);

                    // Redirection selon le rôle
                    if ($user['role'] === 'ingenieur') {
                        header('Location: ' . BASE_URL . 'index.php?page=engineer_dashboard');
                    } else {
                        header('Location: ' . BASE_URL . 'index.php?page=client_dashboard');
                    }
                    exit;
                } else {
                    $errors[] = "Email ou mot de passe incorrect";
                }
            }

            return ['errors' => $errors];
        }

        return ['errors' => []];
    }

    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . 'index.php?page=login');
        exit;
    }
}
?>
