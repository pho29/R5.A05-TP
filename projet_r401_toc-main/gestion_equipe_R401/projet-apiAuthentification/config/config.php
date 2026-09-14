<?php
/**
 * =====================================================
 * FICHIER: config/config.php
 * ROLE: Definition des constantes de configuration du projet
 * =====================================================
 */

// =====================================================
// CONFIGURATION BASE DE DONNEES
// =====================================================

/**
 * Serveur MySQL (generalement localhost)
 * @var string
 */
define('HOTE_BDD', 'localhost');

/**
 * Nom de la base de donnees d'authentification
 * @var string
 */
define('NOM_BDD2', 'bddauth');

/**
 * Utilisateur MySQL (root par defaut en developpement)
 * @var string
 */
define('UTILISATEUR_BDD', 'root');

/**
 * Mot de passe MySQL (vide par defaut en local)
 * @var string
 */
define('MOT_DE_PASSE_BDD', '');

// =====================================================
// CONFIGURATION JWT (JSON Web Tokens)
// =====================================================

/**
 * Cle secrete pour signer les tokens JWT
 * IMPORTANT: A MODIFIER pour la production
 * Doit etre une chaine complexe et unique
 * @var string
 */
define('JWT_SECRET', 'VOTRE_CLE_SECRETE_SUPER_COMPLEXE_ICI_123456789');

/**
 * Duree de validite du token en secondes
 * 3600 secondes = 1 heure
 * @var int
 */
define('JWT_EXPIRATION', 3600);

// =====================================================
// CONFIGURATION PHP
// =====================================================

/**
 * Definition du fuseau horaire sur Paris
 */
date_default_timezone_set('Europe/Paris');

/**
 * Activation de l'affichage de toutes les erreurs PHP
 */
error_reporting(E_ALL);

/**
 * Affichage des erreurs a l'ecran (desactiver en production)
 */
ini_set('display_errors', 1);
?>