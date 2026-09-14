<?php
/**
 * =====================================================
 * FICHIER: models/JoueurModel.php
 * ROLE: Modele pour la gestion des joueurs
 * HERITE DE: Model
 * =====================================================
 */

require_once __DIR__ . '/Model.php';

/**
 * Classe JoueurModel
 * Gere les operations CRUD sur la table joueur
 * Contient aussi les methodes de statistiques
 */
class JoueurModel extends Model
{
    /**
     * Nom de la table associee a ce modele
     * @var string
     */
    protected $table = 'joueur';

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
     * Recupere tous les joueurs tries par nom
     *
     * @return array Liste des joueurs
     */
    public function getAll()
    {
        $sql = "SELECT * FROM joueur ORDER BY nom_joueur ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recupere un joueur par son ID
     *
     * @param int $id ID du joueur
     * @return array|false Donnees du joueur ou false
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM joueur WHERE id_joueur = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Recupere un joueur par son numero de licence
     *
     * @param string $licence Numero de licence
     * @return array|false Donnees du joueur ou false
     */
    public function getByLicence($licence)
    {
        $sql = "SELECT * FROM joueur WHERE numero_licence = :licence";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['licence' => $licence]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cree un nouveau joueur
     *
     * @param array $data Donnees du joueur
     * @return bool True si reussi, False sinon
     */
    public function create($data)
    {
        $sql = "INSERT INTO joueur (
                    numero_licence,
                    nom_joueur,
                    prenom_joueur,
                    date_naissance,
                    taille_cm,
                    poids_kg,
                    statut_joueur,
                    commentaires_joueur
                ) VALUES (
                    :numero_licence,
                    :nom_joueur,
                    :prenom_joueur,
                    :date_naissance,
                    :taille_cm,
                    :poids_kg,
                    :statut_joueur,
                    :commentaires_joueur
                )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'numero_licence'      => $data['numero_licence'],
            'nom_joueur'          => $data['nom_joueur'],
            'prenom_joueur'       => $data['prenom_joueur'],
            'date_naissance'      => $data['date_naissance'],
            'taille_cm'           => $data['taille_cm'],
            'poids_kg'            => $data['poids_kg'],
            'statut_joueur'       => $data['statut_joueur'],
            'commentaires_joueur' => $data['commentaires_joueur'] ?? null
        ]);
    }

    /**
     * Met a jour un joueur
     *
     * @param int $id ID du joueur
     * @param array $data Nouvelles donnees
     * @return bool True si reussi, False sinon
     */
    public function update($id, $data)
    {
        $sql = "UPDATE joueur SET
                    numero_licence = :numero_licence,
                    nom_joueur = :nom_joueur,
                    prenom_joueur = :prenom_joueur,
                    date_naissance = :date_naissance,
                    taille_cm = :taille_cm,
                    poids_kg = :poids_kg,
                    statut_joueur = :statut_joueur,
                    commentaires_joueur = :commentaires_joueur
                WHERE id_joueur = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id'                  => $id,
            'numero_licence'      => $data['numero_licence'],
            'nom_joueur'          => $data['nom_joueur'],
            'prenom_joueur'       => $data['prenom_joueur'],
            'date_naissance'      => $data['date_naissance'],
            'taille_cm'           => $data['taille_cm'],
            'poids_kg'            => $data['poids_kg'],
            'statut_joueur'       => $data['statut_joueur'],
            'commentaires_joueur' => $data['commentaires_joueur'] ?? null
        ]);
    }

    /**
     * Supprime un joueur
     *
     * @param int $id ID du joueur
     * @return bool True si reussi, False sinon
     */
    public function delete($id)
    {
        $sql = "DELETE FROM joueur WHERE id_joueur = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    // =====================================================
    // METHODES DE FILTRAGE
    // =====================================================

    /**
     * Recupere les joueurs par statut
     *
     * @param string $statut Statut du joueur (Actif, Blesse, Suspendu, Absent)
     * @return array Liste des joueurs filtres
     */
    public function getByStatut($statut)
    {
        $sql = "SELECT * FROM joueur
                WHERE statut_joueur = :statut
                ORDER BY nom_joueur ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['statut' => $statut]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recupere uniquement les joueurs actifs
     *
     * @return array Liste des joueurs actifs
     */
    public function getActifs()
    {
        $sql = "SELECT * FROM joueur
                WHERE statut_joueur = 'Actif'
                ORDER BY nom_joueur ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recupere uniquement les joueurs blesses
     *
     * @return array Liste des joueurs blesses
     */
    public function getBlesses()
    {
        $sql = "SELECT * FROM joueur
                WHERE statut_joueur = 'Blessé'
                ORDER BY nom_joueur ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // METHODES DE STATISTIQUES
    // =====================================================

    /**
     * Obtient les statistiques globales de l'equipe
     *
     * @return array Statistiques (matchs, victoires, defaites, nuls, joueurs...)
     */
    public function obtenirStatistiquesJoueurs()
    {
        // Statistiques des matchs termines
        $sqlMatchs = "SELECT
                        COUNT(*) as total_matchs,
                        SUM(CASE WHEN resultat_match = 'Victoire' THEN 1 ELSE 0 END) as victoires,
                        SUM(CASE WHEN resultat_match = 'Défaite' THEN 1 ELSE 0 END) as defaites,
                        SUM(CASE WHEN resultat_match = 'Nul' THEN 1 ELSE 0 END) as nuls
                      FROM matchs
                      WHERE statut_match = 'Terminé'";

        $stmtMatchs = $this->db->prepare($sqlMatchs);
        $stmtMatchs->execute();
        $statsMatchs = $stmtMatchs->fetch(PDO::FETCH_ASSOC);

        if (!$statsMatchs) {
            $statsMatchs = ['total_matchs' => 0, 'victoires' => 0, 'defaites' => 0, 'nuls' => 0];
        }

        // Nombre total de joueurs
        $sqlTotalJoueurs = "SELECT COUNT(*) as total FROM joueur";
        $stmtTotal = $this->db->prepare($sqlTotalJoueurs);
        $stmtTotal->execute();
        $totalJoueurs = $stmtTotal->fetch(PDO::FETCH_ASSOC);

        // Nombre de joueurs actifs
        $sqlActifs = "SELECT COUNT(*) as total FROM joueur WHERE statut_joueur = 'Actif'";
        $stmtActifs = $this->db->prepare($sqlActifs);
        $stmtActifs->execute();
        $actifs = $stmtActifs->fetch(PDO::FETCH_ASSOC);

        // Nombre de joueurs blesses
        $sqlBlesses = "SELECT COUNT(*) as total FROM joueur WHERE statut_joueur = 'Blessé'";
        $stmtBlesses = $this->db->prepare($sqlBlesses);
        $stmtBlesses->execute();
        $blesses = $stmtBlesses->fetch(PDO::FETCH_ASSOC);

        // Moyenne des evaluations
        $sqlEvals = "SELECT AVG(p.evaluation) as moyenne_notes
                     FROM participer p
                     INNER JOIN matchs m ON p.id_match = m.id_match
                     WHERE m.statut_match = 'Terminé' AND p.evaluation IS NOT NULL";

        $stmtEvals = $this->db->prepare($sqlEvals);
        $stmtEvals->execute();
        $evals = $stmtEvals->fetch(PDO::FETCH_ASSOC);

        return [
            'total_matchs'     => (int)($statsMatchs['total_matchs'] ?? 0),
            'victoires'        => (int)($statsMatchs['victoires'] ?? 0),
            'defaites'         => (int)($statsMatchs['defaites'] ?? 0),
            'nuls'             => (int)($statsMatchs['nuls'] ?? 0),
            'total_joueurs'    => (int)($totalJoueurs['total'] ?? 0),
            'joueurs_actifs'   => (int)($actifs['total'] ?? 0),
            'joueurs_blesses'  => (int)($blesses['total'] ?? 0),
            'moyenne_notes'    => round($evals['moyenne_notes'] ?? 0, 1)
        ];
    }

    /**
     * Obtient les statistiques de matchs d'un joueur
     *
     * @param int $idJoueur ID du joueur
     * @return array Statistiques du joueur
     */
    public function obtenirStatsMatchsJoueur($idJoueur)
    {
        $sql = "SELECT
                    COUNT(p.id_participation) as total_matchs,
                    SUM(CASE WHEN p.titulaire = 1 THEN 1 ELSE 0 END) as titularisations,
                    SUM(CASE WHEN p.titulaire = 0 THEN 1 ELSE 0 END) as remplacements,
                    SUM(CASE WHEN m.resultat_match = 'Victoire' THEN 1 ELSE 0 END) as matchs_gagnes,
                    SUM(CASE WHEN m.resultat_match = 'Défaite' THEN 1 ELSE 0 END) as matchs_perdus,
                    SUM(CASE WHEN m.resultat_match = 'Nul' THEN 1 ELSE 0 END) as matchs_nuls,
                    ROUND(AVG(p.evaluation), 1) as moyenne_notes
                FROM participer p
                INNER JOIN matchs m ON p.id_match = m.id_match
                WHERE p.id_joueur = :id_joueur AND m.statut_match = 'Terminé'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_joueur' => $idJoueur]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            $result = [
                'total_matchs'     => 0,
                'titularisations'  => 0,
                'remplacements'    => 0,
                'matchs_gagnes'    => 0,
                'matchs_perdus'    => 0,
                'matchs_nuls'      => 0,
                'moyenne_notes'    => 0
            ];
        }

        // Calcul du pourcentage de victoires
        $totalMatchsJoues = $result['total_matchs'];
        $matchsGagnes = $result['matchs_gagnes'];
        $result['pourcentage_victoires'] = $totalMatchsJoues > 0
            ? round(($matchsGagnes / $totalMatchsJoues) * 100, 1)
            : 0;

        // Ajout du poste prefere
        $postePrefere = $this->obtenirPostePreferJoueur($idJoueur);
        $result['poste_prefere'] = $postePrefere['poste_prefere'] ?? 'N/A';

        // Ajout des selections consecutives
        $selections = $this->obtenirSelectionsConsecutives($idJoueur);
        $result['selections_consecutives'] = $selections['consecutives'] ?? 0;

        return $result;
    }

    /**
     * Obtient les performances d'un joueur
     *
     * @param int $idJoueur ID du joueur
     * @return array Performances (moyenne, meilleure note, pire note)
     */
    public function obtenirPerformancesJoueur($idJoueur)
    {
        $sql = "SELECT
                    ROUND(AVG(p.evaluation), 1) as moyenne_evaluation,
                    MAX(p.evaluation) as meilleure_note,
                    MIN(p.evaluation) as pire_note,
                    COUNT(p.id_participation) as total_matchs
                FROM participer p
                INNER JOIN matchs m ON p.id_match = m.id_match
                WHERE p.id_joueur = :id_joueur AND m.statut_match = 'Terminé'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_joueur' => $idJoueur]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            $result = [
                'moyenne_evaluation' => 0,
                'meilleure_note'     => 0,
                'pire_note'          => 0,
                'total_matchs'       => 0
            ];
        }

        return $result;
    }

    /**
     * Obtient les derniers matchs d'un joueur
     *
     * @param int $idJoueur ID du joueur
     * @param int $limit Nombre de matchs a recuperer (defaut: 10)
     * @return array Liste des derniers matchs
     */
    public function obtenirDerniersMatchsJoueur($idJoueur, $limit = 10)
    {
        $sql = "SELECT
                    m.id_match,
                    m.date_heure_match,
                    m.equipe_adverse,
                    m.lieu_match,
                    m.resultat_match,
                    m.score_equipe,
                    m.score_adverse,
                    p.titulaire,
                    p.libelle_poste,
                    p.evaluation
                FROM participer p
                INNER JOIN matchs m ON p.id_match = m.id_match
                WHERE p.id_joueur = :id_joueur AND m.statut_match = 'Terminé'
                ORDER BY m.date_heure_match DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_joueur', $idJoueur, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtient les statistiques de selections d'un joueur
     *
     * @param int $idJoueur ID du joueur
     * @return array Statistiques de selections
     */
    public function obtenirStatsSelectionsJoueur($idJoueur)
    {
        $sql = "SELECT
                    COUNT(p.id_participation) as total_selections,
                    SUM(CASE WHEN p.titulaire = 1 THEN 1 ELSE 0 END) as titularisations,
                    SUM(CASE WHEN p.titulaire = 0 THEN 1 ELSE 0 END) as remplacements,
                    MAX(p.evaluation) as meilleure_note
                FROM participer p
                INNER JOIN matchs m ON p.id_match = m.id_match
                WHERE p.id_joueur = :id_joueur AND m.statut_match = 'Terminé'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_joueur' => $idJoueur]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            $result = [
                'total_selections' => 0,
                'titularisations'  => 0,
                'remplacements'    => 0,
                'meilleure_note'   => 0
            ];
        }

        // Ajout des selections consecutives
        $selections = $this->obtenirSelectionsConsecutives($idJoueur);
        $result['selections_consecutives'] = $selections['consecutives'] ?? 0;

        return $result;
    }

    /**
     * Obtient le poste prefere d'un joueur
     *
     * @param int $idJoueur ID du joueur
     * @return array|null Poste prefere ou null
     */
    public function obtenirPostePreferJoueur($idJoueur)
    {
        $sql = "SELECT
                    p.libelle_poste as poste,
                    COUNT(*) as nombre_fois
                FROM participer p
                INNER JOIN matchs m ON p.id_match = m.id_match
                WHERE p.id_joueur = :id_joueur AND m.statut_match = 'Terminé'
                GROUP BY p.libelle_poste
                ORDER BY nombre_fois DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_joueur' => $idJoueur]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $posteNom = $this->traduirePoste($result['poste']);
            return [
                'poste_prefere' => $posteNom,
                'nombre_fois'   => $result['nombre_fois']
            ];
        }

        return null;
    }

    /**
     * Traduit un code de poste en nom lisible
     *
     * @param string $poste Code du poste
     * @return string Nom lisible du poste
     */
    private function traduirePoste($poste)
    {
        $mapping = [
            'Gardien'           => 'Gardien de but',
            'DCG'               => 'Défenseur central gauche',
            'DCD'               => 'Défenseur central droit',
            'AD'                => 'Arrière droit',
            'AG'                => 'Arrière gauche',
            'MD'                => 'Milieu défensif',
            'MC'                => 'Milieu central',
            'MO'                => 'Milieu offensif',
            'AiD'               => 'Ailier droit',
            'AiG'               => 'Ailier gauche',
            'AC'                => 'Avant-centre',
            'Défenseur central' => 'Défenseur central',
            'Arrière'           => 'Arrière',
            'Milieu défensif'   => 'Milieu défensif',
            'Milieu relayeur'   => 'Milieu relayeur',
            'Milieu offensif'   => 'Milieu offensif',
            'Ailier'            => 'Ailier',
            'Avant-centre'      => 'Avant-centre'
        ];

        return $mapping[$poste] ?? $poste;
    }

    /**
     * Obtient le nombre de selections consecutives d'un joueur
     *
     * @param int $idJoueur ID du joueur
     * @return array Nombre de selections consecutives
     */
    public function obtenirSelectionsConsecutives($idJoueur)
    {
        $sql = "SELECT
                    m.id_match,
                    m.date_heure_match,
                    CASE WHEN p.id_participation IS NOT NULL THEN 1 ELSE 0 END as a_joue
                FROM matchs m
                LEFT JOIN participer p ON p.id_match = m.id_match AND p.id_joueur = :id_joueur
                WHERE m.statut_match = 'Terminé'
                ORDER BY m.date_heure_match DESC, m.id_match DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_joueur' => $idJoueur]);
        $matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $consecutives = 0;
        foreach ($matchs as $match) {
            if ($match['a_joue'] == 1) {
                $consecutives++;
            } else {
                break;
            }
        }

        return ['consecutives' => $consecutives];
    }

    /**
     * Obtient les statistiques pour tous les joueurs
     *
     * @return array Statistiques par joueur
     */
    public function obtenirStatistiquesParJoueur()
    {
        $sql = "SELECT
                    j.id_joueur,
                    j.numero_licence,
                    j.nom_joueur as nom,
                    j.prenom_joueur as prenom,
                    j.statut_joueur as statut,
                    COUNT(p.id_participation) AS total_matchs,
                    SUM(CASE WHEN p.titulaire = 1 THEN 1 ELSE 0 END) AS titularisations,
                    SUM(CASE WHEN p.titulaire = 0 THEN 1 ELSE 0 END) AS remplacements,
                    ROUND(AVG(p.evaluation), 1) AS moyenne_evaluation,
                    SUM(CASE WHEN m.resultat_match = 'Victoire' THEN 1 ELSE 0 END) AS matchs_gagnes,
                    (
                        SELECT p2.libelle_poste
                        FROM participer p2
                        INNER JOIN matchs m2 ON p2.id_match = m2.id_match
                        WHERE p2.id_joueur = j.id_joueur AND m2.statut_match = 'Terminé'
                        GROUP BY p2.libelle_poste
                        ORDER BY COUNT(*) DESC
                        LIMIT 1
                    ) AS poste_prefere
                FROM joueur j
                LEFT JOIN participer p ON p.id_joueur = j.id_joueur
                LEFT JOIN matchs m ON p.id_match = m.id_match AND m.statut_match = 'Terminé'
                GROUP BY j.id_joueur, j.numero_licence, j.nom_joueur,
                         j.prenom_joueur, j.statut_joueur
                ORDER BY j.nom_joueur ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}