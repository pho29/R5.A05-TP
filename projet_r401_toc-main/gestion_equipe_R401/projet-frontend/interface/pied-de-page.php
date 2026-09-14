<?php
/**
 * =====================================================
 * FICHIER: interface/pied-de-page.php
 * ROLE: Pied de page commun a toutes les pages
 * DESCRIPTION: Affiche les informations de l'application et de l'utilisateur
 * =====================================================
 */

// Determination du prefixe selon l'emplacement du fichier
$pageActuelle = basename($_SERVER['PHP_SELF']);
$estDansPages = strpos($_SERVER['PHP_SELF'], 'pages-principales') !== false;
$estDansViews = strpos($_SERVER['PHP_SELF'], 'views') !== false;
$prefixe = ($estDansPages || $estDansViews) ? '../' : '';
?>

        <!-- Fermeture des balises ouvertes dans entete.php -->
        </main>
    </div>

    <!-- ============================================= -->
    <!-- PIED DE PAGE                                   -->
    <!-- ============================================= -->
    <footer class="pied-de-page">
        <div class="conteneur-pied">

            <!-- Informations de l'application -->
            <div class="info-application">
                <p class="nom-application"><?php echo NOM_APPLICATION; ?> v<?php echo VERSION_APPLICATION; ?></p>
                <p class="copyright">&copy; <?php echo date('Y'); ?> <?php echo NOM_EQUIPE; ?></p>
            </div>

            <!-- Informations de l'utilisateur connecte -->
            <?php if (isset($_SESSION['token']) && !empty($_SESSION['token'])): ?>
            <?php $u = $_SESSION['utilisateur'] ?? []; ?>
            <div class="info-utilisateur">
                <p class="nom-utilisateur">
                    <strong><?php echo htmlspecialchars(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? '')); ?></strong>
                </p>
                <p class="email-utilisateur">
                    <?php echo htmlspecialchars($u['email'] ?? ''); ?>
                </p>
            </div>
            <?php endif; ?>

        </div>
    </footer>

    <!-- ============================================= -->
    <!-- SCRIPTS (a ajouter si necessaire)              -->
    <!-- ============================================= -->
    <!-- Les scripts seront ajoutes plus tard si necessaire -->

    <!-- Inclusion du CSS du pied de page -->
    <link rel="stylesheet" href="<?php echo $prefixe; ?>styles/style-pied-de-page.css">
</body>
</html>