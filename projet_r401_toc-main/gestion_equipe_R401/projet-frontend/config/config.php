<?php
/**
 * =====================================================
 * FICHIER: config/config.php
 * ROLE: Configuration principale de l'application frontend
 * DESCRIPTION: Constantes et configuration globale
 * =====================================================
 */

// =====================================================
// GESTION DE LA SESSION
// =====================================================

// Demarrage de la session si elle n'est pas deja active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================================
// CONSTANTES DE L'APPLICATION
// =====================================================

/** Nom de l'application */
define('NOM_APPLICATION', 'Manager Football Pro');

/** Version de l'application */
define('VERSION_APPLICATION', '1.0.0');

/** Nom de l'equipe */
define('NOM_EQUIPE', 'Les Champions FC');

// =====================================================
// REGLES DE GESTION D'EQUIPE
// =====================================================

/** Nombre maximum de joueurs titulaires */
define('JOUEURS_TITULAIRES_MAX', 11);

/** Nombre maximum de remplacants */
define('JOUEURS_REMPLACANTS_MAX', 7);

/** Nombre maximum total de joueurs */
define('JOUEURS_TOTAL_MAX', 23);

// =====================================================
// CHEMINS DES DOSSIERS
// =====================================================

/** Chemin racine du projet */
define('CHEMIN_RACINE', dirname(__DIR__));

/** Chemin vers le dossier interface (entete, navigation, pied de page) */
define('CHEMIN_INCLUDES', __DIR__ . '/../interface');

/** Chemin vers le dossier securite */
define('CHEMIN_SECURITE', __DIR__ . '/../securite');

// =====================================================
// URLS DES API BACKEND
// =====================================================

/** URL de base du backend (sans /api/ - architecture RESTful) */
define('URL_API_BACKEND', 'http://localhost/projet_r401_toc/gestion_equipe_R401/projet-backend');

/** URL de l'API d'authentification */
define('URL_API_AUTH', 'http://localhost/projet_r401_toc/gestion_equipe_R401/projet-apiAuthentification/auth.php');

// =====================================================
// ENDPOINTS API RESTFUL (via le routeur backend)
// =====================================================

/** Endpoint pour la gestion des joueurs */
define('API_JOUEURS', URL_API_BACKEND . '/joueurs');

/** Endpoint pour la gestion des matchs */
define('API_MATCHS', URL_API_BACKEND . '/matchs');

/** Endpoint pour les statistiques */
define('API_STATISTIQUES', URL_API_BACKEND . '/statistiques');

/** Endpoint pour les commentaires */
define('API_COMMENTAIRES', URL_API_BACKEND . '/commentaires');

/** Endpoint pour les feuilles de match (participations) */
define('API_FEUILLES_MATCH', URL_API_BACKEND . '/feuilles-match');

/** Alias pour compatibilite */
define('API_PARTICIPATIONS', URL_API_BACKEND . '/feuilles-match');

// =====================================================
// CONFIGURATION PHP
// =====================================================

/** Definition du fuseau horaire */
date_default_timezone_set('Europe/Paris');

/** Activation de l'affichage des erreurs (desactiver en production) */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/** Encodage par defaut */
header('Content-Type: text/html; charset=utf-8');
?>