<?php
/**
 * =====================================================
 * FICHIER: models/ParticipationModel.php
 * ROLE: Modele pour la gestion des participations (feuilles de match)
 * HERITE DE: Model
 * =====================================================
 */

require_once __DIR__ . '/Model.php';

/**
 * Classe ParticipationModel
 * Gere les operations CRUD sur la table Participer
 * Geres aussi l'enregistrement en masse des compositions
 */
class ParticipationModel extends Model
{
    /**
     * Nom de la table associee a ce modele
     * @var string
     */
    protected $table = 'Participer';

    /**
     * Constructeur
     * Appelle le constructeur parent
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Enregistre toute une composition en une seule transaction
     * Supprime les anciennes participations et insere les nouvelles
     *
     * @param int $idMatch ID du match
     * @param array $participants Liste des participants
     * @return bool True si reussi, False sinon
     */
    public function setParticipations($idMatch, $participants)
    {
        try {
            $this->db->beginTransaction();

            // Suppression des anciennes participations
            $stmtDelete = $this->db->prepare("DELETE FROM Participer WHERE id_match = :id_match");
            $stmtDelete->execute([':id_match' => intval($idMatch)]);

            // Insertion des nouvelles participations
            $stmtInsert = $this->db->prepare(
                "INSERT INTO Participer (id_joueur, id_match, titulaire, libelle_poste, evaluation)
                 VALUES (:id_joueur, :id_match, :titulaire, :libelle_poste, NULL)"
            );

            foreach ($participants as $p) {
                $stmtInsert->execute([
                    ':id_joueur'     => intval($p['id_joueur']),
                    ':id_match'      => intval($idMatch),
                    ':titulaire'     => $p['titulaire'] ? 1 : 0,
                    ':libelle_poste' => $p['libelle_poste'],
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Erreur setParticipations: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recupere toutes les participations avec les infos des joueurs et matchs
     *
     * @return array Liste des participations
     */
    public function getAll()
    {
        $sql = "SELECT p.*, j.nom_joueur, j.prenom_joueur, j.numero_licence,
                       m.equipe_adverse, m.date_heure_match
                FROM Participer p
                INNER JOIN Joueur j ON p.id_joueur = j.id_joueur
                INNER JOIN Matchs m ON p.id_match = m.id_match
                ORDER BY m.date_heure_match DESC";

        return $this->obtenirTousResultats($sql);
    }

    /**
     * Recupere une participation par son ID
     *
     * @param int $id ID de la participation
     * @return array|false Donnees de la participation ou false
     */
    public function getById($id)
    {
        $sql = "SELECT p.*, j.nom_joueur, j.prenom_joueur, j.numero_licence
                FROM Participer p
                INNER JOIN Joueur j ON p.id_joueur = j.id_joueur
                WHERE p.id_participation = :id";

        return $this->obtenirUnResultat($sql, [':id' => $id]);
    }

    /**
     * Recupere les joueurs selectionnes pour un match
     *
     * @param int $idMatch ID du match
     * @return array Liste des participations du match
     */
    public function getByMatch($idMatch)
    {
        $sql = "SELECT p.*, j.nom_joueur, j.prenom_joueur, j.numero_licence,
                       j.taille_cm, j.poids_kg, j.statut_joueur
                FROM Participer p
                INNER JOIN Joueur j ON p.id_joueur = j.id_joueur
                WHERE p.id_match = :id_match
                ORDER BY p.titulaire DESC, j.nom_joueur";

        $result = $this->obtenirTousResultats($sql, [':id_match' => $idMatch]);

        // Log pour deboguer
        error_log("getByMatch - Match ID: $idMatch, Nombre de joueurs: " . count($result));

        return $result;
    }

    /**
     * Cree une participation unitaire
     *
     * @param array $data Donnees de la participation
     * @return bool True si reussi, False sinon
     */
    public function create($data)
    {
        $sql = "INSERT INTO Participer (id_joueur, id_match, titulaire, libelle_poste, evaluation)
                VALUES (:id_joueur, :id_match, :titulaire, :libelle_poste, :evaluation)";

        return $this->executerRequete($sql, [
            ':id_joueur'     => intval($data['id_joueur']),
            ':id_match'      => intval($data['id_match']),
            ':titulaire'     => isset($data['titulaire']) ? intval($data['titulaire']) : 1,
            ':libelle_poste' => $data['libelle_poste'] ?? '',
            ':evaluation'    => $data['evaluation'] ?? null,
        ]);
    }

    /**
     * Met a jour une participation
     *
     * @param int $id ID de la participation
     * @param array $data Nouvelles donnees
     * @return bool True si reussi, False sinon
     */
    public function update($id, $data)
    {
        $sql = "UPDATE Participer SET
                    evaluation    = :evaluation,
                    libelle_poste = :libelle_poste,
                    titulaire     = :titulaire
                WHERE id_participation = :id";

        // Log pour deboguer
        error_log("ParticipationModel::update - ID: $id");
        error_log("Données reçues: " . print_r($data, true));

        try {
            $requete = $this->db->prepare($sql);
            $result = $requete->execute([
                ':id'            => $id,
                ':evaluation'    => $data['evaluation'] ?? null,
                ':libelle_poste' => $data['libelle_poste'] ?? '',
                ':titulaire'     => isset($data['titulaire']) ? intval($data['titulaire']) : 1,
            ]);

            error_log("Résultat de l'update: " . ($result ? 'SUCCÈS' : 'ÉCHEC'));

            return $result;
        } catch (PDOException $e) {
            error_log("Erreur PDO dans update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime une participation
     *
     * @param int $id ID de la participation
     * @return bool True si reussi, False sinon
     */
    public function delete($id)
    {
        $sql = "DELETE FROM Participer WHERE id_participation = :id";

        return $this->executerRequete($sql, [':id' => $id]);
    }
}