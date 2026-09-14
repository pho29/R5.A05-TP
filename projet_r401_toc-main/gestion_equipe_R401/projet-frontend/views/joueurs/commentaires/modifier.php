<?php
/**
 * =====================================================
 * FICHIER: views/joueurs/commentaires/modifier.php
 * ROLE: Formulaire de modification d'un commentaire
 * DESCRIPTION: Permet de modifier un commentaire existant
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
                <i class="fas fa-comment-edit"></i>
            </div>
            <div class="entete-texte">
                <h2>Modifier le commentaire</h2>
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

        <!-- Informations sur le commentaire (dates) -->
        <div class="info-commentaire">
            <div class="info-item">
                <i class="fas fa-calendar-alt"></i>
                <strong>Créé le :</strong>
                <?php
                $dateCreation = isset($commentaire['date_creation']) ? $commentaire['date_creation'] : $commentaire['date_ajout'] ?? 'now';
                echo date('d/m/Y à H:i', strtotime($dateCreation));
                ?>
            </div>
            <?php if (isset($commentaire['date_modification']) && $commentaire['date_modification']): ?>
            <div class="info-item">
                <i class="fas fa-edit"></i>
                <strong>Dernière modification :</strong>
                <?php echo date('d/m/Y à H:i', strtotime($commentaire['date_modification'])); ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Formulaire de modification -->
        <form method="POST" action="index.php?controller=commentaire&action=modifier&id=<?php echo $idCommentaire; ?>" class="formulaire-commentaire">

            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <!-- Champ Commentaire -->
            <div class="groupe-formulaire">
                <label for="commentaire">
                    <i class="fas fa-pen"></i> Commentaire <span class="obligatoire">*</span>
                </label>
                <textarea id="commentaire" name="commentaire" rows="8"
                          placeholder="Saisissez votre commentaire ici..."
                          required maxlength="1000"><?php echo htmlspecialchars($commentaire['commentaire'] ?? $commentaire['Texte'] ?? ''); ?></textarea>
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
                    <i class="fas fa-save"></i> Enregistrer les modifications
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