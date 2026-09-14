<?php
/**
 * =====================================================
 * FICHIER: controllers/JoueurController.php
 * ROLE: Controleur pour la gestion des joueurs (frontend)
 * ACTIONS: index, ajouter, modifier, details, supprimer
 * =====================================================
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../securite/authentification.php';

/**
 * Classe JoueurController
 * Gere les interactions utilisateur pour les joueurs
 */
class JoueurController
{
    /**
     * URL de l'API des joueurs
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
        $this->apiUrl = API_JOUEURS;
        $this->authApiUrl = URL_API_AUTH;

        if (!Authentification::estConnecte()) {
            header('Location: /projet_r401_toc/gestion_equipe_R401/projet-frontend/connexion.php');
            exit;
        }
    }

    /**
     * Verifie la validite du token aupres de l'API d'authentification
     */
    private function verifierAuthentification()
    {
        if (!isset($_SESSION['token'])) {
            header('Location: /projet_r401_toc/gestion_equipe_R401/projet-frontend/connexion.php');
            exit;
        }

        $token = $_SESSION['token'];

        $ch = curl_init($this->authApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode !== 200) {
            session_destroy();
            header('Location: /projet_r401_toc/gestion_equipe_R401/projet-frontend/connexion.php');
            exit;
        }

        $userData = json_decode($response, true);
        if (isset($userData['data'])) {
            $_SESSION['utilisateur'] = $userData['data'];
        }
    }

    /**
     * Appelle l'API des joueurs
     *
     * @param string $method Methode HTTP
     * @param array|null $data Donnees a envoyer
     * @param int|null $id ID du joueur
     * @return array Reponse de l'API
     */
    public function appelApi($method, $data = null, $id = null)
    {
        $url = $this->apiUrl;
        if ($id !== null) {
            $url .= '?id=' . $id;
        }

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

        if ($curlError) {
            error_log("Curl error: " . $curlError);
            return [
                'code' => 500,
                'data' => ['status_message' => 'Erreur de connexion']
            ];
        }

        $decodedResponse = json_decode($response, true);

        if ($decodedResponse === null) {
            return [
                'code' => $httpCode,
                'data' => ['status_message' => 'Réponse invalide']
            ];
        }

        return [
            'code' => $httpCode,
            'data' => $decodedResponse
        ];
    }

    /**
     * Recupere un joueur par son ID
     *
     * @param int $id ID du joueur
     * @return array Reponse de l'API
     */
    public function getJoueurById($id)
    {
        return $this->appelApi('GET', null, $id);
    }

    /**
     * Extrait les donnees de la reponse API
     *
     * @param array $resultat Reponse de l'API
     * @return array|null Donnees extraites ou null
     */
    private function extraireDonnees($resultat)
    {
        if ($resultat['code'] !== 200) {
            return null;
        }

        $data = $resultat['data'];

        if (is_array($data) && isset($data['data'])) {
            return $data['data'];
        }

        if (is_array($data) && isset($data[0]['id_joueur'])) {
            return $data;
        }

        if (is_array($data) && isset($data['id_joueur'])) {
            return $data;
        }

        if (is_array($data) && count($data) > 0 && !isset($data['status_code'])) {
            return $data;
        }

        return null;
    }

