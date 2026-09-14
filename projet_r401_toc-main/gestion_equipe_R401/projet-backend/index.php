<?php
// projet-backend/index.php - Routeur principal

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Autoloader
spl_autoload_register(function ($className) {
    $paths = [
        __DIR__ . '/controllers/' . $className . '.php',
        __DIR__ . '/models/' . $className . '.php',
        __DIR__ . '/config/' . $className . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Récupération de l'URI
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = dirname($scriptName);
$path = str_replace($basePath, '', $requestUri);
$path = parse_url($path, PHP_URL_PATH);
$path = trim($path, '/');

// Séparer les segments
$segments = explode('/', $path);
$resource = $segments[0] ?? '';
$id = isset($segments[1]) && is_numeric($segments[1]) ? (int)$segments[1] : null;
$action = $segments[2] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

// Si l'ID n'est pas dans le path, chercher dans $_GET
if ($id === null && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
}

// Log pour déboguer
error_log("=== ROUTEUR ===");
error_log("Resource: " . $resource);
error_log("Method: " . $method);
error_log("ID from path: " . ($segments[1] ?? 'null'));
error_log("ID from GET: " . ($_GET['id'] ?? 'null'));
error_log("Final ID: " . ($id ?? 'null'));
error_log("Action: " . ($action ?? 'null'));

// Router vers le bon contrôleur
try {
    switch ($resource) {
        case 'joueurs':
            $controller = new JoueurController();
            
            if ($method === 'GET') {
                if ($id) {
                    $controller->getById($id);
                } elseif ($action === 'actifs') {
                    $controller->getActifs();
                } elseif ($action === 'blesses') {
                    $controller->getBlesses();
                } elseif (isset($_GET['statut'])) {
                    $controller->getByStatut($_GET['statut']);
                } elseif (isset($_GET['licence'])) {
                    $controller->checkLicence($_GET['licence']);
                } else {
                    $controller->getAll();
                }
            } elseif ($method === 'POST') {
                $controller->create();
            } elseif ($method === 'PUT') {
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(["status_message" => "ID requis"]);
                    exit;
                }
                $controller->update($id);
            } elseif ($method === 'DELETE') {
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(["status_message" => "ID requis"]);
                    exit;
                }
                $controller->delete($id);
            }
            break;
            
        case 'matchs':
            $controller = new MatchController();
            
            if ($method === 'GET') {
                if ($id) {
                    $controller->getById($id);
                } elseif ($action === 'upcoming') {
                    $controller->getUpcoming();
                } elseif ($action === 'past') {
                    $controller->getPast();
                } elseif (isset($_GET['statut'])) {
                    $controller->getByStatut($_GET['statut']);
                } else {
                    $controller->getAll();
                }
            } elseif ($method === 'POST') {
                $controller->create();
            } elseif ($method === 'PUT') {
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(["status_message" => "ID requis"]);
                    exit;
                }
                $controller->update($id);
            } elseif ($method === 'DELETE') {
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(["status_message" => "ID requis"]);
                    exit;
                }
                $controller->delete($id);
            }
            break;
            
        case 'feuilles-match':
        case 'participations':
            $controller = new ParticipationController();
            
            if ($method === 'GET') {
                if ($id) {
                    $controller->getById($id);
                } else {
                    $controller->getAll();
                }
            } elseif ($method === 'POST') {
                $controller->create();
            } elseif ($method === 'PUT') {
                if (!$id) {
                    http_response_code(400);
                    echo json_encode([
                        "status_code" => 400,
                        "status_message" => "ID requis pour la mise à jour",
                        "data" => null
                    ]);
                    exit;
                }
                error_log("Appel update avec ID: " . $id);
                $controller->update($id);
            } elseif ($method === 'DELETE') {
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(["status_message" => "ID requis"]);
                    exit;
                }
                $controller->delete($id);
            }
            break;
            
        case 'commentaires':
            $controller = new CommentaireController();
            
            if ($method === 'GET') {
                if ($id) {
                    $controller->getById($id);
                } else {
                    $controller->getAll();
                }
            } elseif ($method === 'POST') {
                $controller->create();
            } elseif ($method === 'PUT') {
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(["status_message" => "ID requis"]);
                    exit;
                }
                $controller->update($id);
            } elseif ($method === 'DELETE') {
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(["status_message" => "ID requis"]);
                    exit;
                }
                $controller->delete($id);
            }
            break;
            
        case 'statistiques':
            $controller = new StatistiqueController();
            
            if ($method === 'GET') {
                if ($id && $action === 'performances') {
                    $controller->getPerformances($id);
                } elseif ($id && $action === 'derniers-matchs') {
                    $controller->getDerniersMatchs($id);
                } elseif ($id && $action === 'selections') {
                    $controller->getSelections($id);
                } elseif ($id && $action === 'poste-prefer') {
                    $controller->getPostePrefer($id);
                } elseif ($id && $action === 'selections-consecutives') {
                    $controller->getSelectionsConsecutives($id);
                } elseif ($id) {
                    $controller->getJoueurStats($id);
                } else {
                    $controller->getGlobales();
                }
            }
            break;
            
        case 'auth':
            $controller = new AuthController();
            
            if ($method === 'POST' && $action === 'login') {
                $controller->login();
            } elseif ($method === 'GET' && $action === 'validate') {
                $controller->validate();
            } else {
                http_response_code(404);
                echo json_encode([
                    "status_code" => 404,
                    "status_message" => "Endpoint non trouvé",
                    "data" => null
                ]);
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                "status_code" => 404,
                "status_message" => "Ressource non trouvée: " . $resource,
                "data" => null
            ]);
            break;
    }
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "status_code" => 500,
        "status_message" => "Erreur serveur: " . $e->getMessage(),
        "data" => null
    ]);
}