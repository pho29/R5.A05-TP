<?php
/**
 * =====================================================
 * FICHIER: controllers/MatchController.php
 * ROLE: API REST pour la gestion des matchs (version controleur)
 * METHODES: getAll, getUpcoming, getPast, getById, create, update, delete
 * =====================================================
 */

// Configuration des en-tetes HTTP
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Gestion des requetes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Inclusion des dependances
require_once dirname(__DIR__) . '/models/MatchModel.php';
require_once dirname(__DIR__) . '/config/auth.php';

/**
 * Classe MatchController
 * Gere les requetes API pour les matchs
 */
class MatchController
{
    /**
     * Instance du modele Match
     * @var MatchModel
     */
    private $matchModel;

    /**
     * Constructeur
     * Initialise le modele et verifie le token
     */
    public function __construct()
    {
        $this->matchModel = new MatchModel();
        verifierToken();
    }

    /**
     * GET /matchs - Recuperer tous les matchs
     * GET /matchs?statut=Termine - Filtrer par statut
     */
    public function getAll()
    {
        if (isset($_GET['statut'])) {
            $matchs = $this->matchModel->getByStatut($_GET['statut']);
        } else {
            $matchs = $this->matchModel->getAll();
        }

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Liste des matchs",
            "data"           => $matchs
        ]);
    }

    /**
     * GET /matchs/upcoming - Recuperer les matchs a venir
     */
    public function getUpcoming()
    {
        $matchs = $this->matchModel->getUpcoming();

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Matchs à venir",
            "data"           => $matchs
        ]);
    }

    /**
     * GET /matchs/past - Recuperer les matchs passes
     */
    public function getPast()
    {
        $matchs = $this->matchModel->getPast();

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Matchs passés",
            "data"           => $matchs
        ]);
    }

    /**
     * GET /matchs/{id} - Recuperer un match specifique
     *
     * @param int $id ID du match
     */
    public function getById($id)
    {
        $match = $this->matchModel->getById($id);

        if ($match) {
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Match trouvé",
                "data"           => $match
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "status_code"    => 404,
                "status_message" => "Match non trouvé",
                "data"           => null
            ]);
        }
    }

    /**
     * POST /matchs - Creer un match
     */
    public function create()
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['equipe_adverse']) || empty($input['equipe_adverse'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Champ requis: equipe_adverse",
                "data"           => null
            ]);
            return;
        }

        if ($this->matchModel->create($input)) {
            http_response_code(201);
            echo json_encode([
                "status_code"    => 201,
                "status_message" => "Match créé avec succès",
                "data"           => $input
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status_code"    => 500,
                "status_message" => "Erreur lors de la création",
                "data"           => null
            ]);
        }
    }

    /**
     * PUT /matchs/{id} - Modifier un match
     *
     * @param int $id ID du match
     */
    public function update($id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID du match requis",
                "data"           => null
            ]);
            return;
        }

        $input = json_decode(file_get_contents("php://input"), true);

        if ($this->matchModel->update($id, $input)) {
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Match modifié avec succès",
                "data"           => $input
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status_code"    => 500,
                "status_message" => "Erreur lors de la modification",
                "data"           => null
            ]);
        }
    }

    /**
     * DELETE /matchs/{id} - Supprimer un match
     *
     * @param int $id ID du match
     */
    public function delete($id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID du match requis",
                "data"           => null
            ]);
            return;
        }

        if ($this->matchModel->delete($id)) {
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Match supprimé avec succès",
                "data"           => null
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status_code"    => 500,
                "status_message" => "Erreur lors de la suppression",
                "data"           => null
            ]);
        }
    }
}