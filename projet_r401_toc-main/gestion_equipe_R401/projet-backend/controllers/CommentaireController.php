<?php
/**
 * =====================================================
 * FICHIER: controllers/CommentaireController.php
 * ROLE: API REST pour la gestion des commentaires (version controleur)
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
require_once dirname(__DIR__) . '/models/CommentaireModel.php';
require_once dirname(__DIR__) . '/config/auth.php';

/**
 * Classe CommentaireController
 * Gere les requetes API pour les commentaires
 */
class CommentaireController
{
    /**
     * Instance du modele Commentaire
     * @var CommentaireModel
     */
    private $commentaireModel;

    /**
     * Constructeur
     * Initialise le modele et verifie le token
     */
    public function __construct()
    {
        $this->commentaireModel = new CommentaireModel();
        verifierToken();
    }

    /**
     * GET /commentaires - Recuperer tous les commentaires
     * GET /commentaires?joueur_id={id} - Recuperer les commentaires d'un joueur
     */
    public function getAll()
    {
        if (isset($_GET['joueur_id'])) {
            $commentaires = $this->commentaireModel->getByJoueur($_GET['joueur_id']);
        } else {
            $commentaires = $this->commentaireModel->getAll();
        }

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Liste des commentaires",
            "data"           => $commentaires
        ]);
    }

    /**
     * GET /commentaires/{id} - Recuperer un commentaire specifique
     *
     * @param int $id ID du commentaire
     */
    public function getById($id)
    {
        $commentaire = $this->commentaireModel->getById($id);

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

    /**
     * POST /commentaires - Creer un commentaire
     */
    public function create()
    {
        $input = json_decode(file_get_contents("php://input"), true);

        // Validation des donnees
        if (!$input) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Données invalides",
                "data"           => null
            ]);
            return;
        }

        if (!isset($input['id_joueur']) || empty($input['id_joueur'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Champ requis: id_joueur",
                "data"           => null
            ]);
            return;
        }

        if (!isset($input['commentaire']) || empty($input['commentaire'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Champ requis: commentaire",
                "data"           => null
            ]);
            return;
        }

        // Creation du commentaire
        if ($this->commentaireModel->create($input)) {
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
    }

    /**
     * PUT /commentaires/{id} - Modifier un commentaire
     *
     * @param int $id ID du commentaire
     */
    public function update($id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID du commentaire requis",
                "data"           => null
            ]);
            return;
        }

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

        if (!isset($input['commentaire']) || empty($input['commentaire'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Champ requis: commentaire",
                "data"           => null
            ]);
            return;
        }

        // Verification que le commentaire existe
        $commentaireExistant = $this->commentaireModel->getById($id);
        if (!$commentaireExistant) {
            http_response_code(404);
            echo json_encode([
                "status_code"    => 404,
                "status_message" => "Commentaire non trouvé",
                "data"           => null
            ]);
            return;
        }

        // Mise a jour
        if ($this->commentaireModel->update($id, $input)) {
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
    }

    /**
     * DELETE /commentaires/{id} - Supprimer un commentaire
     *
     * @param int $id ID du commentaire
     */
    public function delete($id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID du commentaire requis",
                "data"           => null
            ]);
            return;
        }

        // Verification que le commentaire existe
        $commentaireExistant = $this->commentaireModel->getById($id);
        if (!$commentaireExistant) {
            http_response_code(404);
            echo json_encode([
                "status_code"    => 404,
                "status_message" => "Commentaire non trouvé",
                "data"           => null
            ]);
            return;
        }

        // Suppression
        if ($this->commentaireModel->delete($id)) {
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
    }
}