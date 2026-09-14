<?php
/**
 * =====================================================
 * FICHIER: auth.php
 * ROLE: API REST d'authentification
 * ENDPOINTS:
 *   POST /auth.php  -> Connexion (email + mot de passe) -> retourne JWT
 *   GET  /auth.php  -> Validation du token JWT
 * =====================================================
 */

/** Configuration des en-tetes HTTP */
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

/**
 * Gestion des requetes OPTIONS (pre-flight CORS)
 * Necessaire pour les appels AJAX cross-origin
 */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/** Inclusion des fichiers necessaires */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/connexion-bddauth.php';
require_once __DIR__ . '/securite/jwt_utils.php';

/** Recuperation de la methode HTTP */
$method = $_SERVER['REQUEST_METHOD'];

/**
 * Recherche un utilisateur par son email dans la base de donnees
 * @param PDO $db Connexion PDO
 * @param string $email Email de l'utilisateur
 * @return array|false Donnees utilisateur ou false si non trouve
 */
function getUserByEmail($db, $email)
{
    $sql = "SELECT 
                id_entraineur as id,
                email_entraineur as email,
                mot_de_passe_entraineur as password,
                nom_entraineur as nom,
                prenom_entraineur as prenom,
                role_entraineur as role
            FROM entraineur
            WHERE email_entraineur = :email AND actif = 1";

    $stmt = $db->prepare($sql);
    $stmt->execute(['email' => $email]);
    return $stmt->fetch();
}

/**
 * Recherche un utilisateur par son ID dans la base de donnees
 * @param PDO $db Connexion PDO
 * @param int $id ID de l'utilisateur
 * @return array|false Donnees utilisateur ou false si non trouve
 */
function getUserById($db, $id)
{
    $sql = "SELECT
                id_entraineur as id,
                email_entraineur as email,
                nom_entraineur as nom,
                prenom_entraineur as prenom,
                role_entraineur as role
            FROM entraineur
            WHERE id_entraineur = :id AND actif = 1";

    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

/** Connexion a la base de donnees d'authentification */
$db = ConnexionBaseDeDonneesAuthentification::obtenirInstance()->obtenirConnexion();

/** Routage selon la methode HTTP */
switch ($method) {

    /**
     * =====================================================
     * METHODE POST: Connexion utilisateur
     * =====================================================
     */
    case 'POST':
        /** Lecture et decodage des donnees JSON envoyees */
        $input = json_decode(file_get_contents('php://input'), true);

        /** Verification de la presence de l'email */
        if (!isset($input['email']) || empty($input['email'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Email manquant",
                "data"           => null
            ]);
            break;
        }

        /** Verification de la presence du mot de passe */
        if (!isset($input['password']) || empty($input['password'])) {
            http_response_code(400);
            echo json_encode([
                "status_code"    => 400,
                "status_message" => "Mot de passe manquant",
                "data"           => null
            ]);
            break;
        }

        /** Recherche de l'utilisateur par email */
        $user = getUserByEmail($db, $input['email']);

        /**
         * Verification des identifiants:
         * - L'utilisateur existe
         * - Le mot de passe correspond (hashage)
         */
        if (!$user || !password_verify($input['password'], $user['password'])) {
            http_response_code(401);
            echo json_encode([
                "status_code"    => 401,
                "status_message" => "Identifiants incorrects",
                "data"           => null
            ]);
            break;
        }

        /** Construction du payload du JWT */
        $payload = [
            'id'    => $user['id'],
            'email' => $user['email'],
            'nom'   => $user['nom'],
            'prenom' => $user['prenom'],
            'role'  => $user['role'],
            'exp'   => time() + JWT_EXPIRATION
        ];

        /** Generation du token JWT */
        $headers = [];
        $token = generate_jwt($headers, $payload, JWT_SECRET);

        /** Reponse de succes avec le token et les infos utilisateur */
        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Connexion reussie",
            "data"           => [
                "token"       => $token,
                "utilisateur" => [
                    "id"      => $user['id'],
                    "email"   => $user['email'],
                    "nom"     => $user['nom'],
                    "prenom"  => $user['prenom'],
                    "role"    => $user['role']
                ]
            ]
        ]);
        break;

    /**
     * =====================================================
     * METHODE GET: Validation du token JWT
     * =====================================================
     */
    case 'GET':
        /** Recuperation du token depuis l'en-tete Authorization */
        $token = get_bearer_token();

        /** Verification que le token est present */
        if (!$token) {
            http_response_code(401);
            echo json_encode([
                "status_code"    => 401,
                "status_message" => "Token manquant",
                "data"           => null
            ]);
            break;
        }

        /** Verification de la validite du token (signature + expiration) */
        if (!is_jwt_valid($token, JWT_SECRET)) {
            http_response_code(401);
            echo json_encode([
                "status_code"    => 401,
                "status_message" => "Token invalide ou expire",
                "data"           => null
            ]);
            break;
        }

        /** Decodage du payload pour recuperer l'ID utilisateur */
        $tokenParts = explode('.', $token);
        $payload = json_decode(base64url_decode($tokenParts[1]), true);

        /** Recherche de l'utilisateur en base de donnees */
        $user = getUserById($db, $payload['id']);

        /** Verification que l'utilisateur existe encore (actif) */
        if (!$user) {
            http_response_code(401);
            echo json_encode([
                "status_code"    => 401,
                "status_message" => "Utilisateur non trouve",
                "data"           => null
            ]);
            break;
        }

        /** Token valide -> retour des informations utilisateur */
        http_response_code(200);
        echo json_encode([
            "status_code"    => 200,
            "status_message" => "Token valide",
            "data"           => [
                "id"      => $user['id'],
                "email"   => $user['email'],
                "nom"     => $user['nom'],
                "prenom"  => $user['prenom'],
                "role"    => $user['role']
            ]
        ]);
        break;

    /**
     * =====================================================
     * METHODE NON AUTORISEE
     * =====================================================
     */
    default:
        http_response_code(405);
        echo json_encode([
            "status_code"    => 405,
            "status_message" => "Methode non autorisee",
            "data"           => null
        ]);
        break;
}
?>