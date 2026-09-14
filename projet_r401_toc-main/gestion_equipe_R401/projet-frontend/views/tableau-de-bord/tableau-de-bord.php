<?php
/**
 * =====================================================
 * FICHIER: views/tableau-de-bord/tableau-de-bord.php
 * ROLE: Page d'accueil / Tableau de bord
 * DESCRIPTION: Vue d'ensemble de l'equipe (statistiques, prochains matchs, etc.)
 * =====================================================
 */

$cacherTitre = true;
include CHEMIN_INCLUDES . '/entete.php';
?>

<!-- Cartes de statistiques -->
<div class="cartes-statistiques">

    <!-- Carte Joueurs -->
    <div class="carte-statistique">
        <h3>Joueurs</h3>
        <div class="valeur-statistique"><?php echo $statistiques['total_joueurs']; ?></div>
        <p class="variation-statistique">
            <i class="fas fa-user-check"></i> <?php echo $statistiques['joueurs_actifs']; ?> actifs
            <?php if ($statistiques['joueurs_blesses'] > 0): ?>
            <span class="badge-blesse"><i class="fas fa-user-injured"></i> <?php echo $statistiques['joueurs_blesses']; ?> blessés</span>
            <?php endif; ?>
        </p>
    </div>

    <!-- Carte Matchs joues -->
    <div class="carte-statistique">
        <h3>Matchs joués</h3>
        <div class="valeur-statistique"><?php echo $statistiques['matchs_termines']; ?></div>
        <p class="variation-statistique">
            <?php
            if ($statistiques['matchs_termines'] > 0) {
                echo '<i class="fas fa-chart-line"></i> ' . $pourcentageVictoires . '% de victoires';
            } else {
                echo '<i class="fas fa-info-circle"></i> Aucun match terminé';
            }
            ?>
        </p>
        <?php if ($statistiques['matchs_termines'] > 0): ?>
        <div class="mini-stats">
            <span class="victoire-mini">V <?php echo $statistiques['matchs_gagnes']; ?></span>
            <span class="defaite-mini">L <?php echo $statistiques['matchs_perdus']; ?></span>
            <span class="nul-mini">D <?php echo $statistiques['matchs_nuls']; ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Carte Prochains matchs -->
    <div class="carte-statistique">
        <h3>Prochains matchs</h3>
        <div class="valeur-statistique"><?php echo $statistiques['prochains_matchs']; ?></div>
        <p class="variation-statistique">
            <i class="fas fa-calendar-alt"></i> À préparer
        </p>
    </div>

    <!-- Carte Joueurs blesses -->
    <div class="carte-statistique">
        <h3>Joueurs blessés</h3>
        <div class="valeur-statistique"><?php echo $statistiques['joueurs_blesses']; ?></div>
        <p class="variation-statistique">
            <i class="fas fa-notes-medical"></i> En observation
        </p>
        <?php if ($statistiques['joueurs_blesses'] > 0): ?>
        <div class="alerte-blessure">
            Retour prévu prochainement
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tableaux du dashboard -->
<div class="tableaux-dashboard">

    <!-- ========================================= -->
    <!-- TABLEAU DES PROCHAINS MATCHS               -->
    <!-- ========================================= -->
    <div class="tableau-section">
        <h2><i class="fas fa-calendar-week"></i> Prochains matchs</h2>

        <?php if (!empty($prochainsMatchs)): ?>
            <div class="tableau-donnees">
                <table class="tableau-classique">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Adversaire</th>
                            <th>Lieu</th>
                            <th>Stade</th>
                            <th>Actions</th>
                        </thead>
                    <tbody>
                        <?php foreach ($prochainsMatchs as $match): ?>
                            <?php
                            $dateMatch = new DateTime($match['date_heure_match']);
                            $estAujourdhui = $dateMatch->format('Y-m-d') == date('Y-m-d');
                            $estDemain = $dateMatch->format('Y-m-d') == date('Y-m-d', strtotime('+1 day'));
                            $dateClass = $estAujourdhui ? 'date-aujourdhui' : ($estDemain ? 'date-demain' : '');
                            ?>
                            <tr class="<?php echo $dateClass; ?>">
                                <td>
                                    <strong><?php echo $dateMatch->format('d/m/Y'); ?></strong>
                                    <small><?php echo $dateMatch->format('H:i'); ?></small>
                                    <?php if ($estAujourdhui): ?>
                                        <span class="badge-aujourdhui">AUJOURD'HUI</span>
                                    <?php elseif ($estDemain): ?>
                                        <span class="badge-demain">DEMAIN</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($match['equipe_adverse']); ?></strong></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($match['lieu_match']); ?>">
                                        <?php echo $match['lieu_match'] == 'Domicile' ? '<i class="fas fa-home"></i>' : '<i class="fas fa-plane"></i>'; ?>
                                        <?php echo $match['lieu_match']; ?>
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($match['lieu_rencontre'] ?? ($match['lieu_match'] == 'Domicile' ? 'Stade Municipal' : 'Stade Extérieur')); ?>
                                </td>
                                <td class="actions-tableau">
                                    <a href="index.php?controller=match&action=composer&id=<?php echo $match['id_match']; ?>"
                                       class="bouton-action bouton-composer" title="Composer l'équipe">
                                        <i class="fas fa-users-cog"></i> Composer
                                    </a>
                                    <a href="index.php?controller=match&action=modifier&id=<?php echo $match['id_match']; ?>"
                                       class="bouton-action bouton-modifier" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="message-aucune-donnee">
                <i class="fas fa-calendar-times"></i>
                <p>Aucun match à venir pour le moment.</p>
                <a href="index.php?controller=match&action=ajouter" class="bouton-principal petit">
                    <i class="fas fa-plus-circle"></i> Ajouter un match
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- ========================================= -->
    <!-- TABLEAU DES DERNIERS RESULTATS             -->
    <!-- ========================================= -->
    <div class="tableau-section">
        <h2><i class="fas fa-history"></i> Derniers résultats</h2>

        <?php if (!empty($derniersResultats)): ?>
            <div class="tableau-donnees">
                <table class="tableau-classique">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Adversaire</th>
                            <th>Score</th>
                            <th>Résultat</th>
                            <th>Actions</th>
                        </thead>
                    <tbody>
                        <?php foreach ($derniersResultats as $match): ?>
                            <?php
                            $dateMatch = new DateTime($match['date_heure_match']);
                            $resultatClass = '';
                            $resultatIcone = '';
                            switch ($match['resultat_match']) {
                                case 'Victoire':
                                    $resultatClass = 'badge-victoire';
                                    $resultatIcone = '<i class="fas fa-trophy"></i>';
                                    break;
                                case 'Défaite':
                                    $resultatClass = 'badge-defaite';
                                    $resultatIcone = '<i class="fas fa-times-circle"></i>';
                                    break;
                                case 'Nul':
                                    $resultatClass = 'badge-nul';
                                    $resultatIcone = '<i class="fas fa-handshake"></i>';
                                    break;
                                default:
                                    $resultatClass = 'badge-default';
                            }
                            ?>
                            <tr>
                                <td><?php echo $dateMatch->format('d/m/Y'); ?></td>
                                <td><strong><?php echo htmlspecialchars($match['equipe_adverse']); ?></strong></td>
                                <td class="score-cell">
                                    <span class="score-equipe"><?php echo $match['score_equipe']; ?></span>
                                    <span class="score-separateur">-</span>
                                    <span class="score-adverse"><?php echo $match['score_adverse']; ?></span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $resultatClass; ?>">
                                        <?php echo $resultatIcone; ?> <?php echo $match['resultat_match']; ?>
                                    </span>
                                </td>
                                <td class="actions-tableau">
                                    <a href="index.php?controller=match&action=details&id=<?php echo $match['id_match']; ?>"
                                       class="bouton-action bouton-voir" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($match['resultat_match'] != 'À venir'): ?>
                                    <a href="index.php?controller=match&action=evaluer&match=<?php echo $match['id_match']; ?>"
                                       class="bouton-action bouton-evaluer" title="Évaluer les joueurs">
                                        <i class="fas fa-star"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="message-aucune-donnee">
                <i class="fas fa-chart-simple"></i>
                <p>Aucun match terminé pour le moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================= -->
