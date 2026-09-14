<?php
/**
 * =====================================================
 * FICHIER: config/connexion-bdd.php
 * ROLE: Gestion de la connexion a la base de donnees principale
 * PATTERN: Singleton
 * =====================================================
 */

/**
 * Classe ConnexionBaseDeDonnees
 * Gere la connexion a la base de donnees projetequipe
 */
class ConnexionBaseDeDonnees
{
    /**
     * Instance unique de la classe (pattern Singleton)
     * @var ConnexionBaseDeDonnees|null
     */
    private static $instance = null;

    /**
     * Objet PDO de connexion
     * @var PDO
     */
    private $connexion;

    /**
     * Constructeur prive (pattern Singleton)
     * Initialise la connexion PDO
     */
    private function __construct()
    {
        try {
            // Configuration de la base de donnees
            $host = 'localhost';
            $dbname = 'projetequipe';
            $username = 'root';
            $password = '';

            // Creation de la connexion PDO
            $this->connexion = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            // Arret du script en cas d'erreur de connexion
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    /**
     * Retourne l'instance unique de la classe
     *
     * @return ConnexionBaseDeDonnees L'instance unique
     */
    public static function obtenirInstance()
    {
        if (self::$instance === null) {
            self::$instance = new ConnexionBaseDeDonnees();
        }
        return self::$instance;
    }

    /**
     * Retourne l'objet PDO de connexion
     *
     * @return PDO L'objet PDO
     */
    public function obtenirConnexion()
    {
        return $this->connexion;
    }
}
?>