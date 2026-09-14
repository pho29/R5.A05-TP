<?php
/**
 * =====================================================
 * FICHIER: projet-frontend/securite/authentification.php
 * ROLE: Gestion de l'authentification cote frontend
 * DESCRIPTION: Utilise l'API d'authentification principale pour:
 *   - Verifier si un utilisateur est connecte
 *   - Connecter un entraineur
 *   - Gerer la deconnexion
 *   - Recuperer le token et les informations utilisateur
 * =====================================================
 */

/**
 * Classe Authentification
 * Gere l'authentification des utilisateurs via l'API JWT
 * Toutes les methodes sont statiques
 */
class Authentification
{
    /**
     * URL de l'API d'authentification
     * @var string
     */
    private static $authApiUrl = 'http://localhost/projet_r401_toc/gestion_equipe_R401/projet-apiAuthentification/auth.php';

    /**
     * Verifie si l'utilisateur est connecte
     * Valide le token JWT aupres de l'API d'authentification
     *
     * @return bool True si connecte, false sinon
     */
    public static function estConnecte()
    {
        // Verification de la presence du token en session
        if (!isset($_SESSION['token'])) {
            return false;
        }

        $token = $_SESSION['token'];

        // Appel a l'API pour verifier la validite du token
        $ch = curl_init(self::$authApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Si le token est valide (code HTTP 200)
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            if (isset($data['data'])) {
                // Mise a jour des informations utilisateur en session
                $_SESSION['utilisateur'] = $data['data'];
            }
            return true;
        }

        // Token invalide -> nettoyage de la session
        session_destroy();
        return false;
    }

    /**
     * Connecte un entraineur via l'API d'authentification
     *
     * @param string $email Email de l'entraineur
     * @param string $motDePasse Mot de passe
     * @return bool True si connexion reussie, false sinon
     */
    public static function connecterEntraineur($email, $motDePasse)
    {
        // Preparation des donnees de connexion
        $credentials = json_encode([
            'email'    => $email,
            'password' => $motDePasse
        ]);

        // Appel a l'API d'authentification
        $ch = curl_init(self::$authApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $credentials);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Si la connexion est reussie (code HTTP 200)
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            if (isset($data['data']['token'])) {
                // Stockage du token et des informations utilisateur en session
                $_SESSION['token']       = $data['data']['token'];
                $_SESSION['utilisateur'] = $data['data']['utilisateur'];
                return true;
            }
        }

        return false;
    }

    /**
     * Deconnecte l'utilisateur
     * Detruit la session et redirige vers la page de connexion
     */
    public static function deconnecter()
    {
        session_destroy();
        header('Location: /projet_r401_toc/gestion_equipe_R401/projet-frontend/connexion.php');
        exit();
    }

    /**
     * Recupere le token JWT stocke en session
     *
     * @return string|null Token JWT ou null si non present
     */
    public static function getToken()
    {
        return $_SESSION['token'] ?? null;
    }

    /**
     * Recupere les informations de l'utilisateur connecte
     *
     * @return array|null Informations utilisateur (id, email, nom, prenom, role) ou null
     */
    public static function getUtilisateur()
    {
        return $_SESSION['utilisateur'] ?? null;
    }

    /**
     * Verifie si l'utilisateur a un role specifique
     *
     * @param string $role Role a verifier (ex: 'admin', 'entraineur')
     * @return bool True si l'utilisateur a le role, false sinon
     */
    public static function aRole($role)
    {
        $utilisateur = self::getUtilisateur();
        return isset($utilisateur['role']) && $utilisateur['role'] === $role;
    }
}
?>