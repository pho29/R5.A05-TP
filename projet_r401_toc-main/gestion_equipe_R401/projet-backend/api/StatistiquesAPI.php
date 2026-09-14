<?php
/**
 * =====================================================
 * FICHIER: api/StatistiquesAPI.php
 * ROLE: API REST pour les statistiques
 * METHODES:
 *   GET /StatistiquesAPI.php                              -> Statistiques globales
 *   GET /StatistiquesAPI.php/joueurs/X                    -> Stats d'un joueur
 *   GET /StatistiquesAPI.php/joueurs/X/performances       -> Performances d'un joueur
 *   GET /StatistiquesAPI.php/joueurs/X/derniers-matchs    -> Derniers matchs d'un joueur
 *   GET /StatistiquesAPI.php/joueurs/X/selections         -> Selections d'un joueur
 *   GET /StatistiquesAPI.php/joueurs/X/poste-prefer       -> Poste prefere d'un joueur
 *   GET /StatistiquesAPI.php/joueurs/X/selections-consecutives -> Selections consecutives
 * =====================================================
 */

// Configuration des en-tetes HTTP
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

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
$model = new JoueurModel();

// Recuperation de l'URI pour les parametres
$requestUri = $_SERVER['REQUEST_URI'];
$uriParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));

// Extraction des parametres depuis l'URL
$index = array_search('StatistiquesAPI.php', $uriParts);
$id = null;
$action = null;

if ($index !== false && isset($uriParts[$index + 1]) && $uriParts[$index + 1] === 'joueurs' && isset($uriParts[$index + 2]) && is_numeric($uriParts[$index + 2])) {
    $id = intval($uriParts[$index + 2]);
    $action = $uriParts[$index + 3] ?? null;
}

/**
 * Traitement des requetes GET
 */
if ($method === 'GET') {
    if ($id && $action === 'performances') {
        getPerformances($model, $id);
    } elseif ($id && $action === 'derniers-matchs') {
        getDerniersMatchs($model, $id);
    } elseif ($id && $action === 'selections') {
        getSelections($model, $id);
    } elseif ($id && $action === 'poste-prefer') {
        getPostePrefer($model, $id);
    } elseif ($id && $action === 'selections-consecutives') {
        getSelectionsConsecutives($model, $id);
    } elseif ($id) {
        getStatsMatchs($model, $id);
    } else {
        getAllStats($model);
    }
    return;
}

// Si la methode n'est pas GET, retourner 404
http_response_code(404);
echo json_encode([
    "status_code"    => 404,
    "status_message" => "Route non trouvée",
    "data"           => null
]);

/**
 * Recupere toutes les statistiques (globales et par joueur)
 */
function getAllStats($model)
{
    $statsGlobales  = $model->obtenirStatistiquesJoueurs();
    $statsParJoueur = $model->obtenirStatistiquesParJoueur();

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
 * Recupere les statistiques de matchs d'un joueur
 */
function getStatsMatchs($model, $id)
{
    $stats = $model->obtenirStatsMatchsJoueur($id);

    http_response_code(200);
    echo json_encode([
        "status_code"    => 200,
        "status_message" => "Stats matchs du joueur",
        "data"           => $stats
    ]);
}

/**
 * Recupere les performances d'un joueur
 */
function getPerformances($model, $id)
{
    $data = $model->obtenirPerformancesJoueur($id);

    http_response_code(200);
    echo json_encode([
        "status_code"    => 200,
        "status_message" => "Performances du joueur",
        "data"           => $data
    ]);
}

/**
 * Recupere les derniers matchs d'un joueur
 */
function getDerniersMatchs($model, $id)
{
    $data = $model->obtenirDerniersMatchsJoueur($id);

    http_response_code(200);
    echo json_encode([
        "status_code"    => 200,
        "status_message" => "Derniers matchs du joueur",
        "data"           => $data
    ]);
}

/**
 * Recupere les statistiques de selections d'un joueur
 */
function getSelections($model, $id)
{
    $data = $model->obtenirStatsSelectionsJoueur($id);

    http_response_code(200);
    echo json_encode([
        "status_code"    => 200,
        "status_message" => "Statistiques des sélections",
        "data"           => $data
    ]);
}

/**
 * Recupere le poste prefere d'un joueur
 */
function getPostePrefer($model, $id)
{
    $data = $model->obtenirPostePreferJoueur($id);

    http_response_code(200);
    echo json_encode([
        "status_code"    => 200,
        "status_message" => "Poste préféré",
        "data"           => $data
    ]);
}

/**
 * Recupere les selections consecutives d'un joueur
 */
function getSelectionsConsecutives($model, $id)
{
    $data = $model->obtenirSelectionsConsecutives($id);

    http_response_code(200);
    echo json_encode([
        "status_code"    => 200,
        "status_message" => "Sélections consécutives",
        "data"           => $data
    ]);
}
?>