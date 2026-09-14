<?php
/**
 * =====================================================
 * FICHIER: models/MatchModel.php
 * ROLE: Modele pour la gestion des matchs
 * HERITE DE: Model
 * =====================================================
 */

require_once __DIR__ . '/Model.php';

/**
 * Classe MatchModel
 * Gere les operations CRUD sur la table Matchs
 */
class MatchModel extends Model
{
    /**
     * Nom de la table associee a ce modele
     * @var string
     */
    protected $table = 'Matchs';

    /**
     * Constructeur
     * Appelle le constructeur parent
     */
    public function __construct()
    {
        parent::__construct();
    }

    // =====================================================
    // METHODES CRUD DE BASE
    // =====================================================

    /**
     * Recupere tous les matchs tries par date decroissante
     *
     * @return array Liste des matchs
     */
    public function getAll()
    {
        $sql = "SELECT * FROM Matchs ORDER BY date_heure_match DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recupere un match par son ID
     *
     * @param int $id ID du match
     * @return array|false Donnees du match ou false
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM Matchs WHERE id_match = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cree un nouveau match
     *
     * @param array $data Donnees du match
     * @return bool True si reussi, False sinon
     */
    public function create($data)
    {
        $sql = "INSERT INTO Matchs (
                    date_heure_match,
                    equipe_adverse,
                    lieu_match,
                    lieu_rencontre,
                    resultat_match,
                    score_equipe,
                    score_adverse,
                    statut_match,
                    commentaires_match
                ) VALUES (
                    :date_heure_match,
                    :equipe_adverse,
                    :lieu_match,
                    :lieu_rencontre,
                    :resultat_match,
                    :score_equipe,
                    :score_adverse,
                    :statut_match,
                    :commentaires_match
                )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'date_heure_match'  => $data['date_heure_match'] ?? null,
            'equipe_adverse'    => $data['equipe_adverse'],
            'lieu_match'        => $data['lieu_match'] ?? 'Domicile',
            'lieu_rencontre'    => $data['lieu_rencontre'] ?? '',
            'resultat_match'    => $data['resultat_match'] ?? 'À venir',
            'score_equipe'      => $data['score_equipe'] ?? 0,
            'score_adverse'     => $data['score_adverse'] ?? 0,
            'statut_match'      => $data['statut_match'] ?? 'À venir',
            'commentaires_match'=> $data['commentaires_match'] ?? null
        ]);
    }

    /**
     * Met a jour un match
     *
     * @param int $id ID du match
     * @param array $data Nouvelles donnees
     * @return bool True si reussi, False sinon
     */
    public function update($id, $data)
    {
        $sql = "UPDATE Matchs SET
                    date_heure_match = :date_heure_match,
                    equipe_adverse = :equipe_adverse,
                    lieu_match = :lieu_match,
                    lieu_rencontre = :lieu_rencontre,
                    resultat_match = :resultat_match,
                    score_equipe = :score_equipe,
                    score_adverse = :score_adverse,
                    statut_match = :statut_match,
                    commentaires_match = :commentaires_match
                WHERE id_match = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id'                  => $id,
            'date_heure_match'    => $data['date_heure_match'] ?? null,
            'equipe_adverse'      => $data['equipe_adverse'] ?? '',
            'lieu_match'          => $data['lieu_match'] ?? 'Domicile',
            'lieu_rencontre'      => $data['lieu_rencontre'] ?? '',
            'resultat_match'      => $data['resultat_match'] ?? 'À venir',
            'score_equipe'        => $data['score_equipe'] ?? 0,
            'score_adverse'       => $data['score_adverse'] ?? 0,
            'statut_match'        => $data['statut_match'] ?? 'À venir',
            'commentaires_match'  => $data['commentaires_match'] ?? null
        ]);
    }

    /**
     * Supprime un match
     *
     * @param int $id ID du match
     * @return bool True si reussi, False sinon
     */
    public function delete($id)
    {
        $sql = "DELETE FROM Matchs WHERE id_match = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    // =====================================================
    // METHODES DE FILTRAGE
    // =====================================================

    /**
     * Recupere les matchs par statut
     *
     * @param string $statut Statut du match (Termine, A venir, Prepare)
     * @return array Liste des matchs filtres
     */
    public function getByStatut($statut)
    {
        $sql = "SELECT * FROM Matchs
                WHERE statut_match = :statut
                ORDER BY date_heure_match DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['statut' => $statut]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recupere les matchs a venir
     *
     * @return array Liste des matchs a venir
     */
    public function getUpcoming()
    {
        $sql = "SELECT * FROM Matchs
                WHERE statut_match IN ('À venir', 'Préparé')
                AND date_heure_match >= NOW()
                ORDER BY date_heure_match ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recupere les matchs passes (termines)
     *
     * @return array Liste des matchs passes
     */
    public function getPast()
    {
        $sql = "SELECT * FROM Matchs
                WHERE statut_match = 'Terminé'
                ORDER BY date_heure_match DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}