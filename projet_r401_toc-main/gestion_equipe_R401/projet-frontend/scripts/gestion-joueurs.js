/**
 * =====================================================
 * FICHIER: scripts/gestion-joueurs.js
 * ROLE: Gestion des joueurs (liste)
 * FONCTIONS:
 *   - Suppression avec verification des participations
 *   - Notifications
 *   - Animations des lignes du tableau
 *   - Tooltips
 * =====================================================
 */

/**
 * Verifie si le joueur a participe a des matchs avant de supprimer
 *
 * @param {HTMLFormElement} form - Le formulaire de suppression
 * @param {string} nomJoueur - Le nom complet du joueur
 * @param {number} idJoueur - L'ID du joueur
 * @returns {boolean} - Retourne false pour empecher la soumission immediate
 */
function confirmerSuppression(form, nomJoueur, idJoueur) {
    // Affichage d'un indicateur de chargement
    const bouton = form.querySelector('button[type="submit"]');
    const texteOriginal = bouton.innerHTML;
    bouton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    bouton.disabled = true;

    // Appel AJAX pour verifier si le joueur a des participations
    fetch('index.php?controller=joueur&action=verifierParticipations&id=' + idJoueur)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur reseau');
            }
            return response.json();
        })
        .then(data => {
            bouton.innerHTML = texteOriginal;
            bouton.disabled = false;

            if (data.nbParticipations > 0) {
                // Affichage d'un message d'erreur
                showNotification(
                    `Impossible de supprimer "${nomJoueur}" car il a deja participe a ${data.nbParticipations} match(s).`,
                    'error'
                );
                return false;
            } else {
                // Confirmation de suppression
                if (confirm(`Etes-vous sur de vouloir supprimer "${nomJoueur}" ? Cette action est definitive.`)) {
                    form.submit();
                }
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            bouton.innerHTML = texteOriginal;
            bouton.disabled = false;

            // En cas d'erreur, confirmation standard
            if (confirm(`Etes-vous sur de vouloir supprimer "${nomJoueur}" ?`)) {
                form.submit();
            }
        });

    return false; // Empeche la soumission immediate
}

/**
 * Affiche une notification temporaire
 *
 * @param {string} message - Le message a afficher
 * @param {string} type - Le type de notification ('success', 'error', 'info')
 */
function showNotification(message, type = 'info') {
    // Creation de l'element de notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
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

// =====================================================
// STYLES DES NOTIFICATIONS
// =====================================================
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
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

    @media (max-width: 768px) {
        .notification {
            left: 20px;
            right: 20px;
            min-width: auto;
            max-width: none;
        }
    }
`;
document.head.appendChild(notificationStyles);

// =====================================================
// INITIALISATION AU CHARGEMENT DE LA PAGE
// =====================================================
document.addEventListener('DOMContentLoaded', function() {
    // Animation des lignes du tableau
    const lignes = document.querySelectorAll('.joueur-ligne');
    lignes.forEach((ligne, index) => {
        ligne.style.animation = `fadeInUp 0.3s ease ${index * 0.05}s forwards`;
        ligne.style.opacity = '0';
    });

    // Style d'animation
    const animationStyles = document.createElement('style');
    animationStyles.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(animationStyles);

    // Tooltips sur les boutons d'action
    const boutonsAction = document.querySelectorAll('.bouton-action');
    boutonsAction.forEach(btn => {
        const titre = btn.getAttribute('title');
        if (titre) {
            btn.addEventListener('mouseenter', (e) => {
                const tooltip = document.createElement('div');
                tooltip.className = 'tooltip';
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