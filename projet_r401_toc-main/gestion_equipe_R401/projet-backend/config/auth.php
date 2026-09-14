<?php
/**
 * =====================================================
 * FICHIER: config/auth.php
 * ROLE: Gestion de l'authentification via JWT
 * Utilise l'API d'authentification pour verifier les tokens
 * =====================================================
 */

/**
 * URL de l'API d'authentification
 * @var string
 */
define('URL_API_AUTH', 'http://localhost/projet_r401_toc/gestion_equipe_R401/projet-apiAuthentification/auth.php');

/**
 * Verifie le token en interrogeant l'API d'authentification
 * 
 * @return array Les donnees utilisateur si token valide
 * @throws void En cas d'erreur, retourne une reponse HTTP 401
 */
function verifierToken()
{
    // Recuperation de l'en-tete Authorization
    $headers = getallheaders();
    $authorization = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    // Verification de la presence du token Bearer
    if (!$authorization || !preg_match('/^Bearer\s+(.*)$/i', $authorization, $matches)) {
        http_response_code(401);
        echo json_encode([
            "status_code"    => 401,
            "status_message" => "Token manquant",
            "data"           => null
        ]);
        exit;
    }

    $token = $matches[1];

    // Appel a l'API d'authentification pour valider le token
    $ch = curl_init(URL_API_AUTH);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Verification de la reponse
    if ($curlError || $httpCode !== 200) {
        http_response_code(401);
        echo json_encode([
            "status_code"    => 401,
            "status_message" => "Token invalide ou expiré",
            "data"           => null
        ]);
        exit;
    }

    $userData = json_decode($response, true);

    // Retour des donnees utilisateur si tout est valide
    if (isset($userData['data'])) {
        return $userData['data'];
    }

    http_response_code(401);
    echo json_encode([
        "status_code"    => 401,
        "status_message" => "Token invalide",
        "data"           => null
    ]);
    exit;
}
?>