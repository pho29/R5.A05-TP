<?php
/**
 * =====================================================
 * FICHIER: interface/entete.php
 * ROLE: En-tête commun à toutes les pages de l'application
 * DESCRIPTION: Inclut les styles, le meta, et la navigation
 * =====================================================
 */

// Initialisation des chemins
$cheminIncludes = CHEMIN_INCLUDES;
$cheminRacine = CHEMIN_RACINE;

// Determination du prefixe selon l'emplacement du fichier
$pageActuelle = basename($_SERVER['PHP_SELF']);
$estDansPages = strpos($_SERVER['PHP_SELF'], 'pages-principales') !== false;
$estDansViews = strpos($_SERVER['PHP_SELF'], 'views') !== false;
$prefixe = ($estDansPages || $estDansViews) ? '../' : '';

// Recuperation du controleur et de l'action depuis l'URL
$controller = isset($_GET['controller']) ? $_GET['controller'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Chemin vers les styles (toujours relatif a la racine)
$styleBase = ($estDansPages || $estDansViews) ? '../styles/' : 'styles/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titrePage) ? $titrePage . ' - ' . NOM_APPLICATION : NOM_APPLICATION; ?></title>

    <!-- ============================================= -->
    <!-- STYLES DE BASE (communs a toutes les pages)    -->
    <!-- ============================================= -->
    <link rel="stylesheet" href="<?php echo $styleBase; ?>style-general.css">
    <link rel="stylesheet" href="<?php echo $styleBase; ?>style-tableaux.css">
    <link rel="stylesheet" href="<?php echo $styleBase; ?>style-entete.css">
    <link rel="stylesheet" href="<?php echo $styleBase; ?>style-navigation.css">
    <link rel="stylesheet" href="<?php echo $styleBase; ?>style-pied-de-page.css">

    <!-- ============================================= -->
    <!-- STYLES SPECIFIQUES (selon le controleur/action) -->
    <!-- ============================================= -->
    <?php
    // Page du tableau de bord
    if ($controller == 'statistique' && $action == 'dashboard') {
        echo '<link rel="stylesheet" href="' . $styleBase . 'style-tableau-de-bord.css">';
    }
    // Page des statistiques de l'equipe
    elseif ($controller == 'statistique' && $action == 'equipe') {
        echo '<link rel="stylesheet" href="' . $styleBase . 'style-statistiques.css">';
    }
    // Page de gestion des joueurs (liste)
    elseif ($controller == 'joueur' && $action == 'index') {
        echo '<link rel="stylesheet" href="' . $styleBase . 'style-joueurs.css">';
    }
    // Page d'ajout ou de modification d'un joueur
    elseif ($controller == 'joueur' && ($action == 'ajouter' || $action == 'modifier')) {
        echo '<link rel="stylesheet" href="' . $styleBase . 'style-joueurs.css">';
        echo '<link rel="stylesheet" href="' . $styleBase . 'style-formulaire.css">';
    }
    // Page de gestion des matchs (liste)
    elseif ($controller == 'match' && $action == 'index') {
        echo '<link rel="stylesheet" href="' . $styleBase . 'style-gestion-matchs.css">';
    }
    // Page d'ajout ou de modification d'un match
    elseif ($controller == 'match' && ($action == 'ajouter' || $action == 'modifier')) {
        echo '<link rel="stylesheet" href="' . $styleBase . 'style-formulaire.css">';
    }
    // Page des details d'un match
    elseif ($controller == 'match' && $action == 'details') {
        echo '<link rel="stylesheet" href="' . $styleBase . 'style-details-match.css">';
    }
    // Page de composition d'equipe
    elseif ($controller == 'match' && $action == 'composer') {
        echo '<link rel="stylesheet" href="' . $styleBase . 'style-composer-equipe.css">';
    }
    // Page d'evaluation des joueurs
    elseif ($controller == 'match' && $action == 'evaluer') {
        echo '<link rel="stylesheet" href="' . $styleBase . 'style-evaluations.css">';
    }
    // Page de saisie du resultat
    elseif ($controller == 'match' && $action == 'resultat') {
        echo '<link rel="stylesheet" href="' . $styleBase . 'style-saisir-resultat.css">';
    }
    // Pages des commentaires
    elseif ($controller == 'commentaire') {
        echo '<link rel="stylesheet" href="' . $styleBase . 'style-commentaires.css">';
    }
    ?>

    <!-- ============================================= -->
    <!-- FONT AWESOME (icones)                          -->
    <!-- ============================================= -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <!-- ============================================= -->
    <!-- NAVIGATION PRINCIPALE                          -->
    <!-- ============================================= -->
    <?php
    // Inclusion de la navigation pour toutes les pages sauf la page de connexion
    if ($pageActuelle !== 'connexion.php' && isset($_SESSION['token']) && !empty($_SESSION['token'])) {
        $cheminNavigation = $cheminIncludes . '/navigation.php';
        if (file_exists($cheminNavigation)) {
            include $cheminNavigation;
        }
    }
    ?>

    <!-- ============================================= -->
    <!-- CONTENU PRINCIPAL                              -->
    <!-- ============================================= -->
    <div class="conteneur-principal">
        <main class="contenu-page">

            <!-- En-tete de page (titre et description) -->
            <?php if (!isset($cacherTitre) || !$cacherTitre): ?>
            <header class="en-tete-page">
                <h1><?php echo isset($titrePage) ? $titrePage : NOM_APPLICATION; ?></h1>
                <?php if (isset($descriptionPage)): ?>
                    <p class="description-page"><?php echo $descriptionPage; ?></p>
                <?php endif; ?>
            </header>
            <?php endif; ?>

            <!-- Messages d'alerte (erreur / succes) -->
            <?php if (isset($messageErreur) && !empty($messageErreur)): ?>
                <div class="message-erreur">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $messageErreur; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($messageSucces) && !empty($messageSucces)): ?>
                <div class="message-succes">
                    <i class="fas fa-check-circle"></i> <?php echo $messageSucces; ?>
                </div>
            <?php endif; ?>