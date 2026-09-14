<?php
/**
 * =====================================================
 * FICHIER: controllers/ParticipationController.php
 * ROLE: API REST pour la gestion des participations (feuilles de match)
 * METHODES: getAll, getById, create, update, delete
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
require_once dirname(__DIR__) . '/models/ParticipationModel.php';
require_once dirname(__DIR__) . '/config/auth.php';

/**
 * Classe ParticipationController
 * Gere les requetes API pour les participations
 */
class ParticipationController
{
    /**
     * Instance du modele Participation
     * @var ParticipationModel
     */
    private $participationModel;

    /**
     * Constructeur
     * Initialise le modele et verifie le token
     */
    public function __construct()
    {
        $this->participationModel = new ParticipationModel();
        verifierToken();
    }

    /**
     * GET /feuilles-match - Recuperer toutes les participations
     * GET /feuilles-match?match_id={id} - Participations d'un match
     */
    public function getAll()
    {
        if (isset($_GET['match_id'])) {
            $participations = $this->participationModel->getByMatch($_GET['match_id']);
        } else {
            $participations = $this->participationModel->getAll();
        }

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Liste des participations",
            "data"           => $participations
        ]);
    }

    /**
     * GET /feuilles-match/{id} - Recuperer une participation specifique
     *
     * @param int $id ID de la participation
     */
    public function getById($id)
    {
        $participation = $this->participationModel->getById($id);

        if ($participation) {
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Participation trouvée",
                "data"           => $participation
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "status_code"    => 404,
                "status_message" => "Participation non trouvée",
                "data"           => null
            ]);
        }
    }

    /**
     * POST /feuilles-match - Creer une participation
     * POST /feuilles-match?action=set - Enregistrement en masse
     */
    public function create()
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Données invalides",
                "data"           => null
            ]);
            return;
        }

        // Enregistrement en masse avec transaction
        if (isset($_GET['action']) && $_GET['action'] === 'set') {
            if (!isset($input['id_match']) || !isset($input['participants']) || !is_array($input['participants'])) {
                http_response_code(400);
                echo json_encode([
                    "status_code"    => 400,
                    "status_message" => "id_match et participants requis",
                    "data"           => null
                ]);
                return;
            }

            $ok = $this->participationModel->setParticipations($input['id_match'], $input['participants']);
            if ($ok) {
                http_response_code(201);
                echo json_encode([
                    "status_code"    => 201,
                    "status_message" => "Composition enregistrée avec succès",
                    "data"           => null
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "status_code"    => 500,
                    "status_message" => "Erreur lors de l'enregistrement",
                    "data"           => null
                ]);
            }
            return;
        }

        // Insertion unitaire
        if (!isset($input['id_match']) || !isset($input['id_joueur'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "id_match et id_joueur requis",
                "data"           => null
            ]);
            return;
        }

        if ($this->participationModel->create($input)) {
            http_response_code(201);
            echo json_encode([
                "status_code"    => 201,
                "status_message" => "Participation créée",
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
     * PUT /feuilles-match/{id} - Modifier une participation
     *
     * @param int $id ID de la participation
     */
    public function update($id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID requis",
                "data"           => null
            ]);
            return;
        }

        $input = json_decode(file_get_contents("php://input"), true);

        // Logs de debogage
        error_log("=== ParticipationController::update ===");
        error_log("ID reçu: " . $id);
        error_log("Input reçu: " . print_r($input, true));

        if (!$input) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Données invalides",
                "data"           => null
            ]);
            return;
        }

        $result = $this->participationModel->update($id, $input);

        if ($result) {
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Participation mise à jour",
                "data"           => $input
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status_code"    => 500,
                "status_message" => "Erreur lors de la mise à jour",
                "data"           => null
            ]);
        }
    }

    /**
     * DELETE /feuilles-match/{id} - Supprimer une participation
     *
     * @param int $id ID de la participation
     */
    public function delete($id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID requis",
                "data"           => null
            ]);
            return;
        }

        if ($this->participationModel->delete($id)) {
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Participation supprimée",
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