/**
 * =====================================================
 * FICHIER: scripts/commentaires.js
 * ROLE: Gestion des commentaires
 * FONCTIONS:
 *   - Compteur de caracteres
 *   - Notifications
 *   - Confirmation de suppression
 *   - Animations et tooltips
 * =====================================================
 */

/**
 * Initialise le compteur de caracteres pour un textarea
 * Affiche la longueur en temps reel et change de couleur selon la limite
 *
 * @param {HTMLTextAreaElement} textarea - L'element textarea a surveiller
 * @param {HTMLElement} compteur - L'element qui affiche le compteur
 */
function initCompteurCaracteres(textarea, compteur) {
    if (!textarea || !compteur) return;

    /**
     * Met a jour l'affichage du compteur
     * Change la couleur selon la proximite de la limite (1000 caracteres)
     */
    function updateCompteur() {
        const longueur = textarea.value.length;
        compteur.textContent = longueur;

        // Changement de couleur selon la longueur
        if (longueur > 950) {
            compteur.style.color = '#dc3545';  // Rouge - tres proche de la limite
            compteur.style.fontWeight = 'bold';
        } else if (longueur > 900) {
            compteur.style.color = '#ffc107';  // Jaune - approche de la limite
            compteur.style.fontWeight = 'bold';
        } else {
            compteur.style.color = '#3498db';  // Bleu - normal
            compteur.style.fontWeight = 'normal';
        }

        // Animation de pulsation si tres proche de la limite
        if (longueur > 950) {
            compteur.style.animation = 'pulse 1s infinite';
        } else {
            compteur.style.animation = 'none';
        }
    }

    // Ecouteurs d'evenements
    textarea.addEventListener('input', updateCompteur);
    updateCompteur(); // Initialisation
}

/**
 * Affiche une notification temporaire
 *
 * @param {string} message - Le message a afficher
 * @param {string} type - Le type de notification ('success', 'error', 'info')
 */
function showCommentNotification(message, type = 'info') {
    // Creation de l'element de notification
    const notification = document.createElement('div');
    notification.className = `comment-notification comment-notification-${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
        <span>${message}</span>
        <button class="notification-close">&times;</button>
    `;

    // Ajout au DOM
    document.body.appendChild(notification);

    // Animation d'entree
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    // Auto-fermeture apres 5 secondes
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);

    // Fermeture manuelle
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    });
}

/**
 * Confirme la suppression d'un commentaire
 *
 * @param {Event} event - L'evenement de soumission
 * @returns {boolean} - false pour annuler la soumission si annule
 */
function confirmerSuppressionCommentaire(event) {
    if (!confirm('Etes-vous sur de vouloir supprimer ce commentaire ? Cette action est definitive.')) {
        event.preventDefault();
        return false;
    }
    return true;
}

// =====================================================
// STYLES DES NOTIFICATIONS
// =====================================================
const commentStyles = document.createElement('style');
commentStyles.textContent = `
    /* Notifications */
    .comment-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 320px;
        max-width: 450px;
        background: white;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        transform: translateX(450px);
        transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        z-index: 10000;
        border-left: 4px solid;
    }

    .comment-notification.show {
        transform: translateX(0);
    }

    .comment-notification-success {
        border-left-color: #28a745;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
    }

    .comment-notification-error {
        border-left-color: #dc3545;
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
    }

    .comment-notification-info {
        border-left-color: #17a2b8;
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        color: #0c5460;
    }

    .comment-notification i {
        font-size: 22px;
    }

    .comment-notification span {
        flex: 1;
        font-size: 14px;
        line-height: 1.4;
    }

    .comment-notification .notification-close {
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: inherit;
        opacity: 0.6;
        transition: opacity 0.3s;
        padding: 0;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .comment-notification .notification-close:hover {
        opacity: 1;
        background: rgba(0, 0, 0, 0.05);
    }

    /* Animation de pulsation pour le compteur */
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    /* Animation d'entree pour les cartes */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .carte-commentaire {
        animation: fadeInUp 0.4s ease forwards;
    }

    /* Tooltip personnalise */
    .tooltip-comment {
        position: absolute;
        background: #2c3e50;
        color: white;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        pointer-events: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .tooltip-comment::before {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: #2c3e50 transparent transparent transparent;
    }

    @media (max-width: 768px) {
        .comment-notification {
            left: 20px;
            right: 20px;
            min-width: auto;
            max-width: none;
        }
    }
`;
document.head.appendChild(commentStyles);

// =====================================================
// INITIALISATION AU CHARGEMENT DE LA PAGE
// =====================================================
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation du compteur de caracteres
    const textarea = document.getElementById('commentaire');
    const compteur = document.getElementById('compteur');

    if (textarea && compteur) {
        initCompteurCaracteres(textarea, compteur);
    }

    // Animation des cartes de commentaires
    const commentaires = document.querySelectorAll('.carte-commentaire');
    commentaires.forEach((carte, index) => {
        carte.style.animationDelay = `${index * 0.05}s`;
    });

    // Tooltips sur les boutons d'action
    const boutonsAction = document.querySelectorAll('.bouton-action');
    boutonsAction.forEach(btn => {
        const titre = btn.getAttribute('title');
        if (titre) {
            let tooltipTimeout;

            btn.addEventListener('mouseenter', (e) => {
                tooltipTimeout = setTimeout(() => {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'tooltip-comment';
                    tooltip.textContent = titre;

                    const rect = btn.getBoundingClientRect();
                    tooltip.style.top = `${rect.top - 30}px`;
                    tooltip.style.left = `${rect.left + (rect.width / 2) - 20}px`;

                    document.body.appendChild(tooltip);

                    btn.addEventListener('mouseleave', () => {
                        tooltip.remove();
                    }, { once: true });
                }, 300);
            });

            btn.addEventListener('mouseleave', () => {
                clearTimeout(tooltipTimeout);
            });
        }
    });

    // Confirmation de suppression
    const formulairesSuppression = document.querySelectorAll('form[action*="action=supprimer"]');
    formulairesSuppression.forEach(form => {
        form.addEventListener('submit', confirmerSuppressionCommentaire);
    });

    // Message de succes automatique (via parametre URL)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('succes') === '1') {
        showCommentNotification('Operation reussie !', 'success');
        // Suppression du parametre de l'URL sans recharger
        const newUrl = window.location.pathname + window.location.search.replace(/[?&]succes=1/, '').replace(/^&/, '?');
        window.history.replaceState({}, document.title, newUrl);
    }
});