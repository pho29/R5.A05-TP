<?php
/**
 * =====================================================
 * FICHIER: controllers/CommentaireController.php
 * ROLE: Controleur pour la gestion des commentaires (frontend)
 * ACTIONS: index, ajouter, modifier, supprimer
 * =====================================================
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../securite/authentification.php';
require_once __DIR__ . '/JoueurController.php';

/**
 * Classe CommentaireController
 * Gere les interactions utilisateur pour les commentaires
 */
class CommentaireController
{
    /**
     * URL de l'API des commentaires
     * @var string
     */
    private $apiUrl;

    /**
     * URL de l'API d'authentification
     * @var string
     */
    private $authApiUrl;

    /**
     * Constructeur
     * Verifie l'authentification et initialise les URLs
     */
    public function __construct()
    {
        $this->apiUrl = API_COMMENTAIRES;
        $this->authApiUrl = URL_API_AUTH;

        // Verification de la connexion
        if (!Authentification::estConnecte()) {
            header('Location: /projet_r401_toc/gestion_equipe_R401/projet-frontend/connexion.php');
            exit;
        }

        // Generation d'un token CSRF si inexistant
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Appelle l'API des commentaires
     *
     * @param string $method Methode HTTP (GET, POST, PUT, DELETE)
     * @param array|null $data Donnees a envoyer
     * @param int|null $id ID du commentaire
     * @param array $params Parametres GET supplementaires
     * @return array Reponse de l'API
     */
    private function appelApi($method, $data = null, $id = null, $params = [])
    {
        $url = $this->apiUrl;

        // Construction des parametres GET
        $queryParams = [];
        if ($id !== null) {
            $queryParams['id'] = $id;
        }
        if (isset($params['joueur_id'])) {
            $queryParams['joueur_id'] = $params['joueur_id'];
        }

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        // Configuration de CURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $headers = [
            'Authorization: Bearer ' . $_SESSION['token'],
            'Content-Type: application/json'
        ];

        if ($data !== null) {
            $jsonData = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            $headers[] = 'Content-Length: ' . strlen($jsonData);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Gestion des erreurs
        if ($curlError) {
            error_log("Curl error: " . $curlError);
            return [
                'code' => 500,
                'data' => ['status_message' => 'Erreur de connexion']
            ];
        }

        $decodedResponse = json_decode($response, true);

        return [
            'code' => $httpCode,
            'data' => $decodedResponse
        ];
    }

    /**
     * Action: index
     * Affiche la liste des commentaires d'un joueur
     */
    public function index()
    {
        $idJoueur = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$idJoueur) {
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        // Recuperation des informations du joueur
        $joueurController = new JoueurController();
        $resultatJoueur = $joueurController->getJoueurById($idJoueur);

        $joueur = null;
        if ($resultatJoueur['code'] === 200 && isset($resultatJoueur['data']['data'])) {
            $joueur = $resultatJoueur['data']['data'];
        }

        if (!$joueur) {
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        // Recuperation des commentaires
        $resultat = $this->appelApi('GET', null, null, ['joueur_id' => $idJoueur]);
        $commentaires = [];

        if ($resultat['code'] === 200 && isset($resultat['data']['data'])) {
            $commentaires = $resultat['data']['data'];
        }

        $titrePage = 'Commentaires du joueur';
        $descriptionPage = 'Gérer les commentaires pour ' . htmlspecialchars($joueur['prenom_joueur'] . ' ' . $joueur['nom_joueur']);

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/joueurs/commentaires/index.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h2>Erreur : Fichier de vue non trouvé</h2>";
            echo "<p>Chemin recherché : " . $viewPath . "</p>";
            exit;
        }
    }

    /**
     * Action: ajouter
     * Ajoute un nouveau commentaire pour un joueur
     */
    public function ajouter()
    {
        $idJoueur = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$idJoueur) {
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        // Recuperation des informations du joueur
        $joueurController = new JoueurController();
        $resultatJoueur = $joueurController->getJoueurById($idJoueur);

        $joueur = null;
        if ($resultatJoueur['code'] === 200 && isset($resultatJoueur['data']['data'])) {
            $joueur = $resultatJoueur['data']['data'];
        }

        if (!$joueur) {
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        $messageErreur = '';
        $messageSucces = '';

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verification du token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $messageErreur = "Token de sécurité invalide";
            } else {
                $data = [
                    'id_joueur'  => $idJoueur,
                    'commentaire' => trim($_POST['commentaire'] ?? '')
                ];

                if (empty($data['commentaire'])) {
                    $messageErreur = "Le commentaire ne peut pas être vide";
                } else {
                    $resultat = $this->appelApi('POST', $data);

                    if ($resultat['code'] === 201) {
                        $messageSucces = "Commentaire ajouté avec succès";
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        header('Location: index.php?controller=commentaire&action=index&id=' . $idJoueur . '&succes=1');
                        exit;
                    } else {
                        $messageErreur = $resultat['data']['status_message'] ?? "Erreur lors de l'ajout";
                    }
                }
            }
        }

        $titrePage = 'Ajouter un commentaire';
        $descriptionPage = 'Ajouter un commentaire pour ' . htmlspecialchars($joueur['prenom_joueur'] . ' ' . $joueur['nom_joueur']);

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/joueurs/commentaires/ajouter.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Fichier de vue non trouvé : " . $viewPath;
        }
    }

    /**
     * Action: modifier
     * Modifie un commentaire existant
     */
    public function modifier()
    {
        $idCommentaire = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$idCommentaire) {
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        // Recuperation du commentaire
        $resultat = $this->appelApi('GET', null, $idCommentaire);
        $commentaire = null;

        if ($resultat['code'] === 200 && isset($resultat['data']['data'])) {
            $commentaire = $resultat['data']['data'];
        }

        if (!$commentaire) {
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        $idJoueur = $commentaire['id_joueur'];

        // Recuperation des informations du joueur
        $joueurController = new JoueurController();
        $resultatJoueur = $joueurController->getJoueurById($idJoueur);

        $joueur = null;
        if ($resultatJoueur['code'] === 200 && isset($resultatJoueur['data']['data'])) {
            $joueur = $resultatJoueur['data']['data'];
        }

        $messageErreur = '';
        $messageSucces = '';

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $messageErreur = "Token de sécurité invalide";
            } else {
                $data = [
                    'commentaire' => trim($_POST['commentaire'] ?? '')
                ];

                if (empty($data['commentaire'])) {
                    $messageErreur = "Le commentaire ne peut pas être vide";
                } else {
                    $resultat = $this->appelApi('PUT', $data, $idCommentaire);

                    if ($resultat['code'] === 200) {
                        $messageSucces = "Commentaire modifié avec succès";
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        header('Location: index.php?controller=commentaire&action=index&id=' . $idJoueur . '&succes=1');
                        exit;
                    } else {
                        $messageErreur = $resultat['data']['status_message'] ?? "Erreur lors de la modification";
                    }
                }
            }
        }

        $titrePage = 'Modifier un commentaire';
        $descriptionPage = 'Modifier le commentaire pour ' . htmlspecialchars($joueur['prenom_joueur'] . ' ' . $joueur['nom_joueur']);

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/joueurs/commentaires/modifier.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Fichier de vue non trouvé : " . $viewPath;
            exit;
        }
    }

    /**
     * Action: supprimer
     * Supprime un commentaire
     */
    public function supprimer()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        // Verification du token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['erreur_suppression'] = "Token de sécurité invalide";
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        $idCommentaire = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $idJoueur = isset($_POST['id_joueur']) ? intval($_POST['id_joueur']) : 0;

        if (!$idCommentaire) {
            $_SESSION['erreur_suppression'] = "ID du commentaire manquant";
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        // Suppression via l'API
        $resultat = $this->appelApi('DELETE', null, $idCommentaire);

        if ($resultat['code'] === 200) {
            $_SESSION['succes_suppression'] = "Commentaire supprimé avec succès";
        } else {
            $_SESSION['erreur_suppression'] = $resultat['data']['status_message'] ?? "Erreur lors de la suppression";
        }

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header('Location: index.php?controller=commentaire&action=index&id=' . $idJoueur);
        exit;
    }
}
?>