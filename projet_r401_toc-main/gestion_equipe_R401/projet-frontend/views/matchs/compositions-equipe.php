<?php
/**
 * =====================================================
 * FICHIER: views/matchs/compositions-equipe.php
 * ROLE: Page de composition d'equipe
 * DESCRIPTION: Permet de selectionner les titulaires et remplacants
 * =====================================================
 */

// Initialisation des variables
if (!isset($joueursActifs))        $joueursActifs        = [];
if (!isset($idsSelectionnes))      $idsSelectionnes      = [];
if (!isset($statutsSelectionnes))  $statutsSelectionnes  = [];
if (!isset($libellesSelectionnes)) $libellesSelectionnes = [];
if (!isset($remplacantsExistants)) $remplacantsExistants = [];

include CHEMIN_INCLUDES . '/entete.php';
?>

<!-- Conteneur principal -->
<div class="conteneur-composition">

    <!-- ========================================= -->
    <!-- CARTE D'INFORMATION DU MATCH               -->
    <!-- ========================================= -->
    <div class="carte-info-match">
        <div class="info-match-header">
            <div>
                <h2><i class="fas fa-futbol"></i> <?php echo htmlspecialchars($match['equipe_adverse'] ?? ''); ?></h2>
                <p class="meta-match">
                    <i class="fas fa-calendar"></i> <?php echo $dateMatch ?? ''; ?>
                    <span class="badge-lieu badge-<?php echo strtolower($match['lieu_match'] ?? ''); ?>">
                        <?php echo ($match['lieu_match'] ?? '') === 'Domicile'
                            ? '<i class="fas fa-home"></i>' : '<i class="fas fa-plane"></i>'; ?>
                        <?php echo $match['lieu_match'] ?? ''; ?>
                    </span>
                </p>
            </div>
            <div class="statut-match">
                <span class="badge-statut statut-preparation">
                    <i class="fas fa-hourglass-half"></i> Composition en cours
                </span>
            </div>
        </div>
    </div>

    <!-- Messages systeme -->
    <?php if (!empty($messageSucces)): ?>
        <div class="message-succes"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($messageSucces); ?></div>
    <?php endif; ?>
    <?php if (!empty($messageErreur)): ?>
        <div class="message-erreur"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($messageErreur); ?></div>
    <?php endif; ?>

    <!-- Formulaire de composition -->
    <form method="POST"
          action="index.php?controller=match&action=composer&id=<?php echo $match['id_match'] ?? ''; ?>"
          id="formulaireComposition"
          onsubmit="return validerFormulaire()">

        <!-- ========================================= -->
        <!-- COMPTEURS DE SELECTION                     -->
        <!-- ========================================= -->
        <div class="compteurs-composition">
            <div class="compteur titulaires" id="compteur-titulaires">
                <div class="compteur-icone"><i class="fas fa-user-check"></i></div>
                <div class="compteur-info">
                    <span class="compteur-label">Titulaires</span>
                    <div class="compteur-valeur"><span id="nb-titulaires">0</span><span class="compteur-max">/ 11</span></div>
                </div>
            </div>
            <div class="compteur remplacants" id="compteur-remplacants">
                <div class="compteur-icone"><i class="fas fa-exchange-alt"></i></div>
                <div class="compteur-info">
                    <span class="compteur-label">Remplaçants</span>
                    <div class="compteur-valeur"><span id="nb-remplacants">0</span><span class="compteur-max">/ 7</span></div>
                </div>
            </div>
            <div class="compteur total" id="compteur-total">
                <div class="compteur-icone"><i class="fas fa-users"></i></div>
                <div class="compteur-info">
                    <span class="compteur-label">Total</span>
                    <div class="compteur-valeur"><span id="nb-total">0</span><span class="compteur-max">/ 18</span></div>
                </div>
            </div>
        </div>

        <!-- ========================================= -->
        <!-- SECTION TITULAIRES                         -->
        <!-- ========================================= -->
        <div class="section-equipe">
            <div class="section-header">
                <h3><i class="fas fa-user-check"></i> Titulaires
                    <span class="badge-obligatoire">(11 obligatoires)</span>
                </h3>
                <span class="badge-compteur" id="badge-titulaires">0/11</span>
            </div>

            <!-- Grille des postes titulaires -->
            <div class="grille-titulaires">
                <?php
                // Liste des postes avec leurs icones et noms
                $postesTitulaires = [
                    'Gardien' => ['icon' => 'fa-shield-alt', 'name' => 'Gardien de but'],
                    'DCG'     => ['icon' => 'fa-shield-alt', 'name' => 'Défenseur central gauche'],
                    'DCD'     => ['icon' => 'fa-shield-alt', 'name' => 'Défenseur central droit'],
                    'AD'      => ['icon' => 'fa-arrow-right','name' => 'Arrière droit'],
                    'AG'      => ['icon' => 'fa-arrow-left', 'name' => 'Arrière gauche'],
                    'MD'      => ['icon' => 'fa-lock',       'name' => 'Milieu défensif'],
                    'MC'      => ['icon' => 'fa-futbol',     'name' => 'Milieu central'],
                    'MO'      => ['icon' => 'fa-bullseye',   'name' => 'Milieu offensif'],
                    'AiD'     => ['icon' => 'fa-arrow-right','name' => 'Ailier droit'],
                    'AiG'     => ['icon' => 'fa-arrow-left', 'name' => 'Ailier gauche'],
                    'AC'      => ['icon' => 'fa-crown',      'name' => 'Avant-centre'],
                ];

                foreach ($postesTitulaires as $code => $poste):
                    // Recherche du joueur actuel pour ce poste
                    $joueurActuel = null;
                    foreach ($idsSelectionnes as $idJoueur) {
                        if (
                            ($statutsSelectionnes[$idJoueur]  ?? '') === 'Titulaire' &&
                            ($libellesSelectionnes[$idJoueur] ?? '') === $code
                        ) {
                            foreach ($joueursActifs as $j) {
                                if ($j['id_joueur'] == $idJoueur) {
                                    $joueurActuel = $j;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                ?>
                <!-- Carte d'un poste titulaire -->
                <div class="carte-poste" data-poste="<?php echo $code; ?>">
                    <div class="poste-header">
                        <i class="fas <?php echo $poste['icon']; ?>"></i>
                        <span class="poste-nom"><?php echo $poste['name']; ?></span>
                    </div>
                    <select class="select-joueur selection-titulaire"
                            name="titulaire_<?php echo $code; ?>"
                            required
                            onchange="mettreAJourCompteurs(); verifierDoublons()"
                            data-poste="<?php echo $code; ?>">
                        <option value="">-- Sélectionner un joueur --</option>
                        <?php foreach ($joueursActifs as $joueur):
                            $sel = ($joueurActuel && $joueurActuel['id_joueur'] == $joueur['id_joueur']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $joueur['id_joueur']; ?>" <?php echo $sel; ?>
                                    data-id="<?php echo $joueur['id_joueur']; ?>"
                                    data-numero="<?php echo htmlspecialchars($joueur['numero_licence']); ?>"
                                    data-nom="<?php echo htmlspecialchars($joueur['nom_joueur']); ?>"
                                    data-prenom="<?php echo htmlspecialchars($joueur['prenom_joueur']); ?>"
                                    data-taille="<?php echo $joueur['taille_cm']; ?>"
                                    data-poids="<?php echo $joueur['poids_kg']; ?>">
                                #<?php echo htmlspecialchars($joueur['numero_licence']); ?> -
                                <?php echo htmlspecialchars($joueur['prenom_joueur'] . ' ' . $joueur['nom_joueur']); ?>
                                (<?php echo $joueur['taille_cm']; ?>cm/<?php echo $joueur['poids_kg']; ?>kg)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="joueur-details" id="details_<?php echo $code; ?>"></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ========================================= -->
        <!-- SECTION REMPLACANTS                         -->
        <!-- ========================================= -->
        <div class="section-equipe">
            <div class="section-header">
                <h3><i class="fas fa-exchange-alt"></i> Remplaçants
                    <span class="badge-optionnel">(Max 7)</span>
                </h3>
                <span class="badge-compteur" id="badge-remplacants">0/7</span>
            </div>

            <!-- Liste des remplacants (7 max) -->
            <div class="liste-remplacants">
                <?php for ($i = 0; $i < 7; $i++):
                    $joueurActuel = null;
                    $posteActuel  = '';
                    if ($i < count($remplacantsExistants)) {
                        $idJoueur = $remplacantsExistants[$i];
                        foreach ($joueursActifs as $j) {
                            if ($j['id_joueur'] == $idJoueur) {
                                $joueurActuel = $j;
                                break;
                            }
                        }
                        $posteActuel = $libellesSelectionnes[$idJoueur] ?? '';
                    }
                ?>
                <!-- Ligne d'un remplacant -->
                <div class="ligne-remplacant" data-index="<?php echo $i; ?>">
                    <div class="remplacant-joueur">
                        <div class="remplacant-numero">Remplaçant <?php echo $i + 1; ?></div>
                        <select class="select-joueur selection-remplacant"
                                name="remplacant_joueur_<?php echo $i; ?>"
                                onchange="mettreAJourCompteurs(); verifierDoublons(); afficherDetailsRemplacant(<?php echo $i; ?>)"
                                data-index="<?php echo $i; ?>">
                            <option value="">-- Sélectionner un joueur --</option>
                            <?php foreach ($joueursActifs as $joueur):
                                $sel = ($joueurActuel && $joueurActuel['id_joueur'] == $joueur['id_joueur']) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $joueur['id_joueur']; ?>" <?php echo $sel; ?>
                                        data-id="<?php echo $joueur['id_joueur']; ?>"
                                        data-numero="<?php echo htmlspecialchars($joueur['numero_licence']); ?>"
                                        data-nom="<?php echo htmlspecialchars($joueur['nom_joueur']); ?>"
                                        data-prenom="<?php echo htmlspecialchars($joueur['prenom_joueur']); ?>"
                                        data-taille="<?php echo $joueur['taille_cm']; ?>"
                                        data-poids="<?php echo $joueur['poids_kg']; ?>">
                                    #<?php echo htmlspecialchars($joueur['numero_licence']); ?> -
                                    <?php echo htmlspecialchars($joueur['prenom_joueur'] . ' ' . $joueur['nom_joueur']); ?>
                                    (<?php echo $joueur['taille_cm']; ?>cm/<?php echo $joueur['poids_kg']; ?>kg)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="joueur-details" id="details_remplacant_<?php echo $i; ?>"></div>
                    </div>
                    <div class="remplacant-poste">
                        <select class="select-poste" name="remplacant_poste_<?php echo $i; ?>">
                            <option value="">-- Choisir un poste --</option>
                            <option value="Gardien"  <?php echo $posteActuel === 'Gardien'  ? 'selected' : ''; ?>>Gardien</option>
                            <option value="DCG"      <?php echo $posteActuel === 'DCG'      ? 'selected' : ''; ?>>Défenseur central gauche</option>
                            <option value="DCD"      <?php echo $posteActuel === 'DCD'      ? 'selected' : ''; ?>>Défenseur central droit</option>
                            <option value="AD"       <?php echo $posteActuel === 'AD'       ? 'selected' : ''; ?>>Arrière droit</option>
                            <option value="AG"       <?php echo $posteActuel === 'AG'       ? 'selected' : ''; ?>>Arrière gauche</option>
                            <option value="MD"       <?php echo $posteActuel === 'MD'       ? 'selected' : ''; ?>>Milieu défensif</option>
                            <option value="MC"       <?php echo $posteActuel === 'MC'       ? 'selected' : ''; ?>>Milieu central</option>
                            <option value="MO"       <?php echo $posteActuel === 'MO'       ? 'selected' : ''; ?>>Milieu offensif</option>
                            <option value="AiD"      <?php echo $posteActuel === 'AiD'      ? 'selected' : ''; ?>>Ailier droit</option>
                            <option value="AiG"      <?php echo $posteActuel === 'AiG'      ? 'selected' : ''; ?>>Ailier gauche</option>
                            <option value="AC"       <?php echo $posteActuel === 'AC'       ? 'selected' : ''; ?>>Avant-centre</option>
                        </select>
                    </div>
                </div>
                <?php endfor; ?>
                <input type="hidden" name="remplacants_count" value="7">
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="actions-composition">
            <button type="submit" class="bouton-principal" id="btn-submit">
                <i class="fas fa-save"></i> Enregistrer la composition
            </button>
            <a href="index.php?controller=match&action=index" class="bouton-secondaire">
                <i class="fas fa-arrow-left"></i> Annuler
            </a>
        </div>
    </form>
</div>

<!-- Inclusion du JavaScript pour la composition -->
<script src="<?php echo $prefixe; ?>scripts/composition-equipe.js"></script>

<?php include CHEMIN_INCLUDES . '/pied-de-page.php'; ?>