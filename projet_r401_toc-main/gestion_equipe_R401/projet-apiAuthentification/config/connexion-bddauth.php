<?php
/**
 * =====================================================
 * FICHIER: config/connexion-bddauth.php
 * ROLE: Gestion de la connexion a la base de donnees d'authentification
 * PATTERN: Singleton
 * =====================================================
 */

/** Inclusion du fichier de configuration */
require_once __DIR__ . '/config.php';

/**
 * Classe ConnexionBaseDeDonneesAuthentification
 * Gere la connexion a la base de donnees d'authentification avec un pattern Singleton
 */
class ConnexionBaseDeDonneesAuthentification
{
    /**
     * Instance unique de la classe (pattern Singleton)
     * @var ConnexionBaseDeDonneesAuthentification|null
     */
    private static $instance = null;

    /**
     * Objet PDO de connexion a la base de donnees
     * @var PDO
     */
    private $connexion;

    /**
     * Constructeur prive (pattern Singleton)
     * Initialise la connexion PDO a la base de donnees
     * @throws Exception Si la connexion a la base de donnees echoue
     */
    private function __construct()
    {
        try {
            /** Construction du DSN (Data Source Name) */
            $dsn = "mysql:host=" . HOTE_BDD . ";dbname=" . NOM_BDD2 . ";charset=utf8mb4";

            /** Creation de la connexion PDO avec les options de securite */
            $this->connexion = new PDO($dsn, UTILISATEUR_BDD, MOT_DE_PASSE_BDD, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lance des exceptions en cas d'erreur
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retourne des tableaux associatifs
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Utilise les vraies requetes preparees
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"     // Force l'encodage UTF-8
            ]);
        } catch (PDOException $e) {
            /** En cas d'erreur, on leve une exception avec un message clair */
            throw new Exception("Erreur de connexion a la base d'authentification: " . $e->getMessage());
        }
    }

    /**
     * Retourne l'instance unique de la classe (pattern Singleton)
     * @return ConnexionBaseDeDonneesAuthentification L'instance unique
     */
    public static function obtenirInstance()
    {
        /** Si l'instance n'existe pas encore, on la cree */
        if (self::$instance === null) {
            self::$instance = new ConnexionBaseDeDonneesAuthentification();
        }
        return self::$instance;
    }

    /**
     * Retourne l'objet PDO de connexion a la base de donnees
     * @return PDO L'objet PDO
     */
    public function obtenirConnexion()
    {
        return $this->connexion;
    }
}

?>