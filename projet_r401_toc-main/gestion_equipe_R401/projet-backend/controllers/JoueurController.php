<?php
/**
 * =====================================================
 * FICHIER: controllers/JoueurController.php
 * ROLE: API REST pour la gestion des joueurs (version controleur)
 * METHODES: getAll, getActifs, getBlesses, getById, create, update, delete
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
require_once dirname(__DIR__) . '/models/JoueurModel.php';
require_once dirname(__DIR__) . '/config/auth.php';

/**
 * Classe JoueurController
 * Gere les requetes API pour les joueurs
 */
class JoueurController
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
     * GET /joueurs - Recuperer tous les joueurs
     * GET /joueurs?statut=Actif - Filtrer par statut
     * GET /joueurs?licence=LIC001 - Verifier une licence
     */
    public function getAll()
    {
        // Verification de licence
        if (isset($_GET['licence'])) {
            $licence = $_GET['licence'];
            $joueur = $this->joueurModel->getByLicence($licence);

            if ($joueur) {
                http_response_code(200);
                echo json_encode([
                    "status_code"    => 200,
                    "status_message" => "Licence existe déjà",
                    "data"           => [$joueur]
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    "status_code"    => 200,
                    "status_message" => "Licence disponible",
                    "data"           => []
                ]);
            }
            return;
        }

        // Filtrage par statut ou liste complete
        if (isset($_GET['statut'])) {
            $joueurs = $this->joueurModel->getByStatut($_GET['statut']);
        } else {
            $joueurs = $this->joueurModel->getAll();
        }

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Liste des joueurs",
            "data"           => $joueurs
        ]);
    }

    /**
     * GET /joueurs/actifs - Recuperer les joueurs actifs
     */
    public function getActifs()
    {
        $joueurs = $this->joueurModel->getActifs();

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Joueurs actifs",
            "data"           => $joueurs
        ]);
    }

    /**
     * GET /joueurs/blesses - Recuperer les joueurs blesses
     */
    public function getBlesses()
    {
        $joueurs = $this->joueurModel->getBlesses();

        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Joueurs blessés",
            "data"           => $joueurs
        ]);
    }

    /**
     * GET /joueurs/{id} - Recuperer un joueur specifique
     *
     * @param int $id ID du joueur
     */
    public function getById($id)
    {
        $joueur = $this->joueurModel->getById($id);

        if ($joueur) {
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Joueur trouvé",
                "data"           => $joueur
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "status_code"    => 404,
                "status_message" => "Joueur non trouvé",
                "data"           => null
            ]);
        }
    }

    /**
     * POST /joueurs - Creer un joueur
     */
    public function create()
    {
        $input = json_decode(file_get_contents("php://input"), true);

        // Validation des champs obligatoires
        $champsObligatoires = [
            'numero_licence', 'nom_joueur', 'prenom_joueur',
            'date_naissance', 'taille_cm', 'poids_kg', 'statut_joueur'
        ];

        foreach ($champsObligatoires as $champ) {
            if (!isset($input[$champ]) || empty($input[$champ])) {
                http_response_code(400);
                echo json_encode([
                    "status_code"    => 400,
                    "status_message" => "Champ requis: $champ",
                    "data"           => null
                ]);
                return;
            }
        }

        // Validation du format du numero de licence
        if (!preg_match('/^LIC[0-9]{3}$/i', $input['numero_licence'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Le numéro de licence doit être au format LIC001, LIC002, etc. (LIC + 3 chiffres)",
                "data"           => null
            ]);
            return;
        }

        // Verification de l'unicite du numero de licence
        $licenceExistante = $this->joueurModel->getByLicence($input['numero_licence']);
        if ($licenceExistante) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Ce numéro de licence existe déjà. Veuillez utiliser un autre numéro.",
                "data"           => null
            ]);
            return;
        }

        // Creation du joueur
        if ($this->joueurModel->create($input)) {
            http_response_code(201);
            echo json_encode([
                "status_code"    => 201,
                "status_message" => "Joueur créé avec succès",
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
     * PUT /joueurs/{id} - Modifier un joueur
     *
     * @param int $id ID du joueur
     */
    public function update($id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID du joueur requis",
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

        // Verification que le joueur existe
        $joueurExistant = $this->joueurModel->getById($id);
        if (!$joueurExistant) {
            http_response_code(404);
            echo json_encode([
                "status_code"    => 404,
                "status_message" => "Joueur non trouvé",
                "data"           => null
            ]);
            return;
        }

        // Verification du numero de licence si modifie
        if (isset($input['numero_licence']) && $input['numero_licence'] !== $joueurExistant['numero_licence']) {
            // Validation du format
            if (!preg_match('/^LIC[0-9]{3}$/i', $input['numero_licence'])) {
                http_response_code(400);
                echo json_encode([
                    "status_code"    => 400,
                    "status_message" => "Le numéro de licence doit être au format LIC001, LIC002, etc. (LIC + 3 chiffres)",
                    "data"           => null
                ]);
                return;
            }

            // Verification de l'unicite
            $licenceExistante = $this->joueurModel->getByLicence($input['numero_licence']);
            if ($licenceExistante && $licenceExistante['id_joueur'] != $id) {
                http_response_code(400);
                echo json_encode([
                    "status_code"    => 400,
                    "status_message" => "Ce numéro de licence existe déjà. Veuillez utiliser un autre numéro.",
                    "data"           => null
                ]);
                return;
            }
        }

        // Fusion des donnees
        $donneesModifiees = array_merge($joueurExistant, $input);

        // Mise a jour
        if ($this->joueurModel->update($id, $donneesModifiees)) {
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Joueur modifié avec succès",
                "data"           => $donneesModifiees
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
     * DELETE /joueurs/{id} - Supprimer un joueur
     *
     * @param int $id ID du joueur
     */
    public function delete($id)
    {
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID du joueur requis",
                "data"           => null
            ]);
            return;
        }

        // Verification que le joueur existe
        $joueurExistant = $this->joueurModel->getById($id);
        if (!$joueurExistant) {
            http_response_code(404);
            echo json_encode([
                "status_code"    => 404,
                "status_message" => "Joueur non trouvé",
                "data"           => null
            ]);
            return;
        }

        // Suppression
        if ($this->joueurModel->delete($id)) {
            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Joueur supprimé avec succès",
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