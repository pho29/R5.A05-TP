<?php
/**
 * =====================================================
 * FICHIER: api/MatchAPI.php
 * ROLE: API REST pour la gestion des matchs
 * METHODES:
 *   GET    /MatchAPI.php                    -> Liste tous les matchs
 *   GET    /MatchAPI.php?id=1               -> Detail d'un match
 *   GET    /MatchAPI.php?statut=Termine     -> Filtre par statut
 *   GET    /MatchAPI.php?type=upcoming      -> Matchs a venir
 *   GET    /MatchAPI.php?type=past          -> Matchs passes
 *   POST   /MatchAPI.php                    -> Cree un match
 *   PUT    /MatchAPI.php?id=1               -> Modifie un match
 *   DELETE /MatchAPI.php?id=1               -> Supprime un match
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
require_once dirname(__DIR__) . '/models/Model.php';
require_once dirname(__DIR__) . '/models/MatchModel.php';
require_once dirname(__DIR__) . '/config/auth.php';

// Verification du token et instanciation du modele
$utilisateur = verifierToken();
$matchModel = new MatchModel();

// Recuperation des parametres
$method = $_SERVER['REQUEST_METHOD'];
$path = isset($_GET['id']) ? $_GET['id'] : null;

// Routage selon la methode HTTP
switch ($method) {

    /**
     * METHODE GET: Recuperation des matchs
     */
    case 'GET':
        // Cas 1: Recuperation d'un match specifique
        if ($path) {
            $match = $matchModel->getById($path);
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
        // Cas 2: Recuperation avec filtres
        else {
            if (isset($_GET['statut'])) {
                $matches = $matchModel->getByStatut($_GET['statut']);
            } elseif (isset($_GET['type']) && $_GET['type'] === 'upcoming') {
                $matches = $matchModel->getUpcoming();
            } elseif (isset($_GET['type']) && $_GET['type'] === 'past') {
                $matches = $matchModel->getPast();
            } else {
                $matches = $matchModel->getAll();
            }

            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Liste des matchs",
                "data"           => $matches
            ]);
        }
        break;

    /**
     * METHODE POST: Creation d'un match
     */
    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);

        // Verification du champ obligatoire
        if (!isset($input['equipe_adverse'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Champ requis: equipe_adverse",
                "data"           => null
            ]);
            break;
        }

        // Creation du match
        if ($matchModel->create($input)) {
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
        break;

    /**
     * METHODE PUT: Modification d'un match
     */
    case 'PUT':
        if (!$path) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID du match requis",
                "data"           => null
            ]);
            break;
        }

        $input = json_decode(file_get_contents("php://input"), true);

        if ($matchModel->update($path, $input)) {
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
        break;

    /**
     * METHODE DELETE: Suppression d'un match
     */
    case 'DELETE':
        if (!$path) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID du match requis",
                "data"           => null
            ]);
            break;
        }

        if ($matchModel->delete($path)) {
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