<?php
/**
 * =====================================================
 * FICHIER: views/matchs/details-match.php
 * ROLE: Page de detail d'un match
 * DESCRIPTION: Affiche toutes les informations d'un match
 * =====================================================
 */

include CHEMIN_INCLUDES . '/entete.php';
?>

<!-- Conteneur principal -->
<div class="conteneur-details-match">

    <!-- ========================================= -->
    <!-- CARTE PRINCIPALE DU MATCH                  -->
    <!-- ========================================= -->
    <div class="carte-match-principal">

        <!-- En-tete avec date et heure -->
        <div class="entete-match-detail">
            <div class="info-date">
                <div class="date-badge">
                    <i class="fas fa-calendar-alt"></i>
                    <?php echo $dateMatch; ?>
                </div>
                <div class="heure-badge">
                    <i class="fas fa-clock"></i>
                    <?php echo $heureMatch; ?>
                </div>
            </div>
            <div class="badge-lieu badge-<?php echo strtolower($match['lieu_match'] ?? 'domicile'); ?>">
                <?php echo ($match['lieu_match'] ?? 'Domicile') == 'Domicile' ? '<i class="fas fa-home"></i>' : '<i class="fas fa-plane"></i>'; ?>
                <?php echo $match['lieu_match'] ?? 'Domicile'; ?>
            </div>
        </div>

        <!-- Lieu de rencontre -->
        <?php if (!empty($match['lieu_rencontre'])): ?>
        <div class="lieu-rencontre">
            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($match['lieu_rencontre']); ?>
        </div>
        <?php endif; ?>

        <!-- Section VS (affrontement) -->
        <div class="vs-section">
            <div class="equipe notre-equipe">
                <div class="nom-equipe">Notre Équipe</div>
                <div class="score-equipe">
                    <?php echo $match['score_equipe'] ?? 0; ?>
                </div>
            </div>
            <div class="separateur-vs">
                <span class="vs-text">VS</span>
            </div>
            <div class="equipe equipe-adverse">
                <div class="nom-equipe"><?php echo htmlspecialchars($match['equipe_adverse'] ?? 'Adversaire inconnu'); ?></div>
                <div class="score-equipe">
                    <?php echo $match['score_adverse'] ?? 0; ?>
                </div>
            </div>
        </div>

        <!-- Resultat du match -->
        <?php if (isset($match['resultat_match']) && $match['resultat_match'] != 'À venir'): ?>
        <div class="resultat-final">
            <span class="badge-resultat-large badge-<?php echo strtolower($match['resultat_match']); ?>">
                <?php
                if ($match['resultat_match'] == 'Victoire') echo '<i class="fas fa-trophy"></i>';
                elseif ($match['resultat_match'] == 'Défaite') echo '<i class="fas fa-times-circle"></i>';
                else echo '<i class="fas fa-equals"></i>';
                ?>
                <?php echo strtoupper($match['resultat_match']); ?>
            </span>
        </div>
        <?php else: ?>
        <div class="match-a-venir">
            <i class="fas fa-hourglass-half"></i>
            <span>Match à venir</span>
        </div>
        <?php endif; ?>

        <!-- Actions disponibles -->
        <div class="actions-match-detail">
            <?php if (isset($match['resultat_match']) && $match['resultat_match'] == 'À venir'): ?>
            <a href="index.php?controller=match&action=composer&id=<?php echo $match['id_match']; ?>" class="btn-action-detail btn-composer">
                <i class="fas fa-users-cog"></i> Composer l'équipe
            </a>
            <?php endif; ?>
            <a href="index.php?controller=match&action=index" class="btn-action-detail btn-retour">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- STATISTIQUES RAPIDES                       -->
    <!-- ========================================= -->
    <div class="grille-stats-match">
        <div class="stat-card-match">
            <div class="stat-icone" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-contenu">
                <div class="stat-label">Joueurs sélectionnés</div>
                <div class="stat-valeur"><?php echo count($joueurs); ?></div>
            </div>
        </div>
        <div class="stat-card-match">
            <div class="stat-icone" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%);">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-contenu">
                <div class="stat-label">Titulaires</div>
                <div class="stat-valeur"><?php echo $nbTitulaires; ?></div>
            </div>
        </div>
        <div class="stat-card-match">
            <div class="stat-icone" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="stat-contenu">
                <div class="stat-label">Remplaçants</div>
                <div class="stat-valeur"><?php echo $nbRemplacants; ?></div>
            </div>
        </div>
        <div class="stat-card-match">
            <div class="stat-icone" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-contenu">
                <div class="stat-label">Note moyenne</div>
                <div class="stat-valeur"><?php echo $moyenneNote; ?>/5</div>
            </div>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- COMMENTAIRES DU MATCH                      -->
    <!-- ========================================= -->
    <?php if (!empty($match['commentaires_match'])): ?>
    <div class="carte-commentaires">
        <h3><i class="fas fa-comment-alt"></i> Commentaires</h3>
        <p><?php echo nl2br(htmlspecialchars($match['commentaires_match'])); ?></p>
    </div>
    <?php endif; ?>

    <!-- ========================================= -->
    <!-- LISTE DES JOUEURS                          -->
    <!-- ========================================= -->
    <?php if (!empty($joueurs)): ?>
    <div class="carte-joueurs-match">
        <h3><i class="fas fa-users"></i> Composition de l'équipe</h3>

        <!-- SECTION TITULAIRES -->
        <?php
        $titulaires = array_filter($joueurs, function($j) {
            return isset($j['titulaire']) && $j['titulaire'] == 1;
        });
        if (!empty($titulaires)):
        ?>
        <div class="section-joueurs">
            <h4 class="titre-section-joueurs">
                <i class="fas fa-user-check"></i> Titulaires (<?php echo count($titulaires); ?>)
            </h4>
            <div class="grille-joueurs">
                <?php foreach ($titulaires as $joueur): ?>
                <div class="carte-joueur-match">
                    <div class="joueur-header">
                        <div class="joueur-avatar">
                            <?php echo strtoupper(substr($joueur['prenom_joueur'] ?? '', 0, 1) . substr($joueur['nom_joueur'] ?? '', 0, 1)); ?>
                        </div>
                        <div class="joueur-info">
                            <div class="joueur-nom"><?php echo htmlspecialchars(($joueur['prenom_joueur'] ?? '') . ' ' . ($joueur['nom_joueur'] ?? '')); ?></div>
                            <div class="joueur-licence">#<?php echo htmlspecialchars($joueur['numero_licence'] ?? ''); ?></div>
                            <?php if (!empty($joueur['libelle_poste'])): ?>
                            <div class="joueur-role" style="font-size: 12px; color: #3498db; margin-top: 5px;">
                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($joueur['libelle_poste']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="joueur-stats">
                        <div class="stat-item">
                            <i class="fas fa-futbol"></i>
                            <span><?php echo $joueur['points_marques'] ?? 0; ?> buts</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock"></i>
                            <span><?php echo $joueur['temps_joue_minutes'] ?? 0; ?> min</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><?php echo $joueur['fautes_commises'] ?? 0; ?> fautes</span>
                        </div>
                        <div class="stat-item note">
                            <i class="fas fa-star"></i>
                            <span><?php echo $joueur['evaluation'] ?? 0; ?>/5</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- SECTION REMPLACANTS -->
        <?php
        $remplacants = array_filter($joueurs, function($j) {
            return isset($j['titulaire']) && $j['titulaire'] == 0;
        });
        if (!empty($remplacants)):
        ?>
        <div class="section-joueurs">
            <h4 class="titre-section-joueurs">
                <i class="fas fa-exchange-alt"></i> Remplaçants (<?php echo count($remplacants); ?>)
            </h4>
            <div class="grille-joueurs">
                <?php foreach ($remplacants as $joueur): ?>
                <div class="carte-joueur-match remplacant">
                    <div class="joueur-header">
                        <div class="joueur-avatar">
                            <?php echo strtoupper(substr($joueur['prenom_joueur'] ?? '', 0, 1) . substr($joueur['nom_joueur'] ?? '', 0, 1)); ?>
                        </div>
                        <div class="joueur-info">
                            <div class="joueur-nom"><?php echo htmlspecialchars(($joueur['prenom_joueur'] ?? '') . ' ' . ($joueur['nom_joueur'] ?? '')); ?></div>
                            <div class="joueur-licence">#<?php echo htmlspecialchars($joueur['numero_licence'] ?? ''); ?></div>
                            <?php if (!empty($joueur['libelle_poste'])): ?>
                            <div class="joueur-role" style="font-size: 12px; color: #f39c12; margin-top: 5px;">
                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($joueur['libelle_poste']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="joueur-stats">
                        <div class="stat-item">
                            <i class="fas fa-futbol"></i>
                            <span><?php echo $joueur['points_marques'] ?? 0; ?> pts</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock"></i>
                            <span><?php echo $joueur['temps_joue_minutes'] ?? 0; ?> min</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><?php echo $joueur['fautes_commises'] ?? 0; ?> fautes</span>
                        </div>
                        <div class="stat-item note">
                            <i class="fas fa-star"></i>
                            <span><?php echo $joueur['evaluation'] ?? 0; ?>/5</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- Message si aucun joueur n'est selectionne -->
    <div class="aucune-donnee">
        <i class="fas fa-users-slash"></i>
        <h3>Aucun joueur sélectionné</h3>
        <p>Aucun joueur n'a encore été sélectionné pour ce match.</p>
        <a href="index.php?controller=match&action=composer&id=<?php echo $match['id_match']; ?>" class="bouton-principal">
            <i class="fas fa-users-cog"></i> Composer l'équipe
        </a>
    </div>
    <?php endif; ?>
</div>

<?php include CHEMIN_INCLUDES . '/pied-de-page.php'; ?>