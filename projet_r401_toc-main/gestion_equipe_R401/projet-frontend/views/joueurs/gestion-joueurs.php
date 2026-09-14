<?php
/**
 * =====================================================
 * FICHIER: views/joueurs/gestion-joueurs.php
 * ROLE: Page de gestion de la liste des joueurs
 * DESCRIPTION: Affiche la liste des joueurs avec leurs informations
 * =====================================================
 */

// Generation d'un token CSRF s'il n'existe pas
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include CHEMIN_INCLUDES . '/entete.php';
?>

<!-- Conteneur principal -->
<div class="conteneur-gestion">

    <!-- En-tete avec titre et statistiques -->
    <div class="entete-gestion">
        <h1><i class="fas fa-users"></i> Gestion des Joueurs</h1>
        <div class="actions-gestion">
            <a href="index.php?controller=joueur&action=ajouter" class="bouton-ajouter">
                <i class="fas fa-user-plus"></i> Ajouter un joueur
            </a>
            <div class="statistiques">
                <span class="stat">
                    <i class="fas fa-user-check"></i>
                    <?php
                    $actifs = 0;
                    if (!empty($joueurs)) {
                        $actifs = count(array_filter($joueurs, function($j) {
                            return isset($j['statut_joueur']) && $j['statut_joueur'] == 'Actif';
                        }));
                    }
                    echo $actifs;
                    ?> Actifs
                </span>
                <span class="stat">
                    <i class="fas fa-user-injured"></i>
                    <?php
                    $blesses = 0;
                    if (!empty($joueurs)) {
                        $blesses = count(array_filter($joueurs, function($j) {
                            return isset($j['statut_joueur']) && $j['statut_joueur'] == 'Blessé';
                        }));
                    }
                    echo $blesses;
                    ?> Blessés
                </span>
                <span class="stat">
                    <i class="fas fa-user-slash"></i>
                    <?php
                    $suspendus = 0;
                    if (!empty($joueurs)) {
                        $suspendus = count(array_filter($joueurs, function($j) {
                            return isset($j['statut_joueur']) && $j['statut_joueur'] == 'Suspendu';
                        }));
                    }
                    echo $suspendus;
                    ?> Suspendus
                </span>
                <span class="stat">
                    <i class="fas fa-user"></i>
                    <?php echo !empty($joueurs) ? count($joueurs) : 0; ?> Total
                </span>
            </div>
        </div>
    </div>

    <!-- Messages de session -->
    <?php if (isset($messageSucces) && $messageSucces): ?>
        <div class="message-succes"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($messageSucces); ?></div>
    <?php endif; ?>

    <?php if (isset($messageErreur) && $messageErreur): ?>
        <div class="message-erreur"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($messageErreur); ?></div>
    <?php endif; ?>

    <!-- Tableau des joueurs -->
    <?php if (!empty($joueurs)): ?>
        <div class="tableau-donnees">
            <table class="table-joueurs">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Licence</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Date de naissance</th>
                        <th>Taille</th>
                        <th>Poids</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </thead>
                <tbody>
                    <?php foreach ($joueurs as $joueur):
                        if (!isset($joueur['date_naissance'])) continue;

                        // Calcul de l'age
                        try {
                            $dateNaissance = new DateTime($joueur['date_naissance']);
                            $aujourdhui = new DateTime();
                            $age = $dateNaissance->diff($aujourdhui)->y;
                            $dateFormatee = $dateNaissance->format('d/m/Y');
                        } catch (Exception $e) {
                            $age = '?';
                            $dateFormatee = 'Invalide';
                        }

                        $statut = isset($joueur['statut_joueur']) ? $joueur['statut_joueur'] : 'Inconnu';

                        // Classe CSS pour le statut
                        $classeStatut = '';
                        $iconeStatut = '';
                        switch ($statut) {
                            case 'Actif':
                                $classeStatut = 'statut-actif';
                                $iconeStatut = 'fa-user-check';
                                break;
                            case 'Blessé':
                                $classeStatut = 'statut-blesse';
                                $iconeStatut = 'fa-user-injured';
                                break;
                            case 'Suspendu':
                                $classeStatut = 'statut-suspendu';
                                $iconeStatut = 'fa-user-slash';
                                break;
                            case 'Absent':
                                $classeStatut = 'statut-absent';
                                $iconeStatut = 'fa-user-clock';
                                break;
                            default:
                                $classeStatut = 'statut-inconnu';
                                $iconeStatut = 'fa-question-circle';
                        }
                    ?>
                        <tr class="joueur-ligne">
                            <td class="col-id"><?php echo isset($joueur['id_joueur']) ? htmlspecialchars($joueur['id_joueur']) : ''; ?></td>
                            <td class="col-licence"><?php echo isset($joueur['numero_licence']) ? htmlspecialchars($joueur['numero_licence']) : ''; ?></td>
                            <td class="col-nom"><?php echo isset($joueur['nom_joueur']) ? htmlspecialchars($joueur['nom_joueur']) : ''; ?></td>
                            <td class="col-prenom"><?php echo isset($joueur['prenom_joueur']) ? htmlspecialchars($joueur['prenom_joueur']) : ''; ?></td>
                            <td class="col-date-naissance">
                                <?php echo $dateFormatee; ?>
                                <span class="age-indicator">(<?php echo $age; ?> ans)</span>
                            </td>
                            <td class="col-taille"><?php echo isset($joueur['taille_cm']) ? htmlspecialchars($joueur['taille_cm']) : ''; ?> cm</td>
                            <td class="col-poids"><?php echo isset($joueur['poids_kg']) ? htmlspecialchars($joueur['poids_kg']) : ''; ?> kg</td>
                            <td class="col-statut">
                                <span class="badge-statut <?php echo $classeStatut; ?>">
                                    <i class="fas <?php echo $iconeStatut; ?>"></i>
                                    <?php echo htmlspecialchars($statut); ?>
                                </span>
                            </td>
                            <td class="col-actions">
                                <div class="actions-tableau">
                                    <a href="index.php?controller=joueur&action=modifier&id=<?php echo $joueur['id_joueur']; ?>"
                                       class="bouton-action bouton-modifier"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?controller=joueur&action=details&id=<?php echo $joueur['id_joueur']; ?>"
                                       class="bouton-action bouton-voir"
                                       title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="index.php?controller=commentaire&action=index&id=<?php echo $joueur['id_joueur']; ?>"
                                       class="bouton-action bouton-commentaire"
                                       title="Voir les commentaires">
                                        <i class="fas fa-comment-dots"></i>
                                    </a>

                                    <!-- Formulaire de suppression avec verification AJAX -->
                                    <form method="POST" action="index.php?controller=joueur&action=supprimer" style="display: inline;"
                                        onsubmit="return confirmerSuppression(this, '<?php echo addslashes($joueur['prenom_joueur'] . ' ' . $joueur['nom_joueur']); ?>', <?php echo $joueur['id_joueur']; ?>)">
                                        <input type="hidden" name="id_joueur" value="<?php echo $joueur['id_joueur']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <button type="submit" class="bouton-action bouton-supprimer" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <!-- Message quand aucun joueur n'existe -->
        <div class="aucune-donnee">
            <div class="illustration-vide">
                <i class="fas fa-users-slash"></i>
            </div>
            <h3>Aucun joueur enregistré</h3>
            <p>Commencez par ajouter des joueurs à votre équipe.</p>
            <a href="index.php?controller=joueur&action=ajouter" class="bouton-principal">
                <i class="fas fa-user-plus"></i> Ajouter votre premier joueur
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Inclusion du JavaScript pour la gestion des joueurs -->
<script src="<?php echo $prefixe; ?>scripts/gestion-joueurs.js"></script>

<?php include CHEMIN_INCLUDES . '/pied-de-page.php'; ?>