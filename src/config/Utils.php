<?php

/**
 * Utilitaires communs
 */

class Utils {
    
    /**
     * Formater une date pour l'affichage
     */
    public static function formatDate($date_string) {
        if (empty($date_string)) return '';
        $date = new DateTime($date_string);
        return $date->format('d/m/Y à H:i');
    }

    /**
     * Formater une date courte
     */
    public static function formatDateShort($date_string) {
        if (empty($date_string)) return '';
        $date = new DateTime($date_string);
        return $date->format('d/m/Y');
    }

    /**
     * Formater la taille d'un fichier
     */
    public static function formatFileSize($bytes) {
        if ($bytes == 0) return '0 B';
        
        $units = array('B', 'KB', 'MB', 'GB');
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Vérifier si l'utilisateur est connecté
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Vérifier si l'utilisateur est un ingénieur
     */
    public static function isEngineer() {
        return self::isLoggedIn() && $_SESSION['user_role'] === 'ingenieur';
    }

    /**
     * Vérifier si l'utilisateur est un client
     */
    public static function isClient() {
        return self::isLoggedIn() && $_SESSION['user_role'] === 'client';
    }

    /**
     * Obtenir le badge HTML de statut
     */
    public static function getStatusBadge($status) {
        $badge_class = match($status) {
            'ouvert' => 'badge-info',
            'assigné' => 'badge-warning',
            'clôturé' => 'badge-success',
            default => 'badge-secondary'
        };
        
        return '<span class="badge ' . $badge_class . '">' . ucfirst($status) . '</span>';
    }

    /**
     * Obtenir le badge HTML de criticité
     */
    public static function getCriticalityBadge($criticality) {
        return '<span class="badge criticite-' . $criticality . '">' . ucfirst($criticality) . '</span>';
    }

    /**
     * Valider une adresse email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Générer une chaîne aléatoire
     */
    public static function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Défiler les logs
     */
    public static function log($message, $level = 'INFO') {
        $log_dir = dirname(__DIR__) . '/logs/';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $log_file = $log_dir . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $log_message = "[$timestamp] [$level] $message" . PHP_EOL;
        
        file_put_contents($log_file, $log_message, FILE_APPEND);
    }

    /**
     * Rediriger avec message
     */
    public static function redirect($url, $message = '', $type = 'success') {
        if ($message) {
            $_SESSION[$type] = $message;
        }
        header('Location: ' . $url);
        exit;
    }

    /**
     * Récupérer et effacer un message de session
     */
    public static function getFlash($key) {
        $message = $_SESSION[$key] ?? null;
        if ($message) {
            unset($_SESSION[$key]);
        }
        return $message;
    }

    /**
     * Paginer un array
     */
    public static function paginate($array, $page = 1, $per_page = 10) {
        $total = count($array);
        $total_pages = ceil($total / $per_page);
        $page = max(1, min($page, $total_pages));
        
        $start = ($page - 1) * $per_page;
        $items = array_slice($array, $start, $per_page);
        
        return [
            'items' => $items,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total' => $total,
            'has_prev' => $page > 1,
            'has_next' => $page < $total_pages
        ];
    }
}
?>
