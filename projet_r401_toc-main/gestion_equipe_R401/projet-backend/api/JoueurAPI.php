<?php
/**
 * =====================================================
 * FICHIER: api/JoueurAPI.php
 * ROLE: API REST pour la gestion des joueurs
 * METHODES:
 *   GET    /JoueurAPI.php                    -> Liste tous les joueurs
 *   GET    /JoueurAPI.php?id=1               -> Detail d'un joueur
 *   GET    /JoueurAPI.php?statut=Actif       -> Filtre par statut
 *   GET    /JoueurAPI.php?type=actifs        -> Liste des joueurs actifs
 *   GET    /JoueurAPI.php?type=blesses       -> Liste des joueurs blesses
 *   GET    /JoueurAPI.php?licence=LIC001     -> Verifie si une licence existe
 *   POST   /JoueurAPI.php                    -> Cree un joueur
 *   PUT    /JoueurAPI.php?id=1               -> Modifie un joueur
 *   DELETE /JoueurAPI.php?id=1               -> Supprime un joueur
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
require_once dirname(__DIR__) . '/models/JoueurModel.php';
require_once dirname(__DIR__) . '/config/auth.php';

// Verification du token et instanciation du modele
$utilisateur = verifierToken();
$joueurModel = new JoueurModel();

// Recuperation des parametres
$method = $_SERVER['REQUEST_METHOD'];
$path = isset($_GET['id']) ? $_GET['id'] : null;

// Routage selon la methode HTTP
switch ($method) {

    /**
     * METHODE GET: Recuperation des joueurs
     */
    case 'GET':
        // Cas 1: Verification d'une licence (doit etre avant les autres conditions)
        if (isset($_GET['licence'])) {
            $licence = $_GET['licence'];
            $joueur = $joueurModel->getByLicence($licence);

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
            break;
        }

        // Cas 2: Recuperation d'un joueur specifique
        if ($path) {
            $joueur = $joueurModel->getById($path);
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
        // Cas 3: Recuperation avec filtres
        else {
            if (isset($_GET['statut'])) {
                $joueurs = $joueurModel->getByStatut($_GET['statut']);
            } elseif (isset($_GET['type']) && $_GET['type'] === 'actifs') {
                $joueurs = $joueurModel->getActifs();
            } elseif (isset($_GET['type']) && $_GET['type'] === 'blesses') {
                $joueurs = $joueurModel->getBlesses();
            } else {
                $joueurs = $joueurModel->getAll();
            }

            http_response_code(200);
            echo json_encode([
                "status_code"    => 200,
                "status_message" => "Liste des joueurs",
                "data"           => $joueurs
            ]);
        }
        break;

    /**
     * METHODE POST: Creation d'un joueur
     */
    case 'POST':
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
                exit;
            }
        }

        // Validation du format du numero de licence (LIC + 3 chiffres)
        if (!preg_match('/^LIC[0-9]{3}$/i', $input['numero_licence'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Le numéro de licence doit être au format LIC001, LIC002, etc. (LIC + 3 chiffres)",
                "data"           => null
            ]);
            break;
        }

        // Verification de l'unicite du numero de licence
        $licenceExistante = $joueurModel->getByLicence($input['numero_licence']);
        if ($licenceExistante) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Ce numéro de licence existe déjà. Veuillez utiliser un autre numéro.",
                "data"           => null
            ]);
            break;
        }

        // Creation du joueur
        if ($joueurModel->create($input)) {
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
        break;

    /**
     * METHODE PUT: Modification d'un joueur
     */
    case 'PUT':
        if (!$path) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID du joueur requis",
                "data"           => null
            ]);
            break;
        }

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

        // Verification que le joueur existe
        $joueurExistant = $joueurModel->getById($path);
        if (!$joueurExistant) {
            http_response_code(404);
            echo json_encode([
                "status_code"    => 404,
                "status_message" => "Joueur non trouvé",
                "data"           => null
            ]);
            break;
        }

        // Si le numero de licence est modifie, verifier son format et son unicite
        if (isset($input['numero_licence']) && $input['numero_licence'] !== $joueurExistant['numero_licence']) {
            if (!preg_match('/^LIC[0-9]{3}$/i', $input['numero_licence'])) {
                http_response_code(400);
                echo json_encode([
                    "status_code"    => 400,
                    "status_message" => "Le numéro de licence doit être au format LIC001, LIC002, etc. (LIC + 3 chiffres)",
                    "data"           => null
                ]);
                break;
            }

            $licenceExistante = $joueurModel->getByLicence($input['numero_licence']);
            if ($licenceExistante && $licenceExistante['id_joueur'] != $path) {
                http_response_code(400);
                echo json_encode([
                    "status_code"    => 400,
                    "status_message" => "Ce numéro de licence existe déjà. Veuillez utiliser un autre numéro.",
                    "data"           => null
                ]);
                break;
            }
        }

        // Fusion des donnees existantes avec les nouvelles
        $donneesModifiees = array_merge($joueurExistant, $input);

        if ($joueurModel->update($path, $donneesModifiees)) {
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
        break;

    /**
     * METHODE DELETE: Suppression d'un joueur
     */
    case 'DELETE':
        if (!$path) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "ID du joueur requis",
                "data"           => null
            ]);
            break;
        }

        // Verification que le joueur existe
        $joueurExistant = $joueurModel->getById($path);
        if (!$joueurExistant) {
            http_response_code(404);
            echo json_encode([
                "status_code"    => 404,
                "status_message" => "Joueur non trouvé",
                "data"           => null
            ]);
            break;
        }

        // Suppression du joueur
        if ($joueurModel->delete($path)) {
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