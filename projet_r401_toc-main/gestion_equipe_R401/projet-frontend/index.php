<?php
/**
 * =====================================================
 * FICHIER: index.php (projet-frontend)
 * ROLE: Routeur principal de l'application frontend
 * DESCRIPTION: Gere le routage MVC (controleur/action)
 * =====================================================
 */

// Inclusion des fichiers de configuration et d'authentification
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/securite/authentification.php';

// Demarrage de la session si elle n'est pas deja active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================================
// SECURITE: Generation d'un token CSRF
// =====================================================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// =====================================================
// RECUPERATION DES PARAMETRES D'URL
// =====================================================

/**
 * Controleur demande (par defaut: 'statistique')
 * @var string
 */
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'statistique';

/**
 * Action demandee (par defaut: 'dashboard')
 * @var string
 */
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// Securisation: suppression de tous les caracteres non alphabetiques
$controller = preg_replace('/[^a-zA-Z]/', '', $controller);
$action = preg_replace('/[^a-zA-Z]/', '', $action);

// =====================================================
// LISTE DES CONTROLEURS AUTORISES
// =====================================================
$allowedControllers = [
    'statistique',
    'joueur',
    'match',
    'commentaire'
];

// Verification que le controleur demande est autorise
if (!in_array(strtolower($controller), $allowedControllers)) {
    header('Location: index.php?controller=statistique&action=dashboard');
    exit;
}

// =====================================================
// CHARGEMENT ET EXECUTION DU CONTROLEUR
// =====================================================

/**
 * Chemin vers le fichier du controleur
 * @var string
 */
$controllerFile = __DIR__ . '/controllers/' . ucfirst($controller) . 'Controller.php';

// Verification de l'existence du fichier controleur
if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // Nom de la classe du controleur
    $controllerClass = ucfirst($controller) . 'Controller';

    // Verification de l'existence de la classe
    if (class_exists($controllerClass)) {
        // Instanciation du controleur
        $controllerInstance = new $controllerClass();

        // Verification de l'existence de la methode demandee
        if (method_exists($controllerInstance, $action)) {
            // Execution de l'action demandee
            $controllerInstance->$action();
        } else {
            // Si la methode n'existe pas, tentative avec 'index'
            if (method_exists($controllerInstance, 'index')) {
                $controllerInstance->index();
            } else {
                // Redirection par defaut
                header('Location: index.php?controller=statistique&action=dashboard');
                exit;
            }
        }
    } else {
        // Classe non trouvee -> redirection par defaut
        header('Location: index.php?controller=statistique&action=dashboard');
        exit;
    }
} else {
    // =====================================================
    // CONTROLEUR PAR DEFAUT (statistique/dashboard)
    // =====================================================
    require_once __DIR__ . '/controllers/StatistiqueController.php';
    $controllerInstance = new StatistiqueController();
    $controllerInstance->dashboard();
}
?>