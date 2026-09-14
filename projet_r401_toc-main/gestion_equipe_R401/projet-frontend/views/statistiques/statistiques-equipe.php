<?php
/**
 * =====================================================
 * FICHIER: views/statistiques/statistiques-equipe.php
 * ROLE: Page des statistiques de l'equipe
 * DESCRIPTION: Affiche les statistiques globales et individuelles
 * =====================================================
 */

// Initialisation des variables
if (!isset($statsJoueurs)) $statsJoueurs = [];
if (!isset($totalMatchs)) $totalMatchs = 0;
if (!isset($victoires)) $victoires = 0;
if (!isset($defaites)) $defaites = 0;
if (!isset($nuls)) $nuls = 0;
if (!isset($pourcentageVictoires)) $pourcentageVictoires = 0;
if (!isset($pourcentageDefaites)) $pourcentageDefaites = 0;
if (!isset($pourcentageNuls)) $pourcentageNuls = 0;
if (!isset($totalJoueurs)) $totalJoueurs = 0;
?>

<div class="conteneur-statistiques">

    <!-- ============================================= -->
    <!-- SECTION STATISTIQUES GLOBALES DE L'EQUIPE      -->
    <!-- ============================================= -->
    <div class="section-stats-globale">
        <h2><i class="fas fa-chart-line"></i> Statistiques globales de l'équipe</h2>

        <!-- Cartes resume -->
        <div class="carte-resume">
            <div class="resume-item">
                <i class="fas fa-futbol"></i>
                <span class="resume-label">Matchs joués</span>
                <span class="resume-valeur"><?php echo $totalMatchs; ?></span>
            </div>
            <div class="resume-item">
                <i class="fas fa-trophy"></i>
                <span class="resume-label">Victoires</span>
                <span class="resume-valeur"><?php echo $victoires; ?></span>
            </div>
            <div class="resume-item">
                <i class="fas fa-times-circle"></i>
                <span class="resume-label">Défaites</span>
                <span class="resume-valeur"><?php echo $defaites; ?></span>
            </div>
            <div class="resume-item">
                <i class="fas fa-handshake"></i>
                <span class="resume-label">Nuls</span>
                <span class="resume-valeur"><?php echo $nuls; ?></span>
            </div>
            <div class="resume-item">
                <i class="fas fa-users"></i>
                <span class="resume-label">Joueurs</span>
                <span class="resume-valeur"><?php echo $totalJoueurs; ?></span>
            </div>
        </div>

        <!-- Grille des pourcentages -->
        <div class="grille-stats-globales">
            <!-- Carte Victoires -->
            <div class="stat-card-global victoires">
                <div class="stat-icone"><i class="fas fa-trophy"></i></div>
                <div class="stat-contenu">
                    <div class="stat-label">Victoires</div>
                    <div class="stat-valeur"><?php echo $victoires; ?></div>
                    <div class="stat-pourcentage"><?php echo $pourcentageVictoires; ?>%</div>
                    <div class="barre-progression">
                        <div class="progression" style="width: <?php echo $pourcentageVictoires; ?>%;"></div>
                    </div>
                </div>
            </div>

            <!-- Carte Defaites -->
            <div class="stat-card-global defaites">
                <div class="stat-icone"><i class="fas fa-times-circle"></i></div>
                <div class="stat-contenu">
                    <div class="stat-label">Défaites</div>
                    <div class="stat-valeur"><?php echo $defaites; ?></div>
                    <div class="stat-pourcentage"><?php echo $pourcentageDefaites; ?>%</div>
                    <div class="barre-progression">
                        <div class="progression" style="width: <?php echo $pourcentageDefaites; ?>%;"></div>
                    </div>
                </div>
            </div>

            <!-- Carte Nuls -->
            <div class="stat-card-global nuls">
                <div class="stat-icone"><i class="fas fa-handshake"></i></div>
                <div class="stat-contenu">
                    <div class="stat-label">Matchs nuls</div>
                    <div class="stat-valeur"><?php echo $nuls; ?></div>
                    <div class="stat-pourcentage"><?php echo $pourcentageNuls; ?>%</div>
                    <div class="barre-progression">
                        <div class="progression" style="width: <?php echo $pourcentageNuls; ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- SECTION STATISTIQUES INDIVIDUELLES             -->
    <!-- ============================================= -->
    <div class="section-stats-joueurs">
        <h2><i class="fas fa-users"></i> Statistiques individuelles des joueurs</h2>

        <div class="tableau-donnees">
            <?php if (!empty($statsJoueurs)): ?>
                <table class="tableau-classique">
                    <thead>
                        <tr>
                            <th>Joueur</th>
                            <th>Statut</th>
                            <th>Poste Préféré</th>
                            <th>Tit.</th>
                            <th>Remp.</th>
                            <th>Note</th>
                            <th>Sél. conséc.</th>
                            <th>% Vict.</th>
                        </thead>
                    <tbody>
                        <?php foreach ($statsJoueurs as $joueur): ?>
                            <tr class="joueur-ligne-stat">
                                <!-- Colonne Joueur -->
                                <td class="joueur-cell">
                                    <div class="joueur-avatar-mini">
                                        <?php echo strtoupper(substr($joueur['prenom'] ?? '', 0, 1) . substr($joueur['nom'] ?? '', 0, 1)); ?>
                                    </div>
                                    <div class="joueur-info-stat">
                                        <strong><?php echo htmlspecialchars(($joueur['prenom'] ?? '') . ' ' . ($joueur['nom'] ?? '')); ?></strong>
                                        <div class="joueur-licence-mini">#<?php echo htmlspecialchars($joueur['numero_licence'] ?? ''); ?></div>
                                    </div>
                                </td>

                                <!-- Colonne Statut -->
                                <td class="text-center">
                                    <span class="badge-statut statut-<?php echo strtolower(str_replace(['é','è','ê','â','û'], ['e','e','e','a','u'], $joueur['statut'] ?? 'Inconnu')); ?>">
                                        <?php echo $joueur['statut'] ?? 'Inconnu'; ?>
                                    </span>
                                </td>

                                <!-- Colonne Poste Prefere -->
                                <td class="poste-prefere">
                                    <?php
                                    $postePrefere = $joueur['poste_prefere'] ?? 'N/A';
                                    echo htmlspecialchars($postePrefere);
                                    ?>
                                </td>

                                <!-- Colonne Titularisations -->
                                <td class="text-center">
                                    <span class="badge-nombre titulaire"><?php echo $joueur['nb_titularisations'] ?? 0; ?></span>
                                </td>

                                <!-- Colonne Remplacements -->
                                <td class="text-center">
                                    <span class="badge-nombre remplacant"><?php echo $joueur['nb_remplacements'] ?? 0; ?></span>
                                </td>

                                <!-- Colonne Note -->
                                <td class="text-center">
                                    <div class="note-cell">
                                        <div class="note-valeur"><?php echo number_format($joueur['moyenne_evaluations'] ?? 0, 1); ?></div>
                                        <div class="etoiles-mini">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <span class="<?php echo $i <= round(($joueur['moyenne_evaluations'] ?? 0)) ? 'active' : ''; ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Colonne Selections consecutives -->
                                <td class="text-center">
                                    <span class="badge-nombre consecutif <?php echo ($joueur['selections_consecutives'] ?? 0) > 5 ? 'record' : ''; ?>">
                                        <?php echo $joueur['selections_consecutives'] ?? 0; ?>
                                    </span>
                                </td>

                                <!-- Colonne Pourcentage victoires -->
                                <td class="text-center">
                                    <div class="pourcentage-cell">
                                        <strong><?php echo $joueur['pourcentage_victoires'] ?? 0; ?>%</strong>
                                        <div class="mini-barre">
                                            <div class="mini-progression" style="width: <?php echo $joueur['pourcentage_victoires'] ?? 0; ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Legende -->
                <div class="legende-stats">
                    <div class="legende-item">
                        <span class="legende-couleur titulaire"></span>
                        <span>Titularisations</span>
                    </div>
                    <div class="legende-item">
                        <span class="legende-couleur remplacant"></span>
                        <span>Remplaçants</span>
                    </div>
                    <div class="legende-item">
                        <span class="legende-couleur consecutif"></span>
                        <span>Sélections consécutives (>5 en rouge)</span>
                    </div>
                    <div class="legende-item">
                        <span class="legende-couleur note"></span>
                        <span>Évaluation moyenne (★ = 1 point)</span>
                    </div>
                </div>

            <?php else: ?>
                <!-- Message: aucune donnee -->
                <div class="aucune-donnee">
                    <i class="fas fa-chart-simple"></i>
                    <h3>Aucune donnée de statistiques</h3>
                    <p>Les statistiques seront disponibles après les premiers matchs.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>