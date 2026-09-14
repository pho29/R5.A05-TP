<?php
/**
 * =====================================================
 * FICHIER: controllers/StatistiqueController.php
 * ROLE: API REST pour les statistiques (version controleur)
 * METHODES: getGlobales, getJoueurStats, getPerformances, getDerniersMatchs,
 *          getSelections, getPostePrefer, getSelectionsConsecutives
 * =====================================================
 */

// Configuration des en-tetes HTTP
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Gestion des requetes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Inclusion des dependances
require_once dirname(__DIR__) . '/models/JoueurModel.php';
require_once dirname(__DIR__) . '/config/auth.php';

/**
 * Classe StatistiqueController
 * Gere les requetes API pour les statistiques
 */
class StatistiqueController
{
    /**
     * Instance du modele Joueur
     * @var JoueurModel
     */
    private $joueurModel;

    /**
     * Constructeur
     * Initialise le modele et verifie le token
     */
    public function __construct()
    {
        $this->joueurModel = new JoueurModel();
        verifierToken();
    }

    /**
     * GET /statistiques - Statistiques globales
     */
    public function getGlobales()
    {
        $statsGlobales = $this->joueurModel->obtenirStatistiquesJoueurs();
        $statsParJoueur = $this->joueurModel->obtenirStatistiquesParJoueur();

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Statistiques globales",
            "data" => [
                'globales' => $statsGlobales,
                'joueurs'  => $statsParJoueur
            ]
        ]);
    }

    /**
     * GET /statistiques/joueurs/{id} - Statistiques d'un joueur
     *
     * @param int $id ID du joueur
     */
    public function getJoueurStats($id)
    {
        $stats = $this->joueurModel->obtenirStatsMatchsJoueur($id);

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Stats matchs du joueur",
            "data"           => $stats
        ]);
    }

    /**
     * GET /statistiques/joueurs/{id}/performances - Performances d'un joueur
     *
     * @param int $id ID du joueur
     */
    public function getPerformances($id)
    {
        $data = $this->joueurModel->obtenirPerformancesJoueur($id);

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Performances du joueur",
            "data"           => $data
        ]);
    }

    /**
     * GET /statistiques/joueurs/{id}/derniers-matchs - Derniers matchs d'un joueur
     *
     * @param int $id ID du joueur
     */
    public function getDerniersMatchs($id)
    {
        $data = $this->joueurModel->obtenirDerniersMatchsJoueur($id);

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Derniers matchs du joueur",
            "data"           => $data
        ]);
    }

    /**
     * GET /statistiques/joueurs/{id}/selections - Selections d'un joueur
     *
     * @param int $id ID du joueur
     */
    public function getSelections($id)
    {
        $data = $this->joueurModel->obtenirStatsSelectionsJoueur($id);

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Statistiques des sélections",
            "data"           => $data
        ]);
    }

    /**
     * GET /statistiques/joueurs/{id}/poste-prefer - Poste prefere d'un joueur
     *
     * @param int $id ID du joueur
     */
    public function getPostePrefer($id)
    {
        $data = $this->joueurModel->obtenirPostePreferJoueur($id);

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Poste préféré",
            "data"           => $data
        ]);
    }

    /**
     * GET /statistiques/joueurs/{id}/selections-consecutives - Selections consecutives
     *
     * @param int $id ID du joueur
     */
    public function getSelectionsConsecutives($id)
    {
        $data = $this->joueurModel->obtenirSelectionsConsecutives($id);

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Sélections consécutives",
            "data"           => $data
        ]);
    }
}