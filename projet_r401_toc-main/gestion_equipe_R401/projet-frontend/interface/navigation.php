<?php
/**
 * =====================================================
 * FICHIER: interface/navigation.php
 * ROLE: Barre de navigation laterale de l'application
 * DESCRIPTION: Menu principal accessible sur toutes les pages
 * =====================================================
 */

// Verification de la connexion
if (!Authentification::estConnecte()) {
    header('Location: ' . (strpos($_SERVER['PHP_SELF'], 'pages-principales') !== false ? '../' : '') . 'connexion.php');
    exit();
}

// Determination de la page active et des prefixes
$pageActuelle = basename($_SERVER['PHP_SELF']);
$estDansPages = strpos($_SERVER['PHP_SELF'], 'pages-principales') !== false;
$prefixeLien = $estDansPages ? '' : 'pages-principales/';
$prefixeRacine = $estDansPages ? '../' : './';
?>

<!-- ============================================= -->
<!-- BARRE DE NAVIGATION LATERALE                   -->
<!-- ============================================= -->
<nav class="navigation-principale">

    <!-- Logo et nom de l'application -->
    <div class="logo-navigation">
        <div class="icone-logo">⚽</div>
        <div class="texte-logo">
            <h1><?php echo NOM_APPLICATION; ?></h1>
            <p><?php echo NOM_EQUIPE; ?></p>
        </div>
    </div>

    <!-- Menu principal -->
    <ul class="menu-principal">

        <!-- Lien Tableau de bord -->
        <li class="<?php echo $pageActuelle == 'tableau-de-bord.php' ? 'actif' : ''; ?>">
            <a href="index.php?controller=statistique&action=dashboard">
                <i class="fas fa-home"></i>
                <span>Tableau de bord</span>
            </a>
        </li>

        <!-- Menu Joueurs avec sous-menu -->
        <li>
            <a href="index.php?controller=joueur&action=index">
                <i class="fas fa-users"></i>
                <span>Joueurs</span>
            </a>
            <ul class="sous-menu">
                <li><a href="index.php?controller=joueur&action=index">Liste des joueurs</a></li>
                <li><a href="index.php?controller=joueur&action=ajouter">Ajouter un joueur</a></li>
            </ul>
        </li>

        <!-- Menu Matchs avec sous-menu -->
        <li>
            <a href="index.php?controller=match&action=index">
                <i class="fas fa-calendar-alt"></i>
                <span>Matchs</span>
            </a>
            <ul class="sous-menu">
                <li><a href="index.php?controller=match&action=index">Liste des matchs</a></li>
                <li><a href="index.php?controller=match&action=ajouter">Ajouter un match</a></li>
            </ul>
        </li>

        <!-- Lien Evaluations -->
        <li>
            <a href="index.php?controller=match&action=evaluer">
                <i class="fas fa-star"></i>
                <span>Évaluations</span>
            </a>
        </li>

        <!-- Lien Statistiques -->
        <li>
            <a href="index.php?controller=statistique&action=equipe">
                <i class="fas fa-chart-bar"></i>
                <span>Statistiques</span>
            </a>
        </li>
    </ul>

    <!-- Profil de l'entraineur connecte -->
    <div class="profil-entraineur">
        <div class="info-profil">
            <div class="avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="details">
                <strong><?php
                    $u = $_SESSION['utilisateur'] ?? [];
                    echo htmlspecialchars(($u['nom'] ?? '') . ' ' . ($u['prenom'] ?? ''));
                ?></strong>
                <small>Entraîneur</small>
            </div>
        </div>
        <a href="<?php echo $prefixeRacine; ?>deconnexion.php" class="btn-deconnexion">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </div>
</nav>

<!-- Inclusion du fichier CSS de navigation -->
<link rel="stylesheet" href="<?php echo $prefixeRacine; ?>styles/style-navigation.css">