<!-- DERNIERS JOUEURS AJOUTES                   -->
<!-- ========================================= -->
<?php if (!empty($derniersJoueurs)): ?>
<div class="tableau-section">
    <h2><i class="fas fa-user-plus"></i> Derniers joueurs ajoutés</h2>
    <div class="tableau-donnees">
        <table class="tableau-classique">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Âge</th>
                    <th>Taille</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </thead>
            <tbody>
                <?php foreach ($derniersJoueurs as $joueur): ?>
                    <?php
                    $dateNaissance = new DateTime($joueur['date_naissance']);
                    $age = (new DateTime())->diff($dateNaissance)->y;
                    $statutClass = 'badge-' . strtolower($joueur['statut_joueur']);
                    $statutIcone = '';
                    switch ($joueur['statut_joueur']) {
                        case 'Actif': $statutIcone = '<i class="fas fa-user-check"></i>'; break;
                        case 'Blessé': $statutIcone = '<i class="fas fa-user-injured"></i>'; break;
                        case 'Suspendu': $statutIcone = '<i class="fas fa-user-slash"></i>'; break;
                        default: $statutIcone = '<i class="fas fa-user"></i>';
                    }
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($joueur['nom_joueur']); ?></strong></td>
                        <td><?php echo htmlspecialchars($joueur['prenom_joueur']); ?></td>
                        <td><?php echo $age; ?> ans</td>
                        <td><?php echo $joueur['taille_cm']; ?> cm</td>
                        <td>
                            <span class="badge <?php echo $statutClass; ?>">
                                <?php echo $statutIcone; ?> <?php echo $joueur['statut_joueur']; ?>
                            </span>
                        </td>
                        <td class="actions-tableau">
                            <a href="index.php?controller=joueur&action=modifier&id=<?php echo $joueur['id_joueur']; ?>"
                               class="bouton-action bouton-modifier" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="index.php?controller=joueur&action=details&id=<?php echo $joueur['id_joueur']; ?>"
                               class="bouton-action bouton-voir" title="Détails">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="actions-tableau-centre">
        <a href="index.php?controller=joueur&action=index" class="bouton-secondaire">
            <i class="fas fa-users"></i> Voir tous les joueurs
        </a>
    </div>
</div>
<?php endif; ?>

<?php include CHEMIN_INCLUDES . '/pied-de-page.php'; ?>