    /**
     * Action: index
     * Affiche la liste des joueurs
     */
    public function index()
    {
        $resultat = $this->appelApi('GET');

        $joueurs = [];
        $messageErreur = null;
        $messageSucces = null;

        // Recuperation des messages de session
        if (isset($_SESSION['succes_suppression'])) {
            $messageSucces = $_SESSION['succes_suppression'];
            unset($_SESSION['succes_suppression']);
        }

        if (isset($_SESSION['erreur_suppression'])) {
            $messageErreur = $_SESSION['erreur_suppression'];
            unset($_SESSION['erreur_suppression']);
        }

        if (isset($_GET['succes'])) {
            $messageSucces = "Opération réussie !";
        }

        // Traitement de la reponse
        if ($resultat['code'] === 200) {
            $response = $resultat['data'];

            if (isset($response['data']) && is_array($response['data'])) {
                $joueurs = $response['data'];
            } elseif (is_array($response) && isset($response[0]['id_joueur'])) {
                $joueurs = $response;
            } elseif (is_array($response) && isset($response['id_joueur'])) {
                $joueurs = [$response];
            } else {
                $messageErreur = "Format de réponse inattendu";
            }
        } else {
            $messageErreur = $resultat['data']['status_message'] ?? "Impossible de charger les joueurs";
        }

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/joueurs/gestion-joueurs.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h2>Erreur : Fichier de vue non trouvé</h2>";
            echo "<p>Chemin recherché : " . $viewPath . "</p>";
        }
    }

    /**
     * Action: ajouter
     * Ajoute un nouveau joueur
     */
    public function ajouter()
    {
        $donneesFormulaire = [
            'nom_joueur'          => '',
            'prenom_joueur'       => '',
            'date_naissance'      => '',
            'numero_licence'      => '',
            'statut_joueur'       => 'Actif',
            'taille_cm'           => '',
            'poids_kg'            => '',
            'commentaires_joueur' => ''
        ];

        $messageErreur = null;
        $dateMax = date('Y-m-d', strtotime('-16 years'));
        $dateMin = date('Y-m-d', strtotime('-50 years'));

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $erreurs = $this->validerDonneesJoueur($_POST);

            if (empty($erreurs)) {
                $data = [
                    'numero_licence'      => trim($_POST['numero_licence']),
                    'nom_joueur'          => trim($_POST['nom_joueur']),
                    'prenom_joueur'       => trim($_POST['prenom_joueur']),
                    'date_naissance'      => $_POST['date_naissance'],
                    'taille_cm'           => floatval($_POST['taille_cm']),
                    'poids_kg'            => floatval($_POST['poids_kg']),
                    'statut_joueur'       => $_POST['statut_joueur'],
                    'commentaires_joueur' => trim($_POST['commentaires_joueur'] ?? '')
                ];

                $resultat = $this->appelApi('POST', $data);

                if ($resultat['code'] === 201 || $resultat['code'] === 200) {
                    header('Location: index.php?controller=joueur&action=index&succes=1');
                    exit;
                } else {
                    $messageErreur = $resultat['data']['status_message'] ?? "Erreur lors de l'ajout";
                    $donneesFormulaire = $_POST;
                }
            } else {
                $messageErreur = implode('<br>', $erreurs);
                $donneesFormulaire = $_POST;
            }
        }

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/joueurs/ajouter-joueur.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h2>Erreur : Fichier de vue non trouvé</h2>";
            echo "<p>Chemin recherché : " . $viewPath . "</p>";
        }
    }

    /**
     * Action: modifier
     * Modifie un joueur existant
     */
    public function modifier()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$id) {
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        $messageErreur = null;

        // Recuperation du joueur
        $resultat = $this->appelApi('GET', null, $id);

        if ($resultat['code'] === 200) {
            $joueur = $this->extraireDonnees($resultat);

            if ($joueur === null) {
                $messageErreur = "Format de réponse inattendu";
            } elseif (!is_array($joueur)) {
                $messageErreur = "Joueur non trouvé";
            }
        } else {
            $messageErreur = $resultat['data']['status_message'] ?? "Joueur non trouvé";
        }

        $donneesFormulaire = isset($joueur) && is_array($joueur) ? $joueur : [
            'nom_joueur'          => '',
            'prenom_joueur'       => '',
            'date_naissance'      => '',
            'numero_licence'      => '',
            'statut_joueur'       => 'Actif',
            'taille_cm'           => '',
            'poids_kg'            => '',
            'commentaires_joueur' => ''
        ];

        $dateMax = date('Y-m-d', strtotime('-16 years'));
        $dateMin = date('Y-m-d', strtotime('-50 years'));

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$messageErreur) {
            $erreurs = $this->validerDonneesJoueur($_POST);

            if (empty($erreurs)) {
                $data = [
                    'numero_licence'      => trim($_POST['numero_licence']),
                    'nom_joueur'          => trim($_POST['nom_joueur']),
                    'prenom_joueur'       => trim($_POST['prenom_joueur']),
                    'date_naissance'      => $_POST['date_naissance'],
                    'taille_cm'           => floatval($_POST['taille_cm']),
                    'poids_kg'            => floatval($_POST['poids_kg']),
                    'statut_joueur'       => $_POST['statut_joueur'],
                    'commentaires_joueur' => trim($_POST['commentaires_joueur'] ?? '')
                ];

                $resultat = $this->appelApi('PUT', $data, $id);

                if ($resultat['code'] === 200) {
                    header('Location: index.php?controller=joueur&action=index&succes=1');
                    exit;
                } else {
                    $messageErreur = $resultat['data']['status_message'] ?? "Erreur lors de la modification";
                    $donneesFormulaire = $_POST;
                }
            } else {
                $messageErreur = implode('<br>', $erreurs);
                $donneesFormulaire = $_POST;
            }
        }

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/joueurs/modifier-joueur.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h2>Erreur : Fichier de vue non trouvé</h2>";
            echo "<p>Chemin recherché : " . $viewPath . "</p>";
        }
    }

    /**
     * Action: details
     * Affiche les details d'un joueur
     */
    public function details()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$id) {
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        $resultat = $this->appelApi('GET', null, $id);

        if ($resultat['code'] === 200) {
            $joueur = $this->extraireDonnees($resultat);

            if ($joueur === null || !is_array($joueur)) {
                header('Location: index.php?controller=joueur&action=index');
                exit;
            }

            // Calcul de l'age
            $dateNaissance = new DateTime($joueur['date_naissance']);
            $aujourdhui = new DateTime();
            $age = $dateNaissance->diff($aujourdhui)->y;

            // Initialisation des variables de statistiques
            $totalMatchs = 0;
            $statsMatchs = ['victoires' => 0, 'defaites' => 0, 'nuls' => 0];
            $pourcentageVictoires = 0;
            $pourcentageDefaites = 0;
            $pourcentageNuls = 0;
            $totalSelections = 0;
            $titularisations = 0;
            $remplacements = 0;
            $pourcentageTitulaire = 0;
            $pourcentageRemplacant = 0;
            $performances = ['total_tirs_cadres' => 0, 'total_tirs_non_cadres' => 0];
            $totalTirs = 0;
            $precisionTirs = 0;
            $derniersMatchs = [];
        } else {
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/joueurs/details-joueur.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h2>Erreur : Fichier de vue non trouvé</h2>";
            echo "<p>Chemin recherché : " . $viewPath . "</p>";
        }
    }

    /**
     * Action: verifierParticipations
     * Verifie si un joueur a des participations (AJAX)
     */
    public function verifierParticipations()
    {
        header('Content-Type: application/json');

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo json_encode(['nbParticipations' => 0, 'error' => 'ID manquant']);
            exit;
        }

        $idJoueur = intval($_GET['id']);

        // Appel a l'API des feuilles de match
        $apiParticipationsUrl = API_FEUILLES_MATCH . '?match_id=all';

        $ch = curl_init($apiParticipationsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $_SESSION['token'],
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $nbParticipations = 0;

        if ($httpCode === 200) {
            $decoded = json_decode($response, true);
            if (isset($decoded['data']) && is_array($decoded['data'])) {
                foreach ($decoded['data'] as $participation) {
                    if (isset($participation['id_joueur']) && $participation['id_joueur'] == $idJoueur) {
                        $nbParticipations++;
                    }
                }
            }
        }

        echo json_encode(['nbParticipations' => $nbParticipations]);
        exit;
    }

    /**
     * Action: supprimer
     * Supprime un joueur
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

        if (!isset($_POST['id_joueur']) || empty($_POST['id_joueur'])) {
            $_SESSION['erreur_suppression'] = "ID du joueur manquant";
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        $idJoueur = intval($_POST['id_joueur']);

        // Verification des participations avant suppression
        $apiParticipationsUrl = API_FEUILLES_MATCH;
        $ch = curl_init($apiParticipationsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $_SESSION['token'],
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $nbParticipations = 0;

        if ($httpCode === 200) {
            $decoded = json_decode($response, true);
            if (isset($decoded['data']) && is_array($decoded['data'])) {
                foreach ($decoded['data'] as $participation) {
                    if (isset($participation['id_joueur']) && $participation['id_joueur'] == $idJoueur) {
                        $nbParticipations++;
                    }
                }
            }
        }

        if ($nbParticipations > 0) {
            $_SESSION['erreur_suppression'] = "Impossible de supprimer ce joueur car il a déjà participé à " . $nbParticipations . " match(s).";
            header('Location: index.php?controller=joueur&action=index');
            exit;
        }

        // Suppression du joueur
        $resultat = $this->appelApi('DELETE', null, $idJoueur);

        if ($resultat['code'] === 200) {
            $_SESSION['succes_suppression'] = "Joueur supprimé avec succès";
        } else {
            $_SESSION['erreur_suppression'] = $resultat['data']['status_message'] ?? "Erreur lors de la suppression";
        }

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header('Location: index.php?controller=joueur&action=index');
        exit;
    }

    /**
     * Recupere tous les joueurs (format simplifie)
     *
     * @return array Liste des joueurs
     */
    public function getAll()
    {
        $resultat = $this->appelApi('GET');

        if ($resultat['code'] === 200 && isset($resultat['data']['data'])) {
            $joueurs = [];
            foreach ($resultat['data']['data'] as $j) {
                $joueurs[] = [
                    'id_joueur'          => $j['id_joueur'],
                    'numero_licence'     => $j['numero_licence'],
                    'nom'                => $j['nom_joueur'],
                    'prenom'             => $j['prenom_joueur'],
                    'date_naissance'     => $j['date_naissance'],
                    'taille'             => $j['taille_cm'],
                    'poids'              => $j['poids_kg'],
                    'statut'             => $j['statut_joueur'],
                    'commentaires_joueur' => $j['commentaires_joueur'] ?? ''
                ];
            }
            return $joueurs;
        }
        return [];
    }

    /**
     * Recupere l'historique d'un joueur
     *
     * @param int $id_joueur ID du joueur
     * @return array Historique du joueur
     */
    public function getHistoriqueJoueur($id_joueur)
    {
        $resultat = $this->appelApi('GET', null, $id_joueur);

        if ($resultat['code'] === 200 && isset($resultat['data']['data'])) {
            $joueur = $resultat['data']['data'];
            return [
                'nb_matchs'         => 0,
                'commentaire_general' => $joueur['commentaires_joueur'] ?? '',
                'derniers_matchs'   => []
            ];
        }

        return [
            'nb_matchs'         => 0,
            'commentaire_general' => '',
            'derniers_matchs'   => []
        ];
    }

    /**
     * Valide les donnees d'un joueur
     *
     * @param array $data Donnees a valider
     * @return array Liste des erreurs
     */
    private function validerDonneesJoueur($data)
    {
        $erreurs = [];

        // Validation du numero de licence
        if (empty($data['numero_licence'])) {
            $erreurs[] = "Le numéro de licence est obligatoire";
        } elseif (!preg_match('/^LIC[0-9]{3}$/i', $data['numero_licence'])) {
            $erreurs[] = "Le numéro de licence doit être au format LIC001, LIC002, etc. (LIC + 3 chiffres)";
        }

        // Validation du nom
        if (empty($data['nom_joueur'])) {
            $erreurs[] = "Le nom est obligatoire";
        } elseif (strlen($data['nom_joueur']) < 2) {
            $erreurs[] = "Le nom doit contenir au moins 2 caractères";
        }

        // Validation du prenom
        if (empty($data['prenom_joueur'])) {
            $erreurs[] = "Le prénom est obligatoire";
        } elseif (strlen($data['prenom_joueur']) < 2) {
            $erreurs[] = "Le prénom doit contenir au moins 2 caractères";
        }

        // Validation de la date de naissance
        if (empty($data['date_naissance'])) {
            $erreurs[] = "La date de naissance est obligatoire";
        } else {
            try {
                $dateNaissance = new DateTime($data['date_naissance']);
                $aujourdhui = new DateTime();
                $age = $dateNaissance->diff($aujourdhui)->y;

                if ($age < 16) {
                    $erreurs[] = "Le joueur doit avoir au moins 16 ans";
                } elseif ($age > 50) {
                    $erreurs[] = "Le joueur ne doit pas avoir plus de 50 ans";
                }
            } catch (Exception $e) {
                $erreurs[] = "Format de date invalide";
            }
        }

        // Validation de la taille
        if (empty($data['taille_cm'])) {
            $erreurs[] = "La taille est obligatoire";
        } elseif (!is_numeric($data['taille_cm']) || $data['taille_cm'] < 100 || $data['taille_cm'] > 250) {
            $erreurs[] = "La taille doit être comprise entre 100 cm et 250 cm";
        }

        // Validation du poids
        if (empty($data['poids_kg'])) {
            $erreurs[] = "Le poids est obligatoire";
        } elseif (!is_numeric($data['poids_kg']) || $data['poids_kg'] < 30 || $data['poids_kg'] > 150) {
            $erreurs[] = "Le poids doit être compris entre 30 kg et 150 kg";
        }

        // Validation du statut
        $statutsValides = ['Actif', 'Blessé', 'Suspendu', 'Absent'];
        if (empty($data['statut_joueur']) || !in_array($data['statut_joueur'], $statutsValides)) {
            $erreurs[] = "Le statut du joueur est invalide";
        }

        return $erreurs;
    }
}
?>