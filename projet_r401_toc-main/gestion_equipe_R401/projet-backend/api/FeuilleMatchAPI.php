<?php
/**
 * =====================================================
 * FICHIER: api/FeuilleMatchAPI.php
 * ROLE: API REST pour la gestion des feuilles de match (participations)
 * METHODES:
 *   GET    /FeuilleMatchAPI.php                    -> Liste toutes les participations
 *   GET    /FeuilleMatchAPI.php?id=X               -> Detail d'une participation
 *   GET    /FeuilleMatchAPI.php?match_id=X         -> Participations d'un match
 *   POST   /FeuilleMatchAPI.php                    -> Cree une participation
 *   POST   /FeuilleMatchAPI.php?action=set         -> Enregistre une composition complete
 *   PUT    /FeuilleMatchAPI.php?id=X               -> Modifie une participation
 *   DELETE /FeuilleMatchAPI.php?id=X               -> Supprime une participation
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
if (!class_exists('Model')) {
    require_once dirname(__DIR__) . '/models/Model.php';
}
require_once dirname(__DIR__) . '/models/ParticipationModel.php';
require_once dirname(__DIR__) . '/config/auth.php';

// Instanciation du modele et verification du token
$model = new ParticipationModel();
$utilisateur = verifierToken();

// Recuperation des parametres
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;
$matchId = isset($_GET['match_id']) ? $_GET['match_id'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Routage selon la methode HTTP
switch ($method) {

    /**
     * METHODE GET: Recuperation des participations
     */
    case 'GET':
        // Cas 1: Recuperation d'une participation specifique
        if ($id) {
            $row = $model->getById($id);
            if ($row) {
                http_response_code(200);
                echo json_encode([
                    "status_code"    => 200,
                    "status_message" => "Participation trouvée",
                    "data"           => $row
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
        // Cas 2: Recuperation des participations d'un match
        elseif ($matchId) {
            $rows = $model->getByMatch($matchId);
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Participations du match",
                "data"           => $rows
            ]);
        }
        // Cas 3: Recuperation de toutes les participations
        else {
            $rows = $model->getAll();
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Toutes les participations",
                "data"           => $rows
            ]);
        }
        break;

    /**
     * METHODE POST: Creation de participations
     */
    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);

        // Cas 1: Enregistrement en masse avec transaction
        if ($action === 'set') {
            if (!isset($input['id_match']) || !isset($input['participants']) || !is_array($input['participants'])) {
                http_response_code(400);
                echo json_encode([
                    "status_code"    => 400,
                    "status_message" => "id_match et participants requis",
                    "data"           => null
                ]);
                break;
            }

            $ok = $model->setParticipations($input['id_match'], $input['participants']);
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
            break;
        }

        // Cas 2: Insertion unitaire
        if (!isset($input['id_match']) || !isset($input['id_joueur'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "id_match et id_joueur requis",
                "data"           => null
            ]);
            break;
        }

        if ($model->create($input)) {
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
        break;

    /**
     * METHODE PUT: Modification d'une participation
     */
    case 'PUT':
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID requis",
                "data"           => null
            ]);
            break;
        }

        $input = json_decode(file_get_contents("php://input"), true);
        if ($model->update($id, $input)) {
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Participation modifiée",
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
        break;

    /**
     * METHODE DELETE: Suppression d'une participation
     */
    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID requis",
                "data"           => null
            ]);
            break;
        }

        if ($model->delete($id)) {
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
        break;

    default:
        http_response_code(405);
        echo json_encode([
            "status_code"    => 405,
            "status_message" => "Méthode non autorisée",
            "data"           => null
        ]);
        break;
}
?>