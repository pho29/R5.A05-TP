<?php
/**
 * =====================================================
 * FICHIER: views/matchs/evaluations-joueurs.php
 * ROLE: Page d'evaluation des joueurs apres un match
 * DESCRIPTION: Permet de noter les joueurs sur 5 etoiles
 * =====================================================
 */
// Activer les erreurs pour le débogage (à désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include CHEMIN_INCLUDES . '/entete.php';
?>

<div class="conteneur-evaluations">
    <!-- Sélection du match -->
    <div class="carte-selection-match">
        <h3><i class="fas fa-futbol"></i> Sélectionner un match</h3>
        <form method="GET" action="index.php" class="form-selection">
            <input type="hidden" name="controller" value="match">
            <input type="hidden" name="action" value="evaluer">
            <select name="match" id="select-match" class="select-match" onchange="this.form.submit()">
                <option value="">-- Choisir un match --</option>
                <?php foreach ($matchsPasses as $match): 
                    // Vérifier que le match a une date valide
                    $dateFormatee = 'Date inconnue';
                    if (!empty($match['date_heure_match']) && $match['date_heure_match'] != '0000-00-00 00:00:00') {
                        try {
                            $dateHeure = new DateTime($match['date_heure_match']);
                            $dateFormatee = $dateHeure->format('d/m/Y à H:i');
                        } catch (Exception $e) {
                            $dateFormatee = 'Date invalide';
                        }
                    }
                    
                    $nomAdversaire = !empty($match['equipe_adverse']) ? $match['equipe_adverse'] : 'Adversaire inconnu';
                    $scoreEquipe = $match['score_equipe'] ?? 0;
                    $scoreAdverse = $match['score_adverse'] ?? 0;
                    $resultat = $match['resultat_match'] ?? 'Nul';
                ?>
                <option value="<?php echo $match['id_match']; ?>"
                        <?php echo ($idMatchSelectionne == $match['id_match']) ? 'selected' : ''; ?>>
                    <?php echo $dateFormatee; ?> - <?php echo htmlspecialchars($nomAdversaire); ?>
                    (<?php echo $scoreEquipe; ?>-<?php echo $scoreAdverse; ?>)
                    - <?php echo $resultat; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if ($messageSucces): ?>
        <div class="message-succes"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($messageSucces); ?></div>
    <?php endif; ?>
    
    <?php if ($messageErreur): ?>
        <div class="message-erreur"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($messageErreur); ?></div>
    <?php endif; ?>

    <?php if ($matchSelectionne && !empty($joueurs)):
        // Vérifier et formater la date du match
        $dateFormatee = 'Date inconnue';
        $heureFormatee = '';
        if (!empty($matchSelectionne['date_heure_match']) && $matchSelectionne['date_heure_match'] != '0000-00-00 00:00:00') {
            try {
                $dateHeure = new DateTime($matchSelectionne['date_heure_match']);
                $dateFormatee = $dateHeure->format('d/m/Y');
                $heureFormatee = $dateHeure->format('H:i');
            } catch (Exception $e) {
                $dateFormatee = 'Date invalide';
            }
        }
        
        $nomAdversaire = !empty($matchSelectionne['equipe_adverse']) ? $matchSelectionne['equipe_adverse'] : 'Adversaire inconnu';
        $lieuMatch = $matchSelectionne['lieu_match'] ?? 'Domicile';
        $resultatMatch = $matchSelectionne['resultat_match'] ?? 'À venir';
        $scoreEquipe = $matchSelectionne['score_equipe'] ?? 0;
        $scoreAdverse = $matchSelectionne['score_adverse'] ?? 0;
    ?>
    <!-- Informations du match sélectionné -->
    <div class="carte-info-match-eval">
        <div class="info-match-content">
            <h2><?php echo htmlspecialchars($nomAdversaire); ?></h2>
            <div class="meta-match-eval">
                <span><i class="fas fa-calendar"></i> <?php echo $dateFormatee; ?> <?php echo $heureFormatee ? "à $heureFormatee" : ''; ?></span>
                <span class="badge-lieu-eval badge-<?php echo strtolower($lieuMatch); ?>">
                    <?php echo $lieuMatch; ?>
                </span>
                <span class="badge-resultat-eval badge-<?php echo strtolower($resultatMatch); ?>">
                    <?php echo $resultatMatch; ?>
                </span>
                <span class="score-eval">
                    Score: <?php echo $scoreEquipe; ?> - <?php echo $scoreAdverse; ?>
                </span>
            </div>
        </div>
    </div>

    <form method="POST" action="index.php?controller=match&action=evaluer" id="form-evaluation">
        <input type="hidden" name="match_id" value="<?php echo $idMatchSelectionne; ?>">
        <div class="grille-evaluations">
            <?php
            // Séparer les titulaires et les remplaçants
            $titulaires = array_filter($joueurs, function($j) { 
                return isset($j['titulaire']) && $j['titulaire'] == 1; 
            });
            $remplacants = array_filter($joueurs, function($j) { 
                return isset($j['titulaire']) && $j['titulaire'] == 0; 
            });

            // Afficher les titulaires
            if (!empty($titulaires)): 
            ?>
            <div class="section-evaluation">
                <h3 class="titre-section-eval">
                    <i class="fas fa-user-check"></i> Titulaires (<?php echo count($titulaires); ?>)
                </h3>

                <?php foreach ($titulaires as $joueur): 
                    $prenom = $joueur['prenom_joueur'] ?? '';
                    $nom = $joueur['nom_joueur'] ?? '';
                    $numeroLicence = $joueur['numero_licence'] ?? '';
                    $libellePoste = $joueur['libelle_poste'] ?? 'Milieu central';
                    $evaluation = isset($joueur['evaluation']) ? floatval($joueur['evaluation']) : 0;
                ?>
                <div class="carte-eval-joueur">
                    <div class="entete-eval-joueur">
                        <div class="joueur-avatar-eval">
                            <?php echo strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1)); ?>
                        </div>
                        <div class="joueur-info-eval">
                            <h4><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></h4>
                            <span class="licence-eval">#<?php echo htmlspecialchars($numeroLicence); ?></span>
                            <span class="role-eval" style="font-size: 12px; color: #3498db; display: block;">
                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($libellePoste); ?>
                            </span>
                        </div>
                    </div>

                    <div class="note-eval">
                        <label><i class="fas fa-star"></i> Note du joueur</label>
                        <div class="etoiles-rating" data-joueur="<?php echo $joueur['id_joueur']; ?>">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="etoile <?php echo ($i <= $evaluation) ? 'active' : ''; ?>"
                                  data-note="<?php echo $i; ?>">★</span>
                            <?php endfor; ?>
                            <input type="hidden"
                                   name="evaluations[<?php echo $joueur['id_joueur']; ?>][note]"
                                   value="<?php echo $evaluation; ?>"
                                   class="input-note">
                            <input type="hidden"
                                   name="evaluations[<?php echo $joueur['id_joueur']; ?>][statut]"
                                   value="1">
                            <span class="note-affichee"><?php echo number_format($evaluation, 1); ?>/5</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($remplacants)): ?>
            <div class="section-evaluation">
                <h3 class="titre-section-eval">
                    <i class="fas fa-exchange-alt"></i> Remplaçants (<?php echo count($remplacants); ?>)
                </h3>

                <?php foreach ($remplacants as $joueur):
                    $prenom = $joueur['prenom_joueur'] ?? '';
                    $nom = $joueur['nom_joueur'] ?? '';
                    $numeroLicence = $joueur['numero_licence'] ?? '';
                    $libellePoste = $joueur['libelle_poste'] ?? 'Milieu central';
                    $evaluation = isset($joueur['evaluation']) ? floatval($joueur['evaluation']) : 0;
                ?>
                <div class="carte-eval-joueur remplacant">
                    <div class="entete-eval-joueur">
                        <div class="joueur-avatar-eval">
                            <?php echo strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1)); ?>
                        </div>
                        <div class="joueur-info-eval">
                            <h4><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></h4>
                            <span class="licence-eval">#<?php echo htmlspecialchars($numeroLicence); ?></span>
                            <span class="role-eval" style="font-size: 12px; color: #f39c12; display: block;">
                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($libellePoste); ?>
                            </span>
                        </div>
                    </div>

                    <div class="note-eval">
                        <label><i class="fas fa-star"></i> Note du joueur</label>
                        <div class="etoiles-rating" data-joueur="<?php echo $joueur['id_joueur']; ?>">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="etoile <?php echo ($i <= $evaluation) ? 'active' : ''; ?>"
                                  data-note="<?php echo $i; ?>">★</span>
                            <?php endfor; ?>
                            <input type="hidden"
                                   name="evaluations[<?php echo $joueur['id_joueur']; ?>][note]"
                                   value="<?php echo $evaluation; ?>"
                                   class="input-note">
                            <input type="hidden"
                                   name="evaluations[<?php echo $joueur['id_joueur']; ?>][statut]"
                                   value="0">
                            <span class="note-affichee"><?php echo number_format($evaluation, 1); ?>/5</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="actions-evaluation">
            <button type="submit" class="bouton-principal btn-large">
                <i class="fas fa-save"></i> Enregistrer les évaluations
            </button>
        </div>
    </form>

    <?php elseif ($matchSelectionne && empty($joueurs)): ?>
    <div class="aucune-donnee">
        <i class="fas fa-users-slash"></i>
        <h3>Aucun joueur à évaluer</h3>
        <p>Aucun joueur n'a été sélectionné pour ce match.</p>
        <a href="index.php?controller=match&action=composer&id=<?php echo $idMatchSelectionne; ?>" class="bouton-principal">
            <i class="fas fa-users-cog"></i> Composer l'équipe
        </a>
    </div>

    <?php elseif (!$idMatchSelectionne): ?>
    <div class="aucune-donnee">
        <i class="fas fa-hand-pointer"></i>
        <h3>Sélectionnez un match</h3>
        <p>Choisissez un match dans la liste ci-dessus pour évaluer les joueurs.</p>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.etoiles-rating').forEach(function(container) {
        const etoiles = container.querySelectorAll('.etoile');
        const input = container.querySelector('.input-note');
        const affichage = container.querySelector('.note-affichee');

        etoiles.forEach(function(etoile) {
            etoile.addEventListener('click', function() {
                const note = parseInt(this.dataset.note);
                input.value = note;
                affichage.textContent = note + '/5';

                etoiles.forEach(function(e, index) {
                    if (index < note) {
                        e.classList.add('active');
                    } else {
                        e.classList.remove('active');
                    }
                });
            });

            etoile.addEventListener('mouseenter', function() {
                const note = parseInt(this.dataset.note);
                etoiles.forEach(function(e, index) {
                    if (index < note) {
                        e.classList.add('hover');
                    } else {
                        e.classList.remove('hover');
                    }
                });
            });
        });

        container.addEventListener('mouseleave', function() {
            etoiles.forEach(e => e.classList.remove('hover'));
        });
    });
});
</script>

<?php include CHEMIN_INCLUDES . '/pied-de-page.php'; ?>