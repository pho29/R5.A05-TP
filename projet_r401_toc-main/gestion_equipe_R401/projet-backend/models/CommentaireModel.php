<?php
/**
 * =====================================================
 * FICHIER: models/CommentaireModel.php
 * ROLE: Modele pour la gestion des commentaires
 * HERITE DE: Model
 * =====================================================
 */

require_once __DIR__ . '/Model.php';

/**
 * Classe CommentaireModel
 * Gere les operations CRUD sur la table Commentaire
 */
class CommentaireModel extends Model
{
    /**
     * Nom de la table associee a ce modele
     * @var string
     */
    protected $table = 'Commentaire';

    /**
     * Constructeur
     * Appelle le constructeur parent
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Recupere tous les commentaires avec les informations du joueur
     *
     * @return array Liste des commentaires
     */
    public function getAll()
    {
        $sql = "SELECT c.*, j.nom_joueur, j.prenom_joueur
                FROM Commentaire c
                JOIN Joueur j ON c.id_joueur = j.id_joueur
                ORDER BY c.date_creation DESC";

        return $this->obtenirTousResultats($sql);
    }

    /**
     * Recupere un commentaire par son ID avec les infos du joueur
     *
     * @param int $id ID du commentaire
     * @return array|false Donnees du commentaire ou false
     */
    public function getById($id)
    {
        $sql = "SELECT c.*, j.nom_joueur, j.prenom_joueur, j.numero_licence
                FROM Commentaire c
                JOIN Joueur j ON c.id_joueur = j.id_joueur
                WHERE c.id_commentaire = :id";

        $requete = $this->db->prepare($sql);
        $requete->execute(['id' => $id]);

        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Recupere tous les commentaires d'un joueur specifique
     *
     * @param int $idJoueur ID du joueur
     * @return array Liste des commentaires du joueur
     */
    public function getByJoueur($idJoueur)
    {
        $sql = "SELECT *
                FROM Commentaire
                WHERE id_joueur = :id_joueur
                ORDER BY date_creation DESC";

        $requete = $this->db->prepare($sql);
        $requete->execute(['id_joueur' => $idJoueur]);

        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cree un nouveau commentaire
     *
     * @param array $data Donnees du commentaire (id_joueur, commentaire)
     * @return bool True si reussi, False sinon
     */
    public function create($data)
    {
        $sql = "INSERT INTO Commentaire (id_joueur, commentaire)
                VALUES (:id_joueur, :commentaire)";

        $requete = $this->db->prepare($sql);

        return $requete->execute([
            'id_joueur'   => $data['id_joueur'],
            'commentaire' => $data['commentaire']
        ]);
    }

    /**
     * Modifie un commentaire existant
     *
     * @param int $id ID du commentaire
     * @param array $data Nouvelles donnees (commentaire)
     * @return bool True si reussi, False sinon
     */
    public function update($id, $data)
    {
        $sql = "UPDATE Commentaire SET
                    commentaire = :commentaire,
                    date_modification = NOW()
                WHERE id_commentaire = :id";

        $requete = $this->db->prepare($sql);

        return $requete->execute([
            'id'          => $id,
            'commentaire' => $data['commentaire']
        ]);
    }

    /**
     * Supprime un commentaire
     *
     * @param int $id ID du commentaire
     * @return bool True si reussi, False sinon
     */
    public function delete($id)
    {
        $sql = "DELETE FROM Commentaire WHERE id_commentaire = :id";

        $requete = $this->db->prepare($sql);

        return $requete->execute(['id' => $id]);
    }

    /**
     * Compte le nombre de commentaires pour un joueur
     *
     * @param int $idJoueur ID du joueur
     * @return int Nombre de commentaires
     */
    public function compterParJoueur($idJoueur)
    {
        $sql = "SELECT COUNT(*) as total
                FROM Commentaire
                WHERE id_joueur = :id_joueur";

        $requete = $this->db->prepare($sql);
        $requete->execute(['id_joueur' => $idJoueur]);

        $result = $requete->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['total'] : 0;
    }

    /**
     * Recupere les N derniers commentaires d'un joueur
     *
     * @param int $idJoueur ID du joueur
     * @param int $limit Nombre de commentaires a recuperer (defaut: 5)
     * @return array Liste des derniers commentaires
     */
    public function getDerniersCommentaires($idJoueur, $limit = 5)
    {
        $sql = "SELECT *
                FROM Commentaire
                WHERE id_joueur = :id_joueur
                ORDER BY date_creation DESC
                LIMIT :limit";

        $requete = $this->db->prepare($sql);
        $requete->bindValue(':id_joueur', $idJoueur, PDO::PARAM_INT);
        $requete->bindValue(':limit', $limit, PDO::PARAM_INT);
        $requete->execute();

        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
}