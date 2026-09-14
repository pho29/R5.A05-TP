<?php
/**
 * =====================================================
 * FICHIER: controllers/MatchController.php
 * ROLE: Controleur pour la gestion des matchs (frontend)
 * ACTIONS: index, ajouter, modifier, details, composer, resultat, supprimer, evaluer
 * =====================================================
 */

require_once dirname(__DIR__) . '/config/config.php';

/**
 * Classe MatchController
 * Gere les interactions utilisateur pour les matchs
 */
class MatchController
{
    /**
     * URL de l'API des matchs
     * @var string
     */
    private $apiUrl;

    /**
     * URL de l'API des joueurs
     * @var string
     */
    private $apiJoueursUrl;

    /**
     * URL de l'API des feuilles de match
     * @var string
     */
    private $apiFeuillesUrl;

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
        $this->apiUrl = API_MATCHS;
        $this->apiJoueursUrl = API_JOUEURS;
        $this->apiFeuillesUrl = API_FEUILLES_MATCH;
        $this->authApiUrl = URL_API_AUTH;

        $this->verifierAuthentification();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Verifie la validite du token
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

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
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
     * Appelle l'API des matchs
     */
    private function appelApi($method, $data = null, $id = null)
    {
        $url = $this->apiUrl;
        if ($id !== null) {
            $url .= '?id=' . $id;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

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

        if (curl_error($ch)) {
            error_log('Curl error: ' . curl_error($ch));
            curl_close($ch);
            return [
                'code' => 500,
                'data' => ['status_message' => 'Erreur de connexion à l\'API']
            ];
        }

        curl_close($ch);
        $decodedResponse = json_decode($response, true);

        return [
            'code' => $httpCode,
            'data' => $decodedResponse
        ];
    }

    /**
     * Appelle l'API des joueurs
     */
    private function appelApiJoueurs($method, $data = null, $id = null)
    {
        $url = $this->apiJoueursUrl;
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
            error_log('Curl error appelApiJoueurs: ' . $curlError);
            return [
                'code' => 500,
                'data' => ['status_message' => 'Erreur de connexion à l\'API']
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
     * Appelle l'API des feuilles de match
     */
    private function appelApiFeuilles($method, $data = null, $id = null, $matchId = null)
    {
        $url = $this->apiFeuillesUrl;

        // Format RESTful: /feuilles-match/123 pour PUT/DELETE
        if ($id !== null) {
            $url .= '/' . $id;
        } elseif ($matchId !== null) {
            $url .= '?match_id=' . $matchId;
        }

        error_log("=== appelApiFeuilles ===");
        error_log("Method: " . $method);
        error_log("URL: " . $url);
        error_log("ID: " . $id);
        error_log("Data: " . json_encode($data));

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
            error_log('Curl error appelApiFeuilles: ' . $curlError);
            return [
                'code' => 500,
                'data' => ['status_message' => 'Erreur de connexion à l\'API']
            ];
        }

        error_log("Response: " . $response);
        error_log("HTTP Code: " . $httpCode);

        $decodedResponse = json_decode($response, true);

        if ($decodedResponse === null) {
            return [
                'code' => $httpCode,
                'data' => ['status_message' => 'Réponse invalide: ' . $response]
            ];
        }

        return [
            'code' => $httpCode,
            'data' => $decodedResponse
        ];
    }

    /**
     * Action: index
     * Affiche la liste des matchs
     */
    public function index()
    {
        $titrePage = 'Gestion des Matchs';
        $descriptionPage = 'Gérer les matchs de l\'équipe';

        $messageSucces = '';
        $messageErreur = '';

        // Recuperation des messages de session
        if (isset($_SESSION['succes_suppression'])) {
            $messageSucces = $_SESSION['succes_suppression'];
            unset($_SESSION['succes_suppression']);
        }

        if (isset($_SESSION['erreur_suppression'])) {
            $messageErreur = $_SESSION['erreur_suppression'];
            unset($_SESSION['erreur_suppression']);
        }

        if (isset($_SESSION['erreur_modification'])) {
            $messageErreur = $_SESSION['erreur_modification'];
            unset($_SESSION['erreur_modification']);
        }

        if (isset($_SESSION['erreur_saisie'])) {
            $messageErreur = $_SESSION['erreur_saisie'];
            unset($_SESSION['erreur_saisie']);
        }

        if (isset($_GET['succes'])) {
            $messageSucces = 'Opération effectuée avec succès !';
        }

        // Recuperation des matchs
        $resultat = $this->appelApi('GET');

        $tousMatchs = [];
        if ($resultat['code'] === 200 && isset($resultat['data']['data'])) {
            $tousMatchs = $resultat['data']['data'];
        } else {
            $messageErreur = $resultat['data']['status_message'] ?? "Impossible de charger les matchs";
        }

        $maintenant = new DateTime();

        // Classification des matchs
        $matchsAVenir = [];
        $matchsSansResultat = [];
        $matchsAvecResultat = [];

        foreach ($tousMatchs as $match) {
            $aResultat = ($match['statut_match'] ?? '') === 'Terminé' || in_array($match['resultat_match'] ?? '', ['Victoire', 'Défaite', 'Nul']);

            $dateMatch = null;
            try {
                if (!empty($match['date_heure_match']) && $match['date_heure_match'] != '0000-00-00 00:00:00') {
                    $dateMatch = new DateTime($match['date_heure_match']);
                }
            } catch (Exception $e) {}

            if (!$dateMatch) {
                $matchsAVenir[] = $match;
                continue;
            }

            if ($aResultat) {
                $matchsAvecResultat[] = $match;
            } elseif ($dateMatch < $maintenant) {
                $matchsSansResultat[] = $match;
            } else {
                $matchsAVenir[] = $match;
            }
        }

        // Tri des matchs
        usort($matchsAVenir, function($a, $b) {
            $dateA = new DateTime($a['date_heure_match']);
            $dateB = new DateTime($b['date_heure_match']);
            return $dateA <=> $dateB;
        });

        usort($matchsSansResultat, function($a, $b) {
            $dateA = new DateTime($a['date_heure_match']);
            $dateB = new DateTime($b['date_heure_match']);
            return $dateB <=> $dateA;
        });

        usort($matchsAvecResultat, function($a, $b) {
            $dateA = new DateTime($a['date_heure_match']);
            $dateB = new DateTime($b['date_heure_match']);
            return $dateB <=> $dateA;
        });

        // Statistiques globales
        $statsGlobales = [
            'total_matchs'   => count($tousMatchs),
            'victoires'      => count(array_filter($tousMatchs, function($m) { return ($m['resultat_match'] ?? '') == 'Victoire'; })),
            'defaites'       => count(array_filter($tousMatchs, function($m) { return ($m['resultat_match'] ?? '') == 'Défaite'; })),
            'nuls'           => count(array_filter($tousMatchs, function($m) { return ($m['resultat_match'] ?? '') == 'Nul'; })),
            'a_venir'        => count($matchsAVenir),
            'sans_resultat'  => count($matchsSansResultat),
            'passes'         => count($matchsAvecResultat),
            'domicile'       => count(array_filter($tousMatchs, function($m) { return ($m['lieu_match'] ?? '') == 'Domicile'; })),
            'exterieur'      => count(array_filter($tousMatchs, function($m) { return ($m['lieu_match'] ?? '') == 'Extérieur'; })),
            'buts_pour'      => array_sum(array_column($tousMatchs, 'score_equipe')),
            'buts_contre'    => array_sum(array_column($tousMatchs, 'score_adverse'))
        ];

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/matchs/gestion-matchs.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Fichier de vue non trouvé : " . $viewPath;
        }
    }

    /**
     * Action: ajouter
     * Ajoute un nouveau match
     */
    public function ajouter()
    {
        $titrePage = 'Ajouter un Match';
        $descriptionPage = 'Planifier un nouveau match';

        $messageSucces = '';
        $messageErreur = '';

        $donneesFormulaire = [
            'adversaire'        => '',
            'date_match'        => '',
            'heure_match'       => '',
            'type_match'        => 'Domicile',
            'lieu_rencontre'    => '',
            'commentaires_match'=> '',
            'score_equipe'      => '',
            'score_adversaire'  => '',
            'resultat_match'    => 'À venir'
        ];

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $donneesFormulaire = [
                'equipe_adverse'    => trim($_POST['adversaire'] ?? ''),
                'date_match'        => trim($_POST['date_match'] ?? ''),
                'heure_match'       => trim($_POST['heure_match'] ?? ''),
                'lieu_match'        => trim($_POST['type_match'] ?? 'Domicile'),
                'lieu_rencontre'    => trim($_POST['lieu_rencontre'] ?? ''),
                'commentaires_match'=> trim($_POST['commentaires_match'] ?? ''),
                'score_equipe'      => trim($_POST['score_equipe'] ?? ''),
                'score_adverse'     => trim($_POST['score_adversaire'] ?? ''),
                'resultat_match'    => 'À venir',
                'statut_match'      => 'À venir'
            ];

            $erreurs = [];

            // Validations
            if (empty($donneesFormulaire['equipe_adverse'])) {
                $erreurs[] = 'Le nom de l\'équipe adverse est requis';
            }
            if (empty($donneesFormulaire['date_match'])) {
                $erreurs[] = 'La date du match est requise';
            }
            if (empty($donneesFormulaire['heure_match'])) {
                $erreurs[] = 'L\'heure du match est requise';
            }
            if (empty($donneesFormulaire['lieu_match'])) {
                $erreurs[] = 'Le lieu est requis';
            }
            if (empty($donneesFormulaire['lieu_rencontre'])) {
                $erreurs[] = 'Le nom du stade est requis';
            }

            // Verification de la date
            if (!empty($donneesFormulaire['date_match'])) {
                $dateMatch = new DateTime($donneesFormulaire['date_match']);
                $aujourdhui = new DateTime();
                $aujourdhui->setTime(0, 0, 0);
                if ($dateMatch < $aujourdhui && empty($donneesFormulaire['score_equipe'])) {
                    $erreurs[] = 'La date du match ne peut pas être dans le passé (sauf si vous saisissez le score)';
                }
            }

            // Calcul du resultat si scores saisis
            if ($donneesFormulaire['score_equipe'] !== '' && $donneesFormulaire['score_adverse'] !== '') {
                $scoreEquipe = intval($donneesFormulaire['score_equipe']);
                $scoreAdversaire = intval($donneesFormulaire['score_adverse']);
                if ($scoreEquipe > $scoreAdversaire) {
                    $donneesFormulaire['resultat_match'] = 'Victoire';
                    $donneesFormulaire['statut_match'] = 'Terminé';
                } elseif ($scoreEquipe < $scoreAdversaire) {
                    $donneesFormulaire['resultat_match'] = 'Défaite';
                    $donneesFormulaire['statut_match'] = 'Terminé';
                } else {
                    $donneesFormulaire['resultat_match'] = 'Nul';
                    $donneesFormulaire['statut_match'] = 'Terminé';
                }
            } else {
                $donneesFormulaire['score_equipe'] = 0;
                $donneesFormulaire['score_adverse'] = 0;
            }

            $donneesFormulaire['date_heure_match'] = $donneesFormulaire['date_match'] . ' ' . $donneesFormulaire['heure_match'] . ':00';
            unset($donneesFormulaire['date_match']);
            unset($donneesFormulaire['heure_match']);

            if (empty($erreurs)) {
                try {
                    $resultat = $this->appelApi('POST', $donneesFormulaire);
                    if ($resultat['code'] === 201) {
                        $messageSucces = 'Le match contre ' . $donneesFormulaire['equipe_adverse'] . ' a été ajouté avec succès!';
                        echo '<script>
                            setTimeout(function() {
                                window.location.href = "index.php?controller=match&action=index&succes=1";
                            }, 2000);
                        </script>';
                    } else {
                        $messageErreur = $resultat['data']['status_message'] ?? 'Erreur lors de l\'ajout du match.';
                    }
                } catch (Exception $e) {
                    $messageErreur = 'Erreur technique: ' . $e->getMessage();
                }
            } else {
                $messageErreur = implode('<br>', $erreurs);
            }
        }

        $dateMin = date('Y-m-d', strtotime('-1 year'));
        $dateMax = date('Y-m-d', strtotime('+2 years'));

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/matchs/ajouter-match.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Fichier de vue non trouvé : " . $viewPath;
        }
    }

    /**
     * Action: modifier
     * Modifie un match existant
     */
    public function modifier()
    {
        $titrePage = 'Modifier un Match';
        $descriptionPage = 'Modifier les informations d\'un match';

        $messageSucces = '';
        $messageErreur = '';

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: index.php?controller=match&action=index');
            exit();
        }

        $idMatch = intval($_GET['id']);

        // Recuperation du match
        $resultat = $this->appelApi('GET', null, $idMatch);

        if ($resultat['code'] !== 200 || !isset($resultat['data']['data'])) {
            header('Location: index.php?controller=match&action=index');
            exit();
        }

        $match = $resultat['data']['data'];

        // Verification que le match n'est pas passe
        $dateMatchObj = new DateTime($match['date_heure_match']);
        $aujourdhui = new DateTime();
        if ($dateMatchObj < $aujourdhui) {
            $_SESSION['erreur_modification'] = "Impossible de modifier un match qui a déjà eu lieu.";
            header('Location: index.php?controller=match&action=index');
            exit();
        }

        $dateHeure = new DateTime($match['date_heure_match']);
        $dateMatch = $dateHeure->format('Y-m-d');
        $heureMatch = $dateHeure->format('H:i');

        $donneesFormulaire = [
            'adversaire'        => $match['equipe_adverse'],
            'date_match'        => $dateMatch,
            'heure_match'       => $heureMatch,
            'type_match'        => $match['lieu_match'],
            'lieu_rencontre'    => $match['lieu_rencontre'] ?? '',
            'commentaires_match'=> $match['commentaires_match'] ?? '',
            'score_equipe'      => $match['score_equipe'] ?? '',
            'score_adversaire'  => $match['score_adverse'] ?? '',
            'resultat_match'    => $match['resultat_match']
        ];

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nouvelleDate = $_POST['date_match'] ?? '';
            $nouvelleHeure = $_POST['heure_match'] ?? '';

            if (!empty($nouvelleDate) && !empty($nouvelleHeure)) {
                $nouvelleDateHeure = new DateTime($nouvelleDate . ' ' . $nouvelleHeure);
                if ($nouvelleDateHeure < $aujourdhui) {
                    $messageErreur = "La date du match ne peut pas être dans le passé.";
                } else {
                    $donneesFormulaire = [
                        'equipe_adverse'    => trim($_POST['adversaire'] ?? ''),
                        'date_match'        => trim($_POST['date_match'] ?? ''),
                        'heure_match'       => trim($_POST['heure_match'] ?? ''),
                        'lieu_match'        => trim($_POST['type_match'] ?? 'Domicile'),
                        'lieu_rencontre'    => trim($_POST['lieu_rencontre'] ?? ''),
                        'commentaires_match'=> trim($_POST['commentaires_match'] ?? ''),
                        'score_equipe'      => trim($_POST['score_equipe'] ?? ''),
                        'score_adverse'     => trim($_POST['score_adversaire'] ?? ''),
                        'resultat_match'    => 'À venir',
                        'statut_match'      => 'À venir'
                    ];

                    $erreurs = [];

                    if (empty($donneesFormulaire['equipe_adverse'])) {
                        $erreurs[] = 'le nom de l\'équipe adverse est requis';
                    }
                    if (empty($donneesFormulaire['date_match'])) {
                        $erreurs[] = 'la date du match est requise';
                    }
                    if (empty($donneesFormulaire['heure_match'])) {
                        $erreurs[] = 'l\'heure du match est requise';
                    }

                    $donneesFormulaire['date_heure_match'] = $donneesFormulaire['date_match'] . ' ' . $donneesFormulaire['heure_match'] . ':00';
                    unset($donneesFormulaire['date_match']);
                    unset($donneesFormulaire['heure_match']);

                    if (empty($erreurs)) {
                        try {
                            $resultat = $this->appelApi('PUT', $donneesFormulaire, $idMatch);
                            if ($resultat['code'] === 200) {
                                $messageSucces = 'le match a été modifié avec succès';
                                echo '<script>
                                    setTimeout(function() {
                                        window.location.href = "index.php?controller=match&action=index&succes=1";
                                    }, 2000);
                                </script>';
                            } else {
                                $messageErreur = $resultat['data']['status_message'] ?? 'erreur lors de la modification du match';
                            }
                        } catch (Exception $e) {
                            $messageErreur = 'erreur technique : ' . $e->getMessage();
                        }
                    } else {
                        $messageErreur = implode('<br>', $erreurs);
                    }
                }
            }
        }

        $dateMin = date('Y-m-d', strtotime('-1 year'));
        $dateMax = date('Y-m-d', strtotime('+2 years'));

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/matchs/modifier-match.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Fichier de vue non trouvé : " . $viewPath;
        }
    }

    /**
     * Action: details
     * Affiche les details d'un match
     */
    public function details()
    {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: index.php?controller=match&action=index');
            exit();
        }

        $idMatch = intval($_GET['id']);

        // Recuperation du match
        $resultat = $this->appelApi('GET', null, $idMatch);

        if ($resultat['code'] !== 200 || !isset($resultat['data']['data'])) {
            header('Location: index.php?controller=match&action=index');
            exit();
        }

        $match = $resultat['data']['data'];

        // Recuperation des joueurs du match
        $resultatFeuilles = $this->appelApiFeuilles('GET', null, null, $idMatch);
        $joueurs = [];
        $nbTitulaires = 0;
        $nbRemplacants = 0;
        $totalPoints = 0;
        $moyenneNote = 0;

        if ($resultatFeuilles['code'] === 200 && isset($resultatFeuilles['data']['data'])) {
            $joueurs = $resultatFeuilles['data']['data'];
            foreach ($joueurs as $joueur) {
                if (($joueur['titulaire'] ?? 0) == 1) {
                    $nbTitulaires++;
                } else {
                    $nbRemplacants++;
                }
                $totalPoints += $joueur['evaluation'] ?? 0;
            }
            if (count($joueurs) > 0) {
                $moyenneNote = round($totalPoints / count($joueurs), 1);
            }
        }

        $dateHeure = new DateTime($match['date_heure_match']);
        $dateMatch = $dateHeure->format('d/m/Y');
        $heureMatch = $dateHeure->format('H:i');

        $titrePage = 'Détails du Match';
        $descriptionPage = 'Match contre ' . $match['equipe_adverse'] . ' - ' . $dateMatch;

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/matchs/details-match.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Fichier de vue non trouvé : " . $viewPath;
        }
    }

    /**
     * Action: composer
     * Permet de composer l'equipe pour un match
     */
    public function composer()
    {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: index.php?controller=match&action=index');
            exit();
        }

        $idMatch = intval($_GET['id']);

        // Recuperation du match
        $resultat = $this->appelApi('GET', null, $idMatch);

        if ($resultat['code'] !== 200 || !isset($resultat['data']['data'])) {
            header('Location: index.php?controller=match&action=index');
            exit();
        }

        $match = $resultat['data']['data'];

        $dateHeure = new DateTime($match['date_heure_match']);
        $dateMatch = $dateHeure->format('d/m/Y à H:i');

        $titrePage = 'Composer l\'équipe';
        $descriptionPage = 'Match contre ' . $match['equipe_adverse'] . ' - ' . $dateMatch;

        $messageSucces = '';
        $messageErreur = '';

        // Recuperation des joueurs actifs
        $resultatJoueurs = $this->appelApiJoueurs('GET');
        $joueursActifs = [];

        if ($resultatJoueurs['code'] === 200 && isset($resultatJoueurs['data']['data'])) {
            $tousJoueurs = $resultatJoueurs['data']['data'];
            $joueursActifs = array_filter($tousJoueurs, function($j) {
                return isset($j['statut_joueur']) && $j['statut_joueur'] === 'Actif';
            });
            $joueursActifs = array_values($joueursActifs);
        }

        // Recuperation des selections existantes
        $resultatFeuilles = $this->appelApiFeuilles('GET', null, null, $idMatch);
        $idsSelectionnes = [];
        $statutsSelectionnes = [];
        $libellesSelectionnes = [];

        if ($resultatFeuilles['code'] === 200 && isset($resultatFeuilles['data']['data'])) {
            foreach ($resultatFeuilles['data']['data'] as $feuille) {
                $idsSelectionnes[] = $feuille['id_joueur'];
                $statutsSelectionnes[$feuille['id_joueur']] = ($feuille['titulaire'] ?? 0) == 1 ? 'Titulaire' : 'Remplaçant';
                $libellesSelectionnes[$feuille['id_joueur']] = $feuille['libelle_poste'] ?? '';
            }
        }

        $remplacantsExistants = [];
        foreach ($idsSelectionnes as $idJoueur) {
            if (($statutsSelectionnes[$idJoueur] ?? '') === 'Remplaçant') {
                $remplacantsExistants[] = $idJoueur;
            }
        }

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Suppression des anciennes participations
            $resultatParticipations = $this->appelApiFeuilles('GET', null, null, $idMatch);
            if ($resultatParticipations['code'] === 200 && isset($resultatParticipations['data']['data'])) {
                foreach ($resultatParticipations['data']['data'] as $participation) {
                    $this->appelApiFeuilles('DELETE', null, $participation['id_participation']);
                }
            }

            $postesTitulaires = ['Gardien', 'DCG', 'DCD', 'AD', 'AG', 'MD', 'MC', 'MO', 'AiD', 'AiG', 'AC'];
            $succes = true;
            $compteur = 0;
            $erreurMsg = '';

            // Enregistrement des titulaires
            foreach ($postesTitulaires as $poste) {
                $joueurId = $_POST['titulaire_' . $poste] ?? 0;
                if (!empty($joueurId) && $joueurId != 0) {
                    $data = [
                        'id_match'      => $idMatch,
                        'id_joueur'     => intval($joueurId),
                        'titulaire'     => 1,
                        'libelle_poste' => $poste,
                        'evaluation'    => null
                    ];
                    $resultat = $this->appelApiFeuilles('POST', $data);
                    if ($resultat['code'] === 201 || $resultat['code'] === 200) {
                        $compteur++;
                    } else {
                        $succes = false;
                        $erreurMsg = "Erreur titulaire $poste: " . ($resultat['data']['status_message'] ?? 'Erreur inconnue');
                        break;
                    }
                } else {
                    $succes = false;
                    $erreurMsg = "Poste $poste non attribué";
                    break;
                }
            }

            // Enregistrement des remplacants
            for ($i = 0; $i < 7; $i++) {
                $joueurId = $_POST['remplacant_joueur_' . $i] ?? 0;
                $poste = $_POST['remplacant_poste_' . $i] ?? '';
                if (!empty($joueurId) && $joueurId != 0 && !empty($poste)) {
                    $data = [
                        'id_match'      => $idMatch,
                        'id_joueur'     => intval($joueurId),
                        'titulaire'     => 0,
                        'libelle_poste' => $poste,
                        'evaluation'    => null
                    ];
                    $resultat = $this->appelApiFeuilles('POST', $data);
                    if ($resultat['code'] === 201 || $resultat['code'] === 200) {
                        $compteur++;
                    } else {
                        $succes = false;
                        $erreurMsg = "Erreur remplaçant $i: " . ($resultat['data']['status_message'] ?? 'Erreur inconnue');
                        break;
                    }
                }
            }

            if ($succes && $compteur >= 11) {
                $messageSucces = "Composition enregistrée avec succès ! ($compteur joueurs)";
                header('Location: index.php?controller=match&action=composer&id=' . $idMatch . '&succes=1');
                exit;
            } else {
                $messageErreur = $erreurMsg ?: "Erreur lors de l'enregistrement";
            }
        }

        // Rechargement des donnees apres enregistrement
        $resultatFeuilles = $this->appelApiFeuilles('GET', null, null, $idMatch);
        $idsSelectionnes = [];
        $statutsSelectionnes = [];
        $libellesSelectionnes = [];

        if ($resultatFeuilles['code'] === 200 && isset($resultatFeuilles['data']['data'])) {
            foreach ($resultatFeuilles['data']['data'] as $feuille) {
                $idsSelectionnes[] = $feuille['id_joueur'];
                $statutsSelectionnes[$feuille['id_joueur']] = ($feuille['titulaire'] ?? 0) == 1 ? 'Titulaire' : 'Remplaçant';
                $libellesSelectionnes[$feuille['id_joueur']] = $feuille['libelle_poste'] ?? '';
            }
        }

        $remplacantsExistants = [];
        foreach ($idsSelectionnes as $idJoueur) {
            if (($statutsSelectionnes[$idJoueur] ?? '') === 'Remplaçant') {
                $remplacantsExistants[] = $idJoueur;
            }
        }

        if (isset($_GET['succes'])) {
            $messageSucces = "Composition enregistrée avec succès !";
        }

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/matchs/compositions-equipe.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Fichier de vue non trouvé : " . $viewPath;
        }
    }

    /**
     * Action: resultat
     * Saisie du resultat d'un match
     */
    public function resultat()
    {
        $titrePage = 'Saisir le résultat';
        $descriptionPage = 'Saisir le résultat d\'un match joué';

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: index.php?controller=match&action=index');
            exit();
        }

        $idMatch = intval($_GET['id']);

        // Recuperation du match
        $resultatMatch = $this->appelApi('GET', null, $idMatch);

        if ($resultatMatch['code'] !== 200 || !isset($resultatMatch['data']['data'])) {
            header('Location: index.php?controller=match&action=index');
            exit();
        }

        $match = $resultatMatch['data']['data'];

        // Verifications
        $dateMatch = new DateTime($match['date_heure_match']);
        $aujourdhui = new DateTime();
        if ($dateMatch > $aujourdhui) {
            $_SESSION['erreur_saisie'] = "Impossible de saisir le résultat d'un match qui n'a pas encore eu lieu.";
            header('Location: index.php?controller=match&action=index');
            exit();
        }

        if (!empty($match['resultat_match']) && $match['resultat_match'] != 'À venir') {
            $_SESSION['erreur_saisie'] = "Un résultat a déjà été saisi pour ce match.";
            header('Location: index.php?controller=match&action=index');
            exit();
        }

        $messageErreur = '';
        $messageSucces = '';

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $messageErreur = "Token de sécurité invalide";
            } else {
                $scoreEquipe = intval($_POST['score_equipe'] ?? 0);
                $scoreAdverse = intval($_POST['score_adversaire'] ?? 0);

                if ($scoreEquipe > $scoreAdverse) {
                    $resultatMatch = 'Victoire';
                } elseif ($scoreEquipe < $scoreAdverse) {
                    $resultatMatch = 'Défaite';
                } else {
                    $resultatMatch = 'Nul';
                }

                $data = [
                    'equipe_adverse'    => $match['equipe_adverse'],
                    'lieu_match'        => $match['lieu_match'],
                    'lieu_rencontre'    => $match['lieu_rencontre'] ?? '',
                    'date_heure_match'  => $match['date_heure_match'],
                    'score_equipe'      => $scoreEquipe,
                    'score_adverse'     => $scoreAdverse,
                    'resultat_match'    => $resultatMatch,
                    'statut_match'      => 'Terminé',
                    'commentaires_match'=> $match['commentaires_match'] ?? null
                ];

                $resultat = $this->appelApi('PUT', $data, $idMatch);

                if ($resultat['code'] === 200) {
                    $messageSucces = "Résultat enregistré avec succès !";
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    header('Location: index.php?controller=match&action=index&succes=1');
                    exit;
                } else {
                    $messageErreur = $resultat['data']['status_message'] ?? "Erreur lors de l'enregistrement du résultat";
                }
            }
        }

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/matchs/saisir-resultat.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Fichier de vue non trouvé : " . $viewPath;
        }
    }

    /**
     * Action: supprimer
     * Supprime un match
     */
    public function supprimer()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=match&action=index');
            exit;
        }

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['erreur_suppression'] = "Token de sécurité invalide";
            header('Location: index.php?controller=match&action=index');
            exit;
        }

        if (!isset($_POST['id_match']) || empty($_POST['id_match'])) {
            $_SESSION['erreur_suppression'] = "ID du match manquant";
            header('Location: index.php?controller=match&action=index');
            exit;
        }

        $idMatch = intval($_POST['id_match']);

        // Verification que le match n'est pas passe
        $resultatMatch = $this->appelApi('GET', null, $idMatch);
        if ($resultatMatch['code'] === 200 && isset($resultatMatch['data']['data'])) {
            $match = $resultatMatch['data']['data'];
            $dateMatch = new DateTime($match['date_heure_match']);
            $aujourdhui = new DateTime();

            if ($dateMatch < $aujourdhui) {
                $_SESSION['erreur_suppression'] = "Impossible de supprimer un match qui a déjà eu lieu.";
                header('Location: index.php?controller=match&action=index');
                exit;
            }
        } else {
            $_SESSION['erreur_suppression'] = "Match non trouvé";
            header('Location: index.php?controller=match&action=index');
            exit;
        }

        // Suppression du match
        $resultat = $this->appelApi('DELETE', null, $idMatch);

        if ($resultat['code'] === 200) {
            $_SESSION['succes_suppression'] = "Match supprimé avec succès";
        } else {
            $_SESSION['erreur_suppression'] = $resultat['data']['status_message'] ?? "Erreur lors de la suppression";
        }

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header('Location: index.php?controller=match&action=index');
        exit;
    }

    /**
     * Action: evaluer
     * Evaluation des joueurs apres un match
     */
    public function evaluer()
    {
        $titrePage = 'Évaluations des Joueurs';
        $descriptionPage = 'Évaluer les performances des joueurs après chaque match';

        $messageSucces = '';
        $messageErreur = '';

        // Recuperation des matchs passes
        $resultat = $this->appelApi('GET');
        $matchsPasses = [];

        if ($resultat['code'] === 200 && isset($resultat['data']['data'])) {
            $matchsPasses = array_filter($resultat['data']['data'], function($m) {
                $aResultat = isset($m['resultat_match']) &&
                            $m['resultat_match'] != 'À venir' &&
                            !empty($m['resultat_match']);

                $dateValide = isset($m['date_heure_match']) &&
                            $m['date_heure_match'] != '0000-00-00 00:00:00' &&
                            !empty($m['date_heure_match']);

                return $aResultat && $dateValide;
            });

            $matchsPasses = array_values($matchsPasses);
        }

        // Recuperation de l'ID du match selectionne
        $idMatchSelectionne = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_id'])) {
            $idMatchSelectionne = intval($_POST['match_id']);
        } elseif (isset($_GET['match'])) {
            $idMatchSelectionne = intval($_GET['match']);
        }

        $matchSelectionne = null;
        $joueurs = [];

        // Chargement des donnees du match
        if ($idMatchSelectionne) {
            $resultatMatch = $this->appelApi('GET', null, $idMatchSelectionne);
            if ($resultatMatch['code'] === 200 && isset($resultatMatch['data']['data'])) {
                $matchSelectionne = $resultatMatch['data']['data'];
            }

            $resultatFeuilles = $this->appelApiFeuilles('GET', null, null, $idMatchSelectionne);
            if ($resultatFeuilles['code'] === 200 && isset($resultatFeuilles['data']['data'])) {
                $joueurs = $resultatFeuilles['data']['data'];
            }
        }

        // Traitement du formulaire d'evaluation
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evaluations']) && $idMatchSelectionne) {
            error_log("=== TRAITEMENT DES ÉVALUATIONS ===");
            error_log("Match ID: " . $idMatchSelectionne);
            error_log("Evaluations reçues: " . print_r($_POST['evaluations'], true));

            $succes = true;
            $erreurs = [];

            foreach ($_POST['evaluations'] as $joueurId => $eval) {
                // Recherche de l'ID de participation
                $idParticipation = null;
                $currentPoste = 'Milieu central';

                foreach ($joueurs as $j) {
                    if ($j['id_joueur'] == $joueurId) {
                        $idParticipation = $j['id_participation'];
                        $currentPoste = $j['libelle_poste'] ?? 'Milieu central';
                        break;
                    }
                }

                if ($idParticipation) {
                    $note = floatval($eval['note'] ?? 0);
                    $statut = intval($eval['statut'] ?? 1);
                    $poste = $eval['poste'] ?? $currentPoste;

                    $data = [
                        'evaluation'    => $note,
                        'titulaire'     => $statut,
                        'libelle_poste' => $poste
                    ];

                    error_log("Mise à jour participation ID $idParticipation: note=$note, statut=$statut, poste=$poste");

                    $resultat = $this->appelApiFeuilles('PUT', $data, $idParticipation);

                    if ($resultat['code'] !== 200) {
                        $succes = false;
                        $erreurs[] = "Joueur ID $joueurId: " . ($resultat['data']['status_message'] ?? 'Erreur inconnue');
                        error_log("ERREUR: " . ($resultat['data']['status_message'] ?? 'Erreur inconnue'));
                    }
                } else {
                    $succes = false;
                    $erreurs[] = "Joueur ID $joueurId non trouvé dans la feuille de match";
                    error_log("ERREUR: Joueur ID $joueurId non trouvé dans la feuille de match");
                }
            }

            if ($succes) {
                $messageSucces = "Évaluations enregistrées avec succès !";
                error_log("Évaluations enregistrées avec succès");

                // Rechargement des joueurs
                $resultatFeuilles = $this->appelApiFeuilles('GET', null, null, $idMatchSelectionne);
                if ($resultatFeuilles['code'] === 200 && isset($resultatFeuilles['data']['data'])) {
                    $joueurs = $resultatFeuilles['data']['data'];
                }
            } else {
                $messageErreur = "Erreurs lors de l'enregistrement: " . implode(', ', $erreurs);
                error_log($messageErreur);
            }
        }

        // Inclusion de la vue
        $viewPath = dirname(__DIR__) . '/views/matchs/evaluations-joueurs.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Fichier de vue non trouvé : " . $viewPath;
        }
    }
}
?>