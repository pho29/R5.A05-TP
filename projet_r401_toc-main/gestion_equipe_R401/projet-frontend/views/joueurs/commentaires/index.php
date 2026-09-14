<?php
/**
 * =====================================================
 * FICHIER: views/joueurs/commentaires/index.php
 * ROLE: Liste des commentaires d'un joueur
 * DESCRIPTION: Affiche l'historique des commentaires d'un joueur
 * =====================================================
 */

include CHEMIN_INCLUDES . '/entete.php';
?>

<!-- Conteneur principal -->
<div class="conteneur-commentaires">

    <!-- En-tete de la page -->
    <div class="entete-page">
        <h1><i class="fas fa-comment-dots"></i> Commentaires du Joueur</h1>
        <div class="actions">
            <a href="index.php?controller=commentaire&action=ajouter&id=<?php echo $idJoueur; ?>" class="bouton-principal">
                <i class="fas fa-plus"></i> Ajouter un commentaire
            </a>
            <a href="index.php?controller=joueur&action=index" class="bouton-secondaire">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <!-- Carte d'information du joueur -->
    <div class="info-joueur">
        <div class="joueur-avatar">
            <?php echo strtoupper(substr($joueur['prenom_joueur'], 0, 1) . substr($joueur['nom_joueur'], 0, 1)); ?>
        </div>
        <div class="joueur-info">
            <h2><?php echo htmlspecialchars($joueur['prenom_joueur'] . ' ' . $joueur['nom_joueur']); ?></h2>
            <div class="joueur-details">
                <span><i class="fas fa-id-card"></i> <?php echo htmlspecialchars($joueur['numero_licence']); ?></span>
                <span><i class="fas fa-ruler-vertical"></i> <?php echo $joueur['taille_cm']; ?> cm</span>
                <span><i class="fas fa-weight"></i> <?php echo $joueur['poids_kg']; ?> kg</span>
                <span><i class="fas fa-tag"></i>
                    <span class="badge-statut statut-<?php echo strtolower($joueur['statut_joueur']); ?>">
                        <?php echo $joueur['statut_joueur']; ?>
                    </span>
                </span>
            </div>
        </div>
    </div>

    <!-- Message de succes apres operation -->
    <?php if (isset($_GET['succes']) && $_GET['succes'] == 1): ?>
        <div class="message-succes">
            <i class="fas fa-check-circle"></i> Opération réussie !
        </div>
    <?php endif; ?>

    <!-- Cas: Aucun commentaire -->
    <?php if (empty($commentaires)): ?>
        <div class="aucune-donnee">
            <div class="illustration-vide">
                <i class="fas fa-comment-slash"></i>
            </div>
            <h3>Aucun commentaire</h3>
            <p>Commencez par ajouter un commentaire pour ce joueur.</p>
            <a href="index.php?controller=commentaire&action=ajouter&id=<?php echo $idJoueur; ?>" class="bouton-principal">
                <i class="fas fa-plus"></i> Ajouter un commentaire
            </a>
        </div>

    <!-- Cas: Liste des commentaires -->
    <?php else: ?>
        <div class="liste-commentaires">
            <div class="entete-liste">
                <i class="fas fa-history"></i> Historique des commentaires
            </div>

            <!-- Boucle sur les commentaires -->
            <?php foreach ($commentaires as $commentaire): ?>
            <div class="carte-commentaire">
                <div class="entete-commentaire">
                    <div class="date-commentaire">
                        <i class="fas fa-calendar-alt"></i>
                        <?php
                        $date = isset($commentaire['date_creation']) ? $commentaire['date_creation'] : $commentaire['date_ajout'] ?? 'now';
                        echo date('d/m/Y à H:i', strtotime($date));
                        ?>
                    </div>
                    <div class="actions-commentaire">
                        <!-- Lien Modifier -->
                        <a href="index.php?controller=commentaire&action=modifier&id=<?php echo $commentaire['id_commentaire']; ?>"
                           class="bouton-action bouton-modifier" title="Modifier">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <!-- Formulaire de suppression -->
                        <form method="POST" action="index.php?controller=commentaire&action=supprimer" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $commentaire['id_commentaire']; ?>">
                            <input type="hidden" name="id_joueur" value="<?php echo $idJoueur; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit" class="bouton-action bouton-supprimer" title="Supprimer"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
                <div class="contenu-commentaire">
                    <?php
                    $texte = $commentaire['commentaire'] ?? $commentaire['Texte'] ?? '';
                    echo nl2br(htmlspecialchars($texte));
                    ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include CHEMIN_INCLUDES . '/pied-de-page.php'; ?>