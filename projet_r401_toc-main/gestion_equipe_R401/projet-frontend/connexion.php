<?php
/**
 * =====================================================
 * FICHIER: connexion.php
 * ROLE: Page de connexion principale
 * DESCRIPTION: Formulaire d'authentification pour les entraineurs
 *              Redirige vers le dashboard si deja connecte
 * =====================================================
 */

// Inclusion des fichiers de configuration et d'authentification
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/securite/authentification.php';

/**
 * Si l'utilisateur est deja connecte, redirection vers le tableau de bord
 */
if (Authentification::estConnecte()) {
    header('Location: index.php?controller=statistique&action=dashboard');
    exit();
}

// Initialisation du message d'erreur
$messageErreur = '';

/**
 * Traitement du formulaire de connexion (methode POST)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperation et nettoyage des donnees
    $email = trim($_POST['email_entraineur'] ?? '');
    $motDePasse = $_POST['mot_de_passe_entraineur'] ?? '';

    // Validation des champs
    if (empty($email) || empty($motDePasse)) {
        $messageErreur = 'Veuillez remplir tous les champs';
    } else {
        // Tentative de connexion via la classe d'authentification
        $auth = new Authentification();

        if ($auth->connecterEntraineur($email, $motDePasse)) {
            // Connexion reussie -> redirection vers le dashboard
            header('Location: index.php?controller=statistique&action=dashboard');
            exit();
        } else {
            // Echec de la connexion
            $messageErreur = 'Email ou mot de passe incorrect';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Manager d'Équipe</title>

    <!-- Feuilles de style -->
    <link rel="stylesheet" href="styles/style-connexion.css">
    <link rel="stylesheet" href="styles/style-general.css">

    <!-- Font Awesome pour les icones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="page-connexion">

    <!-- Carte de connexion principale -->
    <div class="carte-connexion">

        <!-- En-tete avec logo et titre -->
        <div class="entete-connexion">
            <div class="logo-equipe">
                <i class="fas fa-futbol"></i>
            </div>
            <h1>Manager d'Équipe de Football</h1>
            <p>Application de gestion pour entraîneurs</p>
        </div>

        <!-- Formulaire de connexion -->
        <div class="formulaire-connexion">

            <!-- Affichage du message d'erreur si present -->
            <?php if ($messageErreur): ?>
                <div class="message-erreur">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $messageErreur; ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire -->
            <form method="POST" action="">

                <!-- Champ Email -->
                <div class="groupe-formulaire-connexion">
                    <label for="email_entraineur">
                        <i class="fas fa-envelope"></i> Adresse email
                    </label>
                    <input type="email"
                           id="email_entraineur"
                           name="email_entraineur"
                           required
                           placeholder="monsieur.entraineur@club.com"
                           value="<?php echo htmlspecialchars($_POST['email_entraineur'] ?? ''); ?>">
                </div>

                <!-- Champ Mot de passe -->
                <div class="groupe-formulaire-connexion">
                    <label for="mot_de_passe_entraineur">
                        <i class="fas fa-lock"></i> Mot de passe
                    </label>
                    <input type="password"
                           id="mot_de_passe_entraineur"
                           name="mot_de_passe_entraineur"
                           required
                           placeholder="Votre mot de passe">
                </div>

                <!-- Bouton de soumission -->
                <div class="groupe-formulaire-connexion">
                    <button type="submit" class="bouton-connexion">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </button>
                </div>
            </form>

            <!-- Informations d'aide pour les tests -->
            <div class="informations-connexion">
                <h3><i class="fas fa-info-circle"></i> Instructions</h3>
                <p>1. <strong>Identifiant :</strong> monsieur.entraineur@club.com</p>
                <p>2. <strong>Mot de passe :</strong> entraineur123</p>
            </div>
        </div>
    </div>
</body>
</html>