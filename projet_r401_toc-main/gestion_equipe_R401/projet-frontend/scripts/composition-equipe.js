/**
 * =====================================================
 * FICHIER: scripts/composition-equipe.js
 * ROLE: Gestion de la composition d'equipe
 * FONCTIONS:
 *   - Compteurs de joueurs (titulaires/remplacants)
 *   - Affichage des details des joueurs
 *   - Verification des doublons
 *   - Validation du formulaire
 * =====================================================
 */

// Variables globales pour les compteurs
let nbTitulaires = 0;
let nbRemplacants = 0;

/**
 * Met a jour les compteurs de joueurs
 * Calcule le nombre de titulaires et de remplacants selectionnes
 * Desactive le bouton de validation si les conditions ne sont pas remplies
 */
function mettreAJourCompteurs() {
    // Comptage des titulaires
    nbTitulaires = 0;
    document.querySelectorAll('.selection-titulaire').forEach(s => {
        if (s.value) {
            nbTitulaires++;
            afficherDetails(s, 'details_' + s.dataset.poste);
        } else {
            viderDetails('details_' + s.dataset.poste);
        }
    });

    // Comptage des remplacants
    nbRemplacants = 0;
    document.querySelectorAll('.selection-remplacant').forEach(s => {
        if (s.value) nbRemplacants++;
    });

    // Mise a jour de l'affichage
    document.getElementById('nb-titulaires').textContent = nbTitulaires;
    document.getElementById('nb-remplacants').textContent = nbRemplacants;
    document.getElementById('nb-total').textContent = nbTitulaires + nbRemplacants;

    // Mise a jour des badges
    const bt = document.getElementById('badge-titulaires');
    const br = document.getElementById('badge-remplacants');
    bt.textContent = `${nbTitulaires}/11`;
    br.textContent = `${nbRemplacants}/7`;
    bt.className = 'badge-compteur ' + (nbTitulaires === 11 ? 'succes' : 'danger');
    br.className = 'badge-compteur ' + (nbRemplacants > 7 ? 'danger' : '');

    // Activation/desactivation du bouton de validation
    document.getElementById('compteur-titulaires').classList.toggle('complet', nbTitulaires === 11);
    document.getElementById('compteur-remplacants').classList.toggle('depasse', nbRemplacants > 7);
    document.getElementById('btn-submit').disabled = (nbTitulaires !== 11 || nbRemplacants > 7);
}

/**
 * Affiche les details d'un joueur selectionne
 *
 * @param {HTMLSelectElement} select - L'element select du joueur
 * @param {string} divId - L'ID du div ou afficher les details
 */
function afficherDetails(select, divId) {
    const div = document.getElementById(divId);
    if (!div || select.selectedIndex <= 0) {
        if (div) div.innerHTML = '';
        return;
    }
    const o = select.options[select.selectedIndex];
    div.innerHTML = `<div class="joueur-info-mini">
        <span class="joueur-nom">${o.dataset.prenom} ${o.dataset.nom}</span>
        <span class="joueur-caracteristiques">${o.dataset.taille}cm / ${o.dataset.poids}kg</span>
        <span class="joueur-licence">#${o.dataset.numero}</span>
    </div>`;
}

/**
 * Vide les details d'un joueur
 *
 * @param {string} divId - L'ID du div a vider
 */
function viderDetails(divId) {
    const div = document.getElementById(divId);
    if (div) div.innerHTML = '';
}

/**
 * Affiche les details d'un remplacant specifique
 *
 * @param {number} index - L'index du remplacant
 */
function afficherDetailsRemplacant(index) {
    const s = document.querySelector(`select[name="remplacant_joueur_${index}"]`);
    if (s) afficherDetails(s, `details_remplacant_${index}`);
}

/**
 * Verifie les doublons de selection
 * Un meme joueur ne peut pas etre selectionne plusieurs fois
 */
function verifierDoublons() {
    const tous = [];
    document.querySelectorAll('.selection-titulaire, .selection-remplacant').forEach(s => {
        if (s.value) {
            const id = s.options[s.selectedIndex]?.dataset.id;
            if (id) tous.push({ id, el: s });
        }
    });

    const vus = new Map();
    let erreur = false;
    tous.forEach(j => {
        if (vus.has(j.id)) {
            j.el.style.border = '2px solid #dc3545';
            j.el.classList.add('doublon');
            erreur = true;
        } else {
            vus.set(j.id, j);
            j.el.style.border = '';
            j.el.classList.remove('doublon');
        }
    });

    // Affichage du message d'erreur
    let div = document.getElementById('error-doublon');
    if (erreur) {
        if (!div) {
            div = document.createElement('div');
            div.id = 'error-doublon';
            div.className = 'message-erreur';
            document.getElementById('formulaireComposition').prepend(div);
        }
        div.innerHTML = '<i class="fas fa-exclamation-circle"></i> Un joueur ne peut pas etre selectionne deux fois !';
        div.style.display = 'block';
    } else if (div) {
        div.style.display = 'none';
    }
}

/**
 * Valide le formulaire avant soumission
 *
 * @returns {boolean} - True si valide, False sinon
 */
function validerFormulaire() {
    // Verification du nombre de titulaires (doit etre 11)
    let count = 0;
    document.querySelectorAll('.selection-titulaire').forEach(s => {
        if (s.value) count++;
    });
    if (count !== 11) {
        alert(`Il faut 11 titulaires. Actuellement : ${count}/11`);
        return false;
    }

    // Verification des doublons
    if (document.querySelectorAll('.doublon').length > 0) {
        alert('Un joueur est selectionne plusieurs fois !');
        return false;
    }

    // Verification du nombre de remplacants (max 7)
    if (nbRemplacants > 7) {
        alert(`Maximum 7 remplacants. Actuellement : ${nbRemplacants}/7`);
        return false;
    }

    // Verification que chaque remplacant a un poste
    for (let i = 0; i < 7; i++) {
        const j = document.querySelector(`select[name="remplacant_joueur_${i}"]`);
        const p = document.querySelector(`select[name="remplacant_poste_${i}"]`);
        if (j?.value && !p?.value) {
            alert(`Remplacant ${i + 1} : poste non choisi !`);
            return false;
        }
    }
    return true;
}

// =====================================================
// INITIALISATION AU CHARGEMENT DE LA PAGE
// =====================================================
document.addEventListener('DOMContentLoaded', () => {
    mettreAJourCompteurs();
    verifierDoublons();
    for (let i = 0; i < 7; i++) afficherDetailsRemplacant(i);
});