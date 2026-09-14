<?php
/**
 * =====================================================
 * FICHIER: controllers/StatistiqueController.php
 * ROLE: Controleur pour les statistiques (frontend)
 * ACTIONS: dashboard, equipe
 * =====================================================
 */

require_once __DIR__ . '/../securite/authentification.php';

/**
 * Classe StatistiqueController
 * Gere l'affichage des statistiques
 */
class StatistiqueController
{
    /**
     * URL du backend
     * @var string
     */
    private $urlBackend = URL_API_BACKEND;

    /**
     * Constructeur
     * Verifie l'authentification
     */
    public function __construct()
    {
        if (!Authentification::estConnecte()) {
            header('Location: /projet_r401_toc/gestion_equipe_R401/projet-frontend/connexion.php');
            exit;
        }
    }

    /**
     * Appelle l'API backend
     *
     * @param string $endpoint Endpoint REST (ex: 'statistiques', 'joueurs')
     * @param string $method Methode HTTP
     * @param array|null $data Donnees a envoyer
     * @return array Reponse de l'API
     */
    private function appelAPI($endpoint, $method = 'GET', $data = null)
    {
        $token = Authentification::getToken();
        // CORRECTION : plus de /api/ dans l'URL — on appelle directement le routeur REST
        $url = rtrim($this->urlBackend, '/') . '/' . ltrim($endpoint, '/');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            error_log("Erreur curl: " . $curlError);
            return ['code' => 500, 'data' => null];
        }

        $decoded = json_decode($response, true);

        if (isset($decoded['data'])) {
            return ['code' => $httpCode, 'data' => $decoded['data']];
        }

        return ['code' => $httpCode, 'data' => $decoded];
    }

    /**
     * Action: dashboard
     * Affiche le tableau de bord
     */
    public function dashboard()
    {
        $titrePage = 'Tableau de bord';
        $descriptionPage = 'Vue d\'ensemble de votre équipe';

        // CORRECTION : utilisation des routes REST propres
        $resJoueurs = $this->appelAPI('joueurs');
        $tousJoueurs = $resJoueurs['code'] === 200 ? ($resJoueurs['data'] ?? []) : [];

        $resMatchs = $this->appelAPI('matchs');
        $matchs = $resMatchs['code'] === 200 ? ($resMatchs['data'] ?? []) : [];

        // Calcul des statistiques
        $totalMatchs = count($matchs);

        $matchsTermines = array_filter($matchs, function($m) {
            return ($m['statut_match'] ?? '') === 'Terminé';
        });

        $victoires = 0;
        $defaites = 0;
        $nuls = 0;

        foreach ($matchsTermines as $match) {
            $resultat = $match['resultat_match'] ?? '';
            if ($resultat === 'Victoire') $victoires++;
            elseif ($resultat === 'Défaite') $defaites++;
            elseif ($resultat === 'Nul') $nuls++;
        }

        $totalMatchsTermines = count($matchsTermines);
        $pourcentageVictoires = $totalMatchsTermines > 0 ? round(($victoires / $totalMatchsTermines) * 100, 1) : 0;

        $prochainsMatchs = array_filter($matchs, function($m) {
            return ($m['statut_match'] ?? '') === 'À venir' || ($m['statut_match'] ?? '') === 'Préparé';
        });

        $derniersResultats = array_slice($matchsTermines, 0, 5);
        $derniersJoueurs = array_slice($tousJoueurs, 0, 5);

        $statistiques = [
            'total_joueurs'     => count($tousJoueurs),
            'joueurs_actifs'    => count(array_filter($tousJoueurs, fn($j) => ($j['statut'] ?? '') === 'Actif')),
            'joueurs_blesses'   => count(array_filter($tousJoueurs, fn($j) => ($j['statut'] ?? '') === 'Blessé')),
            'total_matchs'      => $totalMatchs,
            'matchs_termines'   => $totalMatchsTermines,
            'matchs_gagnes'     => $victoires,
            'matchs_perdus'     => $defaites,
            'matchs_nuls'       => $nuls,
            'prochains_matchs'  => count($prochainsMatchs),
        ];

        // Inclusion des vues
        $entetePath = CHEMIN_INCLUDES . '/entete.php';
        $dashboardPath = dirname(__DIR__) . '/views/tableau-de-bord/tableau-de-bord.php';

        if (file_exists($entetePath)) include $entetePath;
        if (file_exists($dashboardPath)) include $dashboardPath;
    }

    /**
     * Action: equipe
     * Affiche les statistiques de l'equipe
     */
    public function equipe()
    {
        $titrePage = 'Statistiques de l\'Équipe';
        $descriptionPage = 'Vue d\'ensemble des performances et statistiques complètes';

        // CORRECTION : utilisation de la route REST propre
        $resStats = $this->appelAPI('statistiques');
        $toutesStats = $resStats['code'] === 200 ? ($resStats['data'] ?? []) : [];

        // Statistiques globales
        $statsGlobales = $toutesStats['globales'] ?? [];
        $victoires     = (int)($statsGlobales['victoires']     ?? 0);
        $defaites      = (int)($statsGlobales['defaites']      ?? 0);
        $nuls          = (int)($statsGlobales['nuls']           ?? 0);
        $totalJoueurs  = (int)($statsGlobales['total_joueurs'] ?? 0);
        $totalMatchs   = $victoires + $defaites + $nuls;

        $totalMatchsTermines  = $totalMatchs;
        $pourcentageVictoires = $totalMatchs > 0 ? round(($victoires / $totalMatchs) * 100, 1) : 0;
        $pourcentageDefaites  = $totalMatchs > 0 ? round(($defaites  / $totalMatchs) * 100, 1) : 0;
        $pourcentageNuls      = $totalMatchs > 0 ? round(($nuls      / $totalMatchs) * 100, 1) : 0;

        // Statistiques par joueur
        $statsJoueurs = [];
        foreach ($toutesStats['joueurs'] ?? [] as $j) {
            if (!is_array($j)) continue;
            $totalJoues = (int)($j['total_matchs']  ?? 0);
            $gagnes     = (int)($j['matchs_gagnes'] ?? 0);
            $statsJoueurs[] = [
                'id'                      => $j['id_joueur']          ?? '',
                'numero_licence'          => $j['numero_licence']     ?? '',
                'nom'                     => $j['nom']         ?? '',
                'prenom'                  => $j['prenom']      ?? '',
                'statut'                  => $j['statut']      ?? 'Inconnu',
                'poste_prefere'           => $j['poste_prefere']      ?? 'N/A',
                'nb_titularisations'      => (int)($j['titularisations']    ?? 0),
                'nb_remplacements'        => (int)($j['remplacements']      ?? 0),
                'moyenne_evaluations'     => (float)($j['moyenne_evaluation'] ?? 0),
                'selections_consecutives' => 0,
                'pourcentage_victoires'   => $totalJoues > 0 ? round(($gagnes / $totalJoues) * 100, 1) : 0,
            ];
        }

        // Inclusion des vues
        $entetePath = CHEMIN_INCLUDES . '/entete.php';
        $statsPath  = dirname(__DIR__) . '/views/statistiques/statistiques-equipe.php';

        if (file_exists($entetePath)) include $entetePath;
        if (file_exists($statsPath))  include $statsPath;
    }
}
?>