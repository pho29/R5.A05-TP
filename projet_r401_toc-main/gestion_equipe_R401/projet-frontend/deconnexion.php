<?php
/**
 * =====================================================
 * FICHIER: deconnexion.php
 * ROLE: Script de deconnexion
 * DESCRIPTION: Detruit la session et redirige vers la page de connexion
 * =====================================================
 */

// Demarrage de la session
session_start();

/**
 * Destruction complete de la session
 */

// Suppression de toutes les variables de session
$_SESSION = array();

// Destruction du cookie de session s'il existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruction de la session
session_destroy();

// Redirection vers la page de connexion
header('Location: connexion.php');
exit();
?>