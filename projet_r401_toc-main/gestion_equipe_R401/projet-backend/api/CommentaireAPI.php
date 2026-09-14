<?php
/**
 * =====================================================
 * FICHIER: api/CommentaireAPI.php
 * ROLE: API REST pour la gestion des commentaires
 * METHODES:
 *   GET    /CommentaireAPI.php           -> Liste tous les commentaires
 *   GET    /CommentaireAPI.php?id=X      -> Detail d'un commentaire
 *   GET    /CommentaireAPI.php?joueur_id=X -> Commentaires d'un joueur
 *   POST   /CommentaireAPI.php           -> Cree un commentaire
 *   PUT    /CommentaireAPI.php?id=X      -> Modifie un commentaire
 *   DELETE /CommentaireAPI.php?id=X      -> Supprime un commentaire
 * =====================================================
 */

// Configuration des en-tetes HTTP pour l'API
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Gestion des requetes OPTIONS (pre-flight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Inclusion des dependances
require_once dirname(__DIR__) . '/models/CommentaireModel.php';
require_once dirname(__DIR__) . '/config/auth.php';

// Instanciation du modele et verification du token
$commentaireModel = new CommentaireModel();
$utilisateur = verifierToken();

// Recuperation des parametres de la requete
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;
$joueurId = isset($_GET['joueur_id']) ? $_GET['joueur_id'] : null;


// Routage selon la methode HTTP
switch ($method) {

    /**
     * METHODE GET: Recuperation des commentaires
     */
    case 'GET':
        // Cas 1: Recuperation d'un commentaire specifique par ID
        if ($id) {
            $commentaire = $commentaireModel->getById($id);
            if ($commentaire) {
                http_response_code(200);
                echo json_encode([
                    "status_code"    => 200,
                    "status_message" => "Commentaire trouvé",
                    "data"           => $commentaire
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "status_code"    => 404,
                    "status_message" => "Commentaire non trouvé",
                    "data"           => null
                ]);
            }
        }
        // Cas 2: Recuperation des commentaires d'un joueur specifique
        elseif ($joueurId) {
            $commentaires = $commentaireModel->getByJoueur($joueurId);
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Liste des commentaires du joueur",
                "data"           => $commentaires
            ]);
        }
        // Cas 3: Recuperation de tous les commentaires
        else {
            $commentaires = $commentaireModel->getAll();
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Liste des commentaires",
                "data"           => $commentaires
            ]);
        }
        break;

    /**
     * METHODE POST: Creation d'un commentaire
     */
    case 'POST':
        // Lecture et decodage des donnees JSON
        $input = json_decode(file_get_contents("php://input"), true);

        error_log("POST input: " . print_r($input, true));

        // Validation des donnees
        if (!$input) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Données invalides",
                "data"           => null
            ]);
            break;
        }

        // Verification du champ id_joueur
        if (!isset($input['id_joueur']) || empty($input['id_joueur'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Champ requis: id_joueur",
                "data"           => null
            ]);
            break;
        }

        // Verification du champ commentaire
        if (!isset($input['commentaire']) || empty($input['commentaire'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Champ requis: commentaire",
                "data"           => null
            ]);
            break;
        }

        // Creation du commentaire
        if ($commentaireModel->create($input)) {
            http_response_code(201);
            echo json_encode([
                "status_code"    => 201,
                "status_message" => "Commentaire créé avec succès",
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
     * METHODE PUT: Modification d'un commentaire
     */
    case 'PUT':
        // Verification de l'ID
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID du commentaire requis",
                "data"           => null
            ]);
            break;
        }

        // Lecture des donnees
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Données invalides",
                "data"           => null
            ]);
            break;
        }

        // Verification du champ commentaire
        if (!isset($input['commentaire']) || empty($input['commentaire'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Champ requis: commentaire",
                "data"           => null
            ]);
            break;
        }

        // Verification que le commentaire existe
        $commentaireExistant = $commentaireModel->getById($id);
        if (!$commentaireExistant) {
            http_response_code(404);
            echo json_encode([
                "status_code"    => 404,
                "status_message" => "Commentaire non trouvé",
                "data"           => null
            ]);
            break;
        }

        // Mise a jour du commentaire
        if ($commentaireModel->update($id, $input)) {
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Commentaire modifié avec succès",
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
     * METHODE DELETE: Suppression d'un commentaire
     */
    case 'DELETE':
        // Verification de l'ID
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID du commentaire requis",
                "data"           => null
            ]);
            break;
        }

        // Verification que le commentaire existe
        $commentaireExistant = $commentaireModel->getById($id);
        if (!$commentaireExistant) {
            http_response_code(404);
            echo json_encode([
                "status_code"    => 404,
                "status_message" => "Commentaire non trouvé",
                "data"           => null
            ]);
            break;
        }

        // Suppression du commentaire
        if ($commentaireModel->delete($id)) {
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Commentaire supprimé avec succès",
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

    /**
     * METHODE NON AUTORISEE
     */
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