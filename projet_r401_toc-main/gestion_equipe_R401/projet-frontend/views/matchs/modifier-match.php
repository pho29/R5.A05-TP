<?php
/**
 * =====================================================
 * FICHIER: views/matchs/modifier-match.php
 * ROLE: Formulaire de modification d'un match
 * DESCRIPTION: Permet de modifier les informations d'un match existant
 * =====================================================
 */

include CHEMIN_INCLUDES . '/entete.php';
?>

<!-- Conteneur principal -->
<div class="conteneur-formulaire">

    <!-- Messages systeme -->
    <?php if (isset($messageSucces) && $messageSucces || isset($messageErreur) && $messageErreur): ?>
    <div class="messages-systeme">
        <?php if (isset($messageSucces) && $messageSucces): ?>
            <div class="message-succes"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($messageSucces); ?></div>
        <?php endif; ?>
        <?php if (isset($messageErreur) && $messageErreur): ?>
            <div class="message-erreur"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($messageErreur); ?></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Carte du formulaire -->
    <div class="carte-formulaire">
        <div class="entete-formulaire">
            <h2><i class="fas fa-edit"></i> Modifier le match</h2>
            <p>Match contre : <?php echo htmlspecialchars($match['equipe_adverse'] ?? ''); ?></p>
        </div>

        <!-- Formulaire de modification -->
        <form method="POST" action="index.php?controller=match&action=modifier&id=<?php echo $match['id_match'] ?? ''; ?>" class="formulaire-ajout">
            <div class="corps-formulaire">
                <div class="grille-formulaire">

                    <!-- SECTION 1: Informations du match -->
                    <div class="section-formulaire">
                        <h3 class="titre-section">Informations du match</h3>

                        <div class="groupe-formulaire">
                            <label for="adversaire" class="obligatoire">Équipe adverse</label>
                            <input type="text" id="adversaire" name="adversaire"
                                   value="<?php echo htmlspecialchars($donneesFormulaire['adversaire'] ?? ''); ?>"
                                   required maxlength="100" placeholder="Ex: FC Barcelone">
                            <small class="aide-champ">Nom de l'équipe adverse</small>
                        </div>

                        <div class="groupe-formulaire">
                            <label for="date_match" class="obligatoire">Date du match</label>
                            <input type="date" id="date_match" name="date_match"
                                   value="<?php echo htmlspecialchars($donneesFormulaire['date_match'] ?? ''); ?>"
                                   required min="<?php echo $dateMin; ?>" max="<?php echo $dateMax; ?>">
                            <small class="aide-champ">Date de la rencontre</small>
                        </div>

                        <div class="groupe-formulaire">
                            <label for="heure_match" class="obligatoire">Heure du match</label>
                            <input type="time" id="heure_match" name="heure_match"
                                   value="<?php echo htmlspecialchars($donneesFormulaire['heure_match'] ?? ''); ?>"
                                   required>
                            <small class="aide-champ">Heure de coup d'envoi</small>
                        </div>
                    </div>

                    <!-- SECTION 2: Localisation -->
                    <div class="section-formulaire">
                        <h3 class="titre-section">Localisation</h3>

                        <div class="groupe-formulaire">
                            <label for="type_match" class="obligatoire">Type de match</label>
                            <select id="type_match" name="type_match" required>
                                <option value="Domicile" <?php echo ($donneesFormulaire['type_match'] ?? '') == 'Domicile' ? 'selected' : ''; ?>>
                                    Domicile
                                </option>
                                <option value="Extérieur" <?php echo ($donneesFormulaire['type_match'] ?? '') == 'Extérieur' ? 'selected' : ''; ?>>
                                    Extérieur
                                </option>
                            </select>
                            <small class="aide-champ">Match à domicile ou à l'extérieur</small>
                        </div>

                        <div class="groupe-formulaire">
                            <label for="lieu_rencontre" class="obligatoire">Lieu de rencontre</label>
                            <input type="text" id="lieu_rencontre" name="lieu_rencontre"
                                   value="<?php echo htmlspecialchars($donneesFormulaire['lieu_rencontre'] ?? ''); ?>"
                                   required maxlength="200"
                                   placeholder="Ex: Stade Municipal, Salle omnisports...">
                            <small class="aide-champ">Nom du stade ou de la salle</small>
                        </div>
                    </div>

                    <!-- SECTION 3: Score -->
                    <div class="section-formulaire">
                        <h3 class="titre-section">Score</h3>
                        <p class="info-section">
                            <i class="fas fa-info-circle"></i>
                            Le résultat sera calculé automatiquement selon les scores.
                        </p>

                        <div class="grille-score">
                            <div class="groupe-formulaire">
                                <label for="score_equipe">Score de votre équipe</label>
                                <input type="number" id="score_equipe" name="score_equipe"
                                       value="<?php echo htmlspecialchars($donneesFormulaire['score_equipe'] ?? ''); ?>"
                                       min="0" max="99" placeholder="0">
                            </div>
                            <div class="separateur-score"><span>-</span></div>
                            <div class="groupe-formulaire">
                                <label for="score_adversaire">Score adverse</label>
                                <input type="number" id="score_adversaire" name="score_adversaire"
                                       value="<?php echo htmlspecialchars($donneesFormulaire['score_adversaire'] ?? ''); ?>"
                                       min="0" max="99" placeholder="0">
                            </div>
                        </div>

                        <!-- Affichage du resultat actuel si existant -->
                        <?php if (isset($donneesFormulaire['resultat_match']) && $donneesFormulaire['resultat_match'] != 'À venir'): ?>
                        <div class="resultat-actuel">
                            <i class="fas fa-flag-checkered"></i>
                            Résultat actuel : <strong><?php echo $donneesFormulaire['resultat_match']; ?></strong>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- SECTION 4: Commentaires -->
                    <div class="section-formulaire pleine-largeur">
                        <h3 class="titre-section">Commentaires et notes</h3>

                        <div class="groupe-formulaire">
                            <label for="commentaires_match">Observations</label>
                            <textarea id="commentaires_match" name="commentaires_match"
                                      rows="4"
                                      placeholder="Notes stratégiques, état du terrain, conditions météo, absences prévues..."
                                      maxlength="1000"><?php echo htmlspecialchars($donneesFormulaire['commentaires_match'] ?? ''); ?></textarea>
                            <small class="aide-champ">Maximum 1000 caractères</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="actions-formulaire">
                <button type="submit" class="bouton-principal">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
                <a href="index.php?controller=match&action=index" class="bouton-secondaire">
                    <i class="fas fa-arrow-left"></i> Annuler et retour
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Inclusion du JavaScript pour l'ajout de match (reutilisable) -->
<script src="../scripts/ajouter-match.js"></script>

<?php include CHEMIN_INCLUDES . '/pied-de-page.php'; ?>