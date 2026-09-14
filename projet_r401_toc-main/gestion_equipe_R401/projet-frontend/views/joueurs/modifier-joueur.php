<?php
/**
 * =====================================================
 * FICHIER: views/joueurs/modifier-joueur.php
 * ROLE: Formulaire de modification d'un joueur
 * DESCRIPTION: Permet de modifier les informations d'un joueur existant
 * =====================================================
 */

// Initialisation des variables si non definies
if (!isset($messageSucces)) {
    $messageSucces = '';
}
if (!isset($messageErreur)) {
    $messageErreur = '';
}
if (!isset($donneesFormulaire)) {
    $donneesFormulaire = [];
}
if (!isset($joueur)) {
    $joueur = [];
}
if (!isset($dateMin)) {
    $dateMin = date('Y-m-d', strtotime('-50 years'));
}
if (!isset($dateMax)) {
    $dateMax = date('Y-m-d', strtotime('-16 years'));
}

include CHEMIN_INCLUDES . '/entete.php';
?>

<!-- Conteneur principal -->
<div class="conteneur-formulaire">

    <!-- Messages d'erreur/succes -->
    <?php if ($messageSucces || $messageErreur): ?>
    <div class="messages-systeme">
        <?php if ($messageSucces): ?>
            <div class="message-succes"><?php echo htmlspecialchars($messageSucces); ?></div>
        <?php endif; ?>
        <?php if ($messageErreur): ?>
            <div class="message-erreur"><?php echo htmlspecialchars($messageErreur); ?></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Carte du formulaire -->
    <div class="carte-formulaire">
        <div class="entete-formulaire">
            <h2>Modifier le joueur</h2>
            <p>Modifiez les informations du joueur : <?php echo htmlspecialchars(($joueur['prenom_joueur'] ?? '') . ' ' . ($joueur['nom_joueur'] ?? '')); ?></p>
        </div>

        <!-- Formulaire de modification -->
        <form method="POST" action="index.php?controller=joueur&action=modifier&id=<?php echo $joueur['id_joueur'] ?? ''; ?>" class="formulaire-ajout" id="formModifierJoueur">

            <!-- Grille du formulaire (2 colonnes) -->
            <div class="grille-formulaire">

                <!-- SECTION 1: Informations personnelles -->
                <div class="section-formulaire">
                    <h3 class="titre-section">Informations personnelles</h3>

                    <div class="groupe-formulaire">
                        <label for="nom_joueur">Nom <span class="obligatoire">*</span></label>
                        <input type="text" id="nom_joueur" name="nom_joueur"
                               value="<?php echo htmlspecialchars($donneesFormulaire['nom_joueur'] ?? ''); ?>"
                               required maxlength="50" placeholder="Ex: Martin">
                    </div>

                    <div class="groupe-formulaire">
                        <label for="prenom_joueur">Prénom <span class="obligatoire">*</span></label>
                        <input type="text" id="prenom_joueur" name="prenom_joueur"
                               value="<?php echo htmlspecialchars($donneesFormulaire['prenom_joueur'] ?? ''); ?>"
                               required maxlength="50" placeholder="Ex: Pierre">
                    </div>

                    <div class="groupe-formulaire">
                        <label for="date_naissance">Date de naissance <span class="obligatoire">*</span></label>
                        <input type="date" id="date_naissance" name="date_naissance"
                               value="<?php echo htmlspecialchars($donneesFormulaire['date_naissance'] ?? ''); ?>"
                               required min="<?php echo $dateMin; ?>" max="<?php echo $dateMax; ?>">
                        <small class="aide-champ">Âge entre 16 et 50 ans</small>
                    </div>
                </div>

                <!-- SECTION 2: Informations administratives -->
                <div class="section-formulaire">
                    <h3 class="titre-section">Informations administratives</h3>

                    <div class="groupe-formulaire">
                        <label for="numero_licence">Numéro de licence <span class="obligatoire">*</span></label>
                        <input type="text" id="numero_licence" name="numero_licence"
                               value="<?php echo htmlspecialchars($donneesFormulaire['numero_licence'] ?? ''); ?>"
                               required maxlength="6" placeholder="Ex: LIC001">
                        <small class="aide-champ">Format: LIC001, LIC002, etc. (LIC + 3 chiffres)</small>
                        <div id="licence-error" class="error-message" style="display:none;"></div>
                    </div>

                    <div class="groupe-formulaire">
                        <label for="statut_joueur">Statut <span class="obligatoire">*</span></label>
                        <select id="statut_joueur" name="statut_joueur" required>
                            <option value="Actif" <?php echo ($donneesFormulaire['statut_joueur'] ?? '') == 'Actif' ? 'selected' : ''; ?>>Actif</option>
                            <option value="Blessé" <?php echo ($donneesFormulaire['statut_joueur'] ?? '') == 'Blessé' ? 'selected' : ''; ?>>Blessé</option>
                            <option value="Suspendu" <?php echo ($donneesFormulaire['statut_joueur'] ?? '') == 'Suspendu' ? 'selected' : ''; ?>>Suspendu</option>
                            <option value="Absent" <?php echo ($donneesFormulaire['statut_joueur'] ?? '') == 'Absent' ? 'selected' : ''; ?>>Absent</option>
                        </select>
                    </div>
                </div>

                <!-- SECTION 3: Informations physiques -->
                <div class="section-formulaire">
                    <h3 class="titre-section">Informations physiques</h3>

                    <div class="groupe-formulaire">
                        <label for="taille_cm">Taille (cm) <span class="obligatoire">*</span></label>
                        <div class="groupe-avec-unite">
                            <input type="number" id="taille_cm" name="taille_cm"
                                   value="<?php echo htmlspecialchars($donneesFormulaire['taille_cm'] ?? ''); ?>"
                                   required min="100" max="250" step="1" placeholder="180">
                            <span class="unite">cm</span>
                        </div>
                        <small class="aide-champ">Entre 100 et 250 cm</small>
                    </div>

                    <div class="groupe-formulaire">
                        <label for="poids_kg">Poids (kg) <span class="obligatoire">*</span></label>
                        <div class="groupe-avec-unite">
                            <input type="number" id="poids_kg" name="poids_kg"
                                   value="<?php echo htmlspecialchars($donneesFormulaire['poids_kg'] ?? ''); ?>"
                                   required min="30" max="150" step="0.1" placeholder="75.5">
                            <span class="unite">kg</span>
                        </div>
                        <small class="aide-champ">Entre 30 et 150 kg</small>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="actions-formulaire">
                <button type="submit" class="bouton-principal" id="btnSubmit">
                    Enregistrer les modifications
                </button>
                <a href="index.php?controller=joueur&action=index" class="bouton-secondaire">
                    Annuler et retour
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Inclusion du JavaScript pour la validation -->
<script src="../scripts/modifier-joueur.js"></script>

<?php include CHEMIN_INCLUDES . '/pied-de-page.php'; ?>