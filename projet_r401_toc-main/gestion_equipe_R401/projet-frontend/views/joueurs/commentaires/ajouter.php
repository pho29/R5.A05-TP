<?php
/**
 * =====================================================
 * FICHIER: views/joueurs/commentaires/ajouter.php
 * ROLE: Formulaire d'ajout d'un commentaire pour un joueur
 * DESCRIPTION: Permet d'ajouter un commentaire textuel sur un joueur
 * =====================================================
 */

include CHEMIN_INCLUDES . '/entete.php';
?>

<!-- Conteneur principal -->
<div class="conteneur-formulaire">

    <!-- Affichage des messages d'erreur -->
    <?php if (isset($messageErreur) && $messageErreur): ?>
        <div class="message-erreur">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($messageErreur); ?>
        </div>
    <?php endif; ?>

    <!-- Affichage des messages de succes -->
    <?php if (isset($messageSucces) && $messageSucces): ?>
        <div class="message-succes">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($messageSucces); ?>
        </div>
    <?php endif; ?>

    <!-- Carte du formulaire -->
    <div class="carte-formulaire">

        <!-- En-tete avec icone -->
        <div class="entete-formulaire">
            <div class="entete-icon">
                <i class="fas fa-comment-plus"></i>
            </div>
            <div class="entete-texte">
                <h2>Ajouter un commentaire</h2>
                <p>Pour : <strong><?php echo htmlspecialchars($joueur['prenom_joueur'] . ' ' . $joueur['nom_joueur']); ?></strong></p>
            </div>
        </div>

        <!-- Informations resume du joueur -->
        <div class="info-joueur-mini">
            <div class="joueur-avatar-mini">
                <?php echo strtoupper(substr($joueur['prenom_joueur'], 0, 1) . substr($joueur['nom_joueur'], 0, 1)); ?>
            </div>
            <div class="joueur-details-mini">
                <span class="joueur-nom"><?php echo htmlspecialchars($joueur['prenom_joueur'] . ' ' . $joueur['nom_joueur']); ?></span>
                <span class="joueur-licence"><i class="fas fa-id-card"></i> <?php echo htmlspecialchars($joueur['numero_licence']); ?></span>
            </div>
            <div class="joueur-statut-mini">
                <span class="badge-statut statut-<?php echo strtolower($joueur['statut_joueur']); ?>">
                    <?php echo $joueur['statut_joueur']; ?>
                </span>
            </div>
        </div>

        <!-- Formulaire d'ajout de commentaire -->
        <form method="POST" action="index.php?controller=commentaire&action=ajouter&id=<?php echo $idJoueur; ?>" class="formulaire-commentaire">

            <!-- Token CSRF pour la securite -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <!-- Champ Commentaire -->
            <div class="groupe-formulaire">
                <label for="commentaire">
                    <i class="fas fa-pen"></i> Commentaire <span class="obligatoire">*</span>
                </label>
                <textarea id="commentaire" name="commentaire" rows="8"
                          placeholder="Saisissez votre commentaire ici..."
                          required maxlength="1000"><?php echo htmlspecialchars($_POST['commentaire'] ?? ''); ?></textarea>
                <small class="aide-champ">
                    <i class="fas fa-info-circle"></i> Maximum 1000 caracteres
                </small>
                <div class="compteur-caracteres">
                    <span id="compteur">0</span> / 1000 caracteres
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="actions-formulaire">
                <button type="submit" class="bouton-principal">
                    <i class="fas fa-save"></i> Enregistrer le commentaire
                </button>
                <a href="index.php?controller=commentaire&action=index&id=<?php echo $idJoueur; ?>" class="bouton-secondaire">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Inclusion du JavaScript pour les commentaires -->
<script src="<?php echo $prefixe; ?>scripts/commentaires.js"></script>

<?php include CHEMIN_INCLUDES . '/pied-de-page.php'; ?>