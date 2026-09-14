<?php
/**
 * =====================================================
 * FICHIER: views/matchs/saisir-resultat.php
 * ROLE: Formulaire de saisie du resultat d'un match
 * DESCRIPTION: Permet de saisir le score final d'un match
 * =====================================================
 */

include CHEMIN_INCLUDES . '/entete.php';

// Formatage de la date et de l'heure
$dateHeure = new DateTime($match['date_heure_match']);
$dateMatch = $dateHeure->format('d/m/Y');
$heureMatch = $dateHeure->format('H:i');
?>

<!-- Conteneur principal -->
<div class="conteneur-formulaire">

    <!-- Messages systeme -->
    <?php if ($messageErreur): ?>
        <div class="message-erreur"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($messageErreur); ?></div>
    <?php endif; ?>

    <?php if ($messageSucces): ?>
        <div class="message-succes"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($messageSucces); ?></div>
    <?php endif; ?>

    <!-- Carte du formulaire -->
    <div class="carte-formulaire">
        <div class="entete-formulaire">
            <h2><i class="fas fa-flag-checkered"></i> Saisir le résultat</h2>
            <p>Match du <?php echo $dateMatch; ?> à <?php echo $heureMatch; ?> contre <strong><?php echo htmlspecialchars($match['equipe_adverse']); ?></strong></p>
        </div>

        <!-- Informations du lieu -->
        <div class="info-match">
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>Lieu : <strong><?php echo htmlspecialchars($match['lieu_rencontre'] ?? $match['lieu_match']); ?></strong></span>
            </div>
        </div>

        <!-- Formulaire de saisie -->
        <form method="POST" action="index.php?controller=match&action=resultat&id=<?php echo $idMatch; ?>" class="formulaire-resultat">

            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <!-- Grille des scores -->
            <div class="grille-score-large">
                <div class="groupe-formulaire">
                    <label for="score_equipe">Score de votre équipe</label>
                    <input type="number" id="score_equipe" name="score_equipe"
                           value="<?php echo isset($_POST['score_equipe']) ? htmlspecialchars($_POST['score_equipe']) : '0'; ?>"
                           min="0" max="99" required>
                </div>

                <div class="separateur-score-large">
                    <span>-</span>
                </div>

                <div class="groupe-formulaire">
                    <label for="score_adversaire">Score adverse</label>
                    <input type="number" id="score_adversaire" name="score_adversaire"
                           value="<?php echo isset($_POST['score_adversaire']) ? htmlspecialchars($_POST['score_adversaire']) : '0'; ?>"
                           min="0" max="99" required>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="actions-formulaire">
                <button type="submit" class="bouton-principal">
                    <i class="fas fa-save"></i> Enregistrer le résultat
                </button>
                <a href="index.php?controller=match&action=index" class="bouton-secondaire">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Inclusion du JavaScript pour la saisie du resultat -->
<script src="<?php echo $prefixe; ?>scripts/saisir-resultat.js"></script>

<?php include CHEMIN_INCLUDES . '/pied-de-page.php'; ?>