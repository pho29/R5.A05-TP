<?php
/**
 * =====================================================
 * FICHIER: views/matchs/gestion-matchs.php
 * ROLE: Page de gestion de la liste des matchs
 * DESCRIPTION: Affiche les matchs organises par categorie
 * =====================================================
 */

// Generation d'un token CSRF s'il n'existe pas
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include CHEMIN_INCLUDES . '/entete.php';
?>

<!-- Conteneur principal -->
<div class="conteneur-gestion-matchs">

    <!-- ========================================= -->
    <!-- EN-TETE AVEC STATISTIQUES                   -->
    <!-- ========================================= -->
    <div class="entete-matchs">
        <div class="titre-section">
            <h1><i class="fas fa-futbol"></i> Gestion des Matchs</h1>
            <a href="index.php?controller=match&action=ajouter" class="bouton-principal">
                <i class="fas fa-plus-circle"></i> Ajouter un match
            </a>
        </div>

        <!-- Messages systeme -->
        <?php if (isset($messageSucces) && $messageSucces): ?>
            <div class="message-succes"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($messageSucces); ?></div>
        <?php endif; ?>

        <?php if (isset($messageErreur) && $messageErreur): ?>
            <div class="message-erreur"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($messageErreur); ?></div>
        <?php endif; ?>

        <!-- Cartes de statistiques rapides -->
        <div class="stats-rapides">
            <div class="stat-card">
                <i class="fas fa-calendar-alt icon-stat a-venir"></i>
                <div class="stat-info">
                    <span class="stat-valeur"><?php echo $statsGlobales['a_venir'] ?? 0; ?></span>
                    <span class="stat-label">À venir</span>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-hourglass-half icon-stat sans-resultat"></i>
                <div class="stat-info">
                    <span class="stat-valeur"><?php echo $statsGlobales['sans_resultat'] ?? 0; ?></span>
                    <span class="stat-label">Sans résultat</span>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-trophy icon-stat victoire"></i>
                <div class="stat-info">
                    <span class="stat-valeur"><?php echo $statsGlobales['victoires'] ?? 0; ?></span>
                    <span class="stat-label">Victoires</span>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-times-circle icon-stat defaite"></i>
                <div class="stat-info">
                    <span class="stat-valeur"><?php echo $statsGlobales['defaites'] ?? 0; ?></span>
                    <span class="stat-label">Défaites</span>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-equals icon-stat nul"></i>
                <div class="stat-info">
                    <span class="stat-valeur"><?php echo $statsGlobales['nuls'] ?? 0; ?></span>
                    <span class="stat-label">Nuls</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- RUBRIQUE 1: MATCHS A VENIR                 -->
    <!-- ========================================= -->
    <?php if (!empty($matchsAVenir)): ?>
    <div class="rubrique-matchs">
        <div class="rubrique-header">
            <h2><i class="fas fa-calendar-alt"></i> Matchs à venir</h2>
            <span class="rubrique-count"><?php echo count($matchsAVenir); ?> match(s)</span>
        </div>
        <div class="grille-matchs">
            <?php foreach ($matchsAVenir as $match):
                $dateMatch = new DateTime($match['date_heure_match']);
                $dateFormatee = $dateMatch->format('d/m/Y');
                $heureFormatee = $dateMatch->format('H:i');
            ?>
            <div class="carte-match carte-a-venir">
                <div class="badge-date"><?php echo $dateFormatee; ?></div>
                <div class="entete-match">
                    <div class="heure-match"><i class="fas fa-clock"></i> <?php echo $heureFormatee; ?></div>
                    <span class="badge-type badge-<?php echo strtolower($match['lieu_match']); ?>">
                        <?php echo $match['lieu_match'] == 'Domicile' ? '<i class="fas fa-home"></i>' : '<i class="fas fa-plane"></i>'; ?>
                        <?php echo $match['lieu_match']; ?>
                    </span>
                </div>
                <div class="adversaire-section">
                    <div class="vs-badge">VS</div>
                    <h3 class="nom-adversaire"><?php echo htmlspecialchars($match['equipe_adverse']); ?></h3>
                </div>
                <div class="info-lieu">
                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($match['lieu_rencontre'] ?? $match['lieu_match']); ?>
                </div>
                <div class="score-section en-attente">
                    <i class="fas fa-hourglass-half"></i>
                    <span>Match à venir</span>
                </div>
                <div class="actions-match">
                    <a href="index.php?controller=match&action=composer&id=<?php echo $match['id_match']; ?>" class="btn-action btn-composer" title="Composer l'équipe">
                        <i class="fas fa-users-cog"></i> Composer
                    </a>
                    <a href="index.php?controller=match&action=modifier&id=<?php echo $match['id_match']; ?>" class="btn-action btn-modifier" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="index.php?controller=match&action=supprimer" style="display: inline;"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce match contre <?php echo addslashes($match['equipe_adverse']); ?> ?');">
                        <input type="hidden" name="id_match" value="<?php echo $match['id_match']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="btn-action btn-supprimer" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ========================================= -->
    <!-- RUBRIQUE 2: MATCHS JOUES SANS RESULTAT     -->
    <!-- ========================================= -->
    <?php if (!empty($matchsSansResultat)): ?>
    <div class="rubrique-matchs">
        <div class="rubrique-header">
            <h2><i class="fas fa-hourglass-half"></i> Matchs joués sans résultat</h2>
            <span class="rubrique-count"><?php echo count($matchsSansResultat); ?> match(s)</span>
        </div>
        <div class="grille-matchs">
            <?php foreach ($matchsSansResultat as $match):
                $dateMatch = new DateTime($match['date_heure_match']);
                $dateFormatee = $dateMatch->format('d/m/Y');
                $heureFormatee = $dateMatch->format('H:i');
            ?>
            <div class="carte-match carte-sans-resultat">
                <div class="badge-date"><?php echo $dateFormatee; ?></div>
                <div class="entete-match">
                    <div class="heure-match"><i class="fas fa-clock"></i> <?php echo $heureFormatee; ?></div>
                    <span class="badge-type badge-<?php echo strtolower($match['lieu_match']); ?>">
                        <?php echo $match['lieu_match'] == 'Domicile' ? '<i class="fas fa-home"></i>' : '<i class="fas fa-plane"></i>'; ?>
                        <?php echo $match['lieu_match']; ?>
                    </span>
                </div>
                <div class="adversaire-section">
                    <div class="vs-badge">VS</div>
                    <h3 class="nom-adversaire"><?php echo htmlspecialchars($match['equipe_adverse']); ?></h3>
                </div>
                <div class="info-lieu">
                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($match['lieu_rencontre'] ?? $match['lieu_match']); ?>
                </div>
                <div class="score-section a-saisir">
                    <i class="fas fa-edit"></i>
                    <span>Résultat à saisir</span>
                </div>
                <div class="actions-match">
                    <a href="index.php?controller=match&action=resultat&id=<?php echo $match['id_match']; ?>" class="btn-action btn-resultat" title="Saisir le résultat">
                        <i class="fas fa-flag-checkered"></i> Saisir résultat
                    </a>
                    <a href="index.php?controller=match&action=details&id=<?php echo $match['id_match']; ?>" class="btn-action btn-details" title="Voir détails">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ========================================= -->
    <!-- RUBRIQUE 3: MATCHS AVEC RESULTAT           -->
    <!-- ========================================= -->
    <?php if (!empty($matchsAvecResultat)): ?>
    <div class="rubrique-matchs">
        <div class="rubrique-header">
            <h2><i class="fas fa-chart-line"></i> Matchs avec résultat</h2>
            <span class="rubrique-count"><?php echo count($matchsAvecResultat); ?> match(s)</span>
        </div>
        <div class="grille-matchs">
            <?php foreach ($matchsAvecResultat as $match):
                // Formatage de la date
                $dateFormatee = 'Date inconnue';
                $heureFormatee = '';
                if (!empty($match['date_heure_match']) && $match['date_heure_match'] != '0000-00-00 00:00:00') {
                    try {
                        $dateMatch = new DateTime($match['date_heure_match']);
                        $dateFormatee = $dateMatch->format('d/m/Y');
                        $heureFormatee = $dateMatch->format('H:i');
                    } catch (Exception $e) {
                        $dateFormatee = 'Date invalide';
                    }
                }

                $nomAdversaire = !empty($match['equipe_adverse']) ? $match['equipe_adverse'] : 'Adversaire inconnu';
                $lieuRencontre = !empty($match['lieu_rencontre']) ? $match['lieu_rencontre'] : ($match['lieu_match'] == 'Domicile' ? 'Stade Municipal' : 'Stade Extérieur');
            ?>
            <div class="carte-match carte-avec-resultat">
                <div class="badge-date">
                    <?php echo $dateFormatee; ?>
                    <?php if ($heureFormatee): ?>
                    <span class="heure-mini"> à <?php echo $heureFormatee; ?></span>
                    <?php endif; ?>
                </div>
                <div class="entete-match">
                    <div class="heure-match"><i class="fas fa-clock"></i> <?php echo $heureFormatee ?: 'Heure inconnue'; ?></div>
                    <span class="badge-type badge-<?php echo strtolower($match['lieu_match'] ?? 'domicile'); ?>">
                        <?php echo ($match['lieu_match'] ?? 'Domicile') == 'Domicile' ? '<i class="fas fa-home"></i>' : '<i class="fas fa-plane"></i>'; ?>
                        <?php echo $match['lieu_match'] ?? 'Domicile'; ?>
                    </span>
                </div>
                <div class="adversaire-section">
                    <div class="vs-badge">VS</div>
                    <h3 class="nom-adversaire"><?php echo htmlspecialchars($nomAdversaire); ?></h3>
                </div>
                <div class="info-lieu">
                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($lieuRencontre); ?>
                </div>
                <div class="score-section">
                    <div class="score-display">
                        <span class="score-equipe"><?php echo $match['score_equipe'] ?? 0; ?></span>
                        <span class="separateur">-</span>
                        <span class="score-adversaire"><?php echo $match['score_adverse'] ?? 0; ?></span>
                    </div>
                    <span class="badge-resultat badge-<?php echo strtolower($match['resultat_match'] ?? 'nul'); ?>">
                        <?php
                        if (($match['resultat_match'] ?? '') == 'Victoire') echo '<i class="fas fa-trophy"></i>';
                        elseif (($match['resultat_match'] ?? '') == 'Défaite') echo '<i class="fas fa-times"></i>';
                        else echo '<i class="fas fa-equals"></i>';
                        ?>
                        <?php echo $match['resultat_match'] ?? 'Nul'; ?>
                    </span>
                </div>
                <div class="actions-match">
                    <a href="index.php?controller=match&action=evaluer&match=<?php echo $match['id_match']; ?>" class="btn-action btn-evaluer" title="Évaluer les joueurs">
                        <i class="fas fa-star"></i> Évaluer
                    </a>
                    <a href="index.php?controller=match&action=details&id=<?php echo $match['id_match']; ?>" class="btn-action btn-details" title="Voir détails">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ========================================= -->
    <!-- MESSAGE: AUCUN MATCH                       -->
    <!-- ========================================= -->
    <?php if (empty($matchsAVenir) && empty($matchsSansResultat) && empty($matchsAvecResultat)): ?>
    <div class="aucune-donnee">
        <div class="illustration-vide">
            <i class="fas fa-calendar-times"></i>
        </div>
        <h3>Aucun match enregistré</h3>
        <p>Commencez par ajouter des matchs au calendrier.</p>
        <a href="index.php?controller=match&action=ajouter" class="bouton-principal">
            <i class="fas fa-plus-circle"></i> Ajouter un match
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Inclusion du JavaScript pour la gestion des matchs -->
<script src="<?php echo $prefixe; ?>scripts/gestion-matchs.js"></script>

<?php include CHEMIN_INCLUDES . '/pied-de-page.php'; ?>