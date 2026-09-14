<?php
/**
 * =====================================================
 * FICHIER: models/Model.php
 * ROLE: Modele abstrait parent pour tous les modeles
 * PATTERN: Template Method
 * =====================================================
 */

require_once __DIR__ . '/../config/connexion-bdd.php';

/**
 * Classe abstraite Model
 * Classe parente pour tous les modeles de l'application
 */
abstract class Model
{
    /**
     * Connexion PDO a la base de donnees
     * @var PDO
     */
    protected $db;

    /**
     * Nom de la table associee au modele
     * @var string
     */
    protected $table;

    /**
     * Constructeur
     * Initialise la connexion a la base de donnees
     */
    public function __construct()
    {
        $instanceBDD = ConnexionBaseDeDonnees::obtenirInstance();
        $this->db = $instanceBDD->obtenirConnexion();
    }

    /**
     * Execute une requete SQL avec des parametres
     *
     * @param string $sql Requete SQL
     * @param array $parametres Parametres a lier
     * @return PDOStatement|false Le statement ou false
     */
    protected function executerRequete($sql, $parametres = [])
    {
        try {
            $requete = $this->db->prepare($sql);
            if (!$requete) {
                return false;
            }

            $resultat = $requete->execute($parametres);

            return $resultat ? $requete : false;
        } catch (PDOException $e) {
            error_log("PDOException: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recupere un seul resultat d'une requete
     *
     * @param string $sql Requete SQL
     * @param array $parametres Parametres a lier
     * @return array|false Le resultat ou false
     */
    protected function obtenirUnResultat($sql, $parametres = [])
    {
        $requete = $this->executerRequete($sql, $parametres);
        return $requete ? $requete->fetch() : false;
    }

    /**
     * Recupere tous les resultats d'une requete
     *
     * @param string $sql Requete SQL
     * @param array $parametres Parametres a lier
     * @return array Liste des resultats
     */
    protected function obtenirTousResultats($sql, $parametres = [])
    {
        $requete = $this->executerRequete($sql, $parametres);
        return $requete ? $requete->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Trouve un enregistrement par son ID
     *
     * @param int $id ID de l'enregistrement
     * @return array|false Le resultat ou false
     */
    public function trouverParId($id)
    {
        $col = "id_" . $this->table;
        $sql = "SELECT * FROM " . $this->table . " WHERE $col = ?";

        return $this->obtenirUnResultat($sql, [$id]);
    }

    /**
     * Trouve tous les enregistrements tries
     *
     * @param string|null $ordre Champ de tri
     * @return array Liste des enregistrements
     */
    public function trouverTous($ordre = null)
    {
        $ordre = $ordre ?? "id_" . $this->table . " ASC";
        $sql = "SELECT * FROM " . $this->table . " ORDER BY $ordre";

        return $this->obtenirTousResultats($sql);
    }

    /**
     * Supprime un enregistrement par son ID
     *
     * @param int $id ID de l'enregistrement
     * @return bool True si reussi, False sinon
     */
    public function supprimer($id)
    {
        $col = "id_" . $this->table;
        $sql = "DELETE FROM " . $this->table . " WHERE $col = ?";

        return $this->executerRequete($sql, [$id]) ? true : false;
    }

    /**
     * Compte le nombre total d'enregistrements
     *
     * @return int Nombre total d'enregistrements
     */
    public function compter()
    {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table;
        $resultat = $this->obtenirUnResultat($sql);

        return $resultat ? $resultat['total'] : 0;
    }
}