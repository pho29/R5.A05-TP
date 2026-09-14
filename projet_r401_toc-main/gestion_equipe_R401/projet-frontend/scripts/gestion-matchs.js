/**
 * =====================================================
 * FICHIER: scripts/gestion-matchs.js
 * ROLE: Gestion des matchs (liste)
 * FONCTIONS:
 *   - Filtrage des matchs par categorie
 *   - Rafraichissement des statistiques
 *   - Notifications
 *   - Animations
 * =====================================================
 */

/**
 * Affiche une notification temporaire
 *
 * @param {string} message - Le message a afficher
 * @param {string} type - Le type de notification ('success', 'error', 'info')
 */
function showMatchNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
        <span>${message}</span>
        <button class="notification-close">&times;</button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);

    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    });
}

/**
 * Filtre les matchs par categorie
 *
 * @param {string} categorie - La categorie a filtrer ('all', 'upcoming', 'past', 'without-result')
 */
function filtrerMatchs(categorie) {
    const matchs = document.querySelectorAll('.carte-match');
    const boutons = document.querySelectorAll('.filtre-btn');

    // Mise a jour de la classe active
    boutons.forEach(btn => {
        if (btn.dataset.categorie === categorie) {
            btn.classList.add('actif');
        } else {
            btn.classList.remove('actif');
        }
    });

    // Filtrage des matchs
    matchs.forEach(match => {
        switch(categorie) {
            case 'all':
                match.style.display = 'block';
                break;
            case 'upcoming':
                if (match.classList.contains('carte-a-venir')) {
                    match.style.display = 'block';
                } else {
                    match.style.display = 'none';
                }
                break;
            case 'past':
                if (match.classList.contains('carte-avec-resultat')) {
                    match.style.display = 'block';
                } else {
                    match.style.display = 'none';
                }
                break;
            case 'without-result':
                if (match.classList.contains('carte-sans-resultat')) {
                    match.style.display = 'block';
                } else {
                    match.style.display = 'none';
                }
                break;
            default:
                match.style.display = 'block';
        }
    });

    // Animation de fondu
    matchs.forEach(match => {
        if (match.style.display !== 'none') {
            match.style.animation = 'fadeIn 0.3s ease';
        }
    });
}

/**
 * Rafraichit les statistiques affichees
 */
function rafraichirStatistiques() {
    const matchs = document.querySelectorAll('.carte-match');
    let aVenir = 0;
    let sansResultat = 0;
    let avecResultat = 0;
    let victoires = 0;
    let defaites = 0;
    let nuls = 0;

    matchs.forEach(match => {
        if (match.classList.contains('carte-a-venir')) aVenir++;
        if (match.classList.contains('carte-sans-resultat')) sansResultat++;
        if (match.classList.contains('carte-avec-resultat')) avecResultat++;

        const badgeResultat = match.querySelector('.badge-resultat');
        if (badgeResultat) {
            if (badgeResultat.classList.contains('badge-victoire')) victoires++;
            if (badgeResultat.classList.contains('badge-defaite')) defaites++;
            if (badgeResultat.classList.contains('badge-nul')) nuls++;
        }
    });

    // Mise a jour des affichages
    const statElements = {
        aVenir: document.querySelector('.stat-card.a-venir .stat-valeur'),
        sansResultat: document.querySelector('.stat-card.sans-resultat .stat-valeur'),
        victoires: document.querySelector('.stat-card.victoire .stat-valeur'),
        defaites: document.querySelector('.stat-card.defaite .stat-valeur'),
        nuls: document.querySelector('.stat-card.nul .stat-valeur')
    };

    if (statElements.aVenir) statElements.aVenir.textContent = aVenir;
    if (statElements.sansResultat) statElements.sansResultat.textContent = sansResultat;
    if (statElements.victoires) statElements.victoires.textContent = victoires;
    if (statElements.defaites) statElements.defaites.textContent = defaites;
    if (statElements.nuls) statElements.nuls.textContent = nuls;
}

// =====================================================
// STYLES DES NOTIFICATIONS ET ANIMATIONS
// =====================================================
const matchStyles = document.createElement('style');
matchStyles.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        max-width: 450px;
        background: white;
        border-radius: 12px;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        transform: translateX(400px);
        transition: transform 0.3s ease;
        z-index: 10000;
        border-left: 4px solid;
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification-success {
        border-left-color: #28a745;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
    }

    .notification-error {
        border-left-color: #dc3545;
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
    }

    .notification-info {
        border-left-color: #17a2b8;
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        color: #0c5460;
    }

    .notification i {
        font-size: 20px;
    }

    .notification span {
        flex: 1;
        font-size: 14px;
    }

    .notification-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: inherit;
        opacity: 0.7;
        transition: opacity 0.3s;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notification-close:hover {
        opacity: 1;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .notification {
            left: 20px;
            right: 20px;
            min-width: auto;
            max-width: none;
        }
    }
`;
document.head.appendChild(matchStyles);

// =====================================================
// INITIALISATION AU CHARGEMENT DE LA PAGE
// =====================================================
document.addEventListener('DOMContentLoaded', function() {
    // Animation des cartes de match
    const cartes = document.querySelectorAll('.carte-match');
    cartes.forEach((carte, index) => {
        carte.style.animation = `fadeIn 0.3s ease ${index * 0.05}s forwards`;
        carte.style.opacity = '0';
    });

    // Gestionnaires pour les boutons de filtre
    const boutonsFiltre = document.querySelectorAll('.filtre-btn');
    boutonsFiltre.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const categorie = btn.dataset.categorie;
            filtrerMatchs(categorie);
        });
    });

    // Gestionnaires pour les boutons de suppression avec confirmation
    const formulairesSuppression = document.querySelectorAll('form[action*="action=supprimer"]');
    formulairesSuppression.forEach(form => {
        form.addEventListener('submit', (e) => {
            const nomAdversaire = form.closest('.carte-match')?.querySelector('.nom-adversaire')?.textContent || 'ce match';
            if (!confirm(`Etes-vous sur de vouloir supprimer le match contre ${nomAdversaire} ?`)) {
                e.preventDefault();
            }
        });
    });

    // Tooltips sur les boutons d'action
    const boutonsAction = document.querySelectorAll('.btn-action');
    boutonsAction.forEach(btn => {
        const titre = btn.getAttribute('title');
        if (titre) {
            btn.addEventListener('mouseenter', (e) => {
                const tooltip = document.createElement('div');
                tooltip.className = 'tooltip-match';
                tooltip.textContent = titre;
                tooltip.style.position = 'absolute';
                tooltip.style.background = '#2c3e50';
                tooltip.style.color = 'white';
                tooltip.style.padding = '4px 8px';
                tooltip.style.borderRadius = '4px';
                tooltip.style.fontSize = '12px';
                tooltip.style.whiteSpace = 'nowrap';
                tooltip.style.zIndex = '1000';
                tooltip.style.pointerEvents = 'none';

                const rect = btn.getBoundingClientRect();
                tooltip.style.top = `${rect.top - 25}px`;
                tooltip.style.left = `${rect.left + (rect.width / 2) - 20}px`;

                document.body.appendChild(tooltip);

                btn.addEventListener('mouseleave', () => {
                    tooltip.remove();
                }, { once: true });
            });
        }
    });
});