<?php
/**
 * =====================================================
 * FICHIER: views/joueurs/ajouter-joueur.php
 * ROLE: Formulaire d'ajout d'un nouveau joueur
 * DESCRIPTION: Permet de saisir toutes les informations d'un joueur
 * =====================================================
 */

// Initialisation des variables si non definies
if (!isset($donneesFormulaire)) {
    $donneesFormulaire = [];
}

// Valeurs par defaut pour le formulaire
$defaultValues = [
    'nom_joueur'          => '',
    'prenom_joueur'       => '',
    'date_naissance'      => '',
    'numero_licence'      => '',
    'statut_joueur'       => 'Actif',
    'taille_cm'           => '',
    'poids_kg'            => '',
    'commentaires_joueur' => ''
];

// Fusion des valeurs par defaut avec les donnees existantes
$donneesFormulaire = array_merge($defaultValues, $donneesFormulaire);

// Calcul des dates min et max pour l'age (16 a 50 ans)
$dateMin = date('Y-m-d', strtotime('-50 years'));  // 50 ans max
$dateMax = date('Y-m-d', strtotime('-16 years'));  // 16 ans min

// Inclusion de l'en-tete
include CHEMIN_INCLUDES . '/entete.php';
?>

<!-- Conteneur principal du formulaire -->
<div class="conteneur-formulaire">

    <!-- Affichage des messages d'erreur -->
    <?php if (isset($messageErreur) && $messageErreur): ?>
    <div class="messages-systeme">
        <div class="message-erreur"><?php echo htmlspecialchars($messageErreur); ?></div>
    </div>
    <?php endif; ?>

    <!-- Affichage des messages de succes -->
    <?php if (isset($messageSucces) && $messageSucces): ?>
    <div class="messages-systeme">
        <div class="message-succes"><?php echo htmlspecialchars($messageSucces); ?></div>
    </div>
    <?php endif; ?>

    <!-- Carte du formulaire -->
    <div class="carte-formulaire">
        <div class="entete-formulaire">
            <h2>Ajouter un nouveau joueur</h2>
            <p>Remplissez le formulaire pour ajouter un joueur à l'équipe</p>
        </div>

        <!-- Formulaire d'ajout -->
        <form method="POST" action="index.php?controller=joueur&action=ajouter" class="formulaire-ajout" id="formAjoutJoueur">

            <!-- Grille du formulaire (2 colonnes) -->
            <div class="grille-formulaire">

                <!-- SECTION 1: Informations personnelles -->
                <div class="section-formulaire">
                    <h3 class="titre-section">Informations personnelles</h3>

                    <!-- Champ Nom -->
                    <div class="groupe-formulaire">
                        <label for="nom_joueur">Nom <span class="obligatoire">*</span></label>
                        <input type="text" id="nom_joueur" name="nom_joueur"
                               value="<?php echo htmlspecialchars($donneesFormulaire['nom_joueur'] ?? ''); ?>"
                               required maxlength="50" placeholder="Ex: Martin">
                    </div>

                    <!-- Champ Prenom -->
                    <div class="groupe-formulaire">
                        <label for="prenom_joueur">Prénom <span class="obligatoire">*</span></label>
                        <input type="text" id="prenom_joueur" name="prenom_joueur"
                               value="<?php echo htmlspecialchars($donneesFormulaire['prenom_joueur'] ?? ''); ?>"
                               required maxlength="50" placeholder="Ex: Pierre">
                    </div>

                    <!-- Champ Date de naissance -->
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

                    <!-- Champ Numero de licence -->
                    <div class="groupe-formulaire">
                        <label for="numero_licence">Numéro de licence <span class="obligatoire">*</span></label>
                        <input type="text" id="numero_licence" name="numero_licence"
                               value="<?php echo htmlspecialchars($donneesFormulaire['numero_licence'] ?? ''); ?>"
                               required maxlength="6" placeholder="Ex: LIC001">
                        <small class="aide-champ">Format: LIC001, LIC002, etc. (LIC + 3 chiffres)</small>
                        <div id="licence-error" class="error-message" style="display:none;"></div>
                    </div>

                    <!-- Champ Statut -->
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

                    <!-- Champ Taille -->
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

                    <!-- Champ Poids -->
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
                    Enregistrer le joueur
                </button>
                <a href="index.php?controller=joueur&action=index" class="bouton-secondaire">
                    Annuler et retour
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Inclusion du JavaScript de validation -->
<script src="../scripts/ajouter-joueur.js"></script>

<?php include CHEMIN_INCLUDES . '/pied-de-page.php'; ?>