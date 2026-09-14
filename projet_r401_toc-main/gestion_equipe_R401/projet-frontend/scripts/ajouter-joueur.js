/**
 * =====================================================
 * FICHIER: scripts/ajouter-joueur.js
 * ROLE: Validation du formulaire d'ajout de joueur
 * CONDITIONS:
 *   - Age entre 16 et 50 ans
 *   - Taille entre 100 et 250 cm
 *   - Poids entre 30 et 150 kg
 *   - Numero de licence: format LICXXX (LIC + 3 chiffres)
 *   - Numero de licence unique (verification via API)
 * =====================================================
 */

// Attente du chargement complet du DOM
document.addEventListener('DOMContentLoaded', function() {
    // References des elements du formulaire
    const form = document.getElementById('formAjoutJoueur');
    const licenceInput = document.getElementById('numero_licence');
    const licenceError = document.getElementById('licence-error');

    // Verification de l'existence du formulaire
    if (!form) {
        console.error('Formulaire non trouvé');
        return;
    }

    // Variables d'etat
    let licenceValid = false;
    let licenceChecking = false;

    /**
     * Calcule l'age a partir d'une date de naissance
     * @param {string} dateNaissance - Date au format YYYY-MM-DD
     * @returns {number} Age en annees
     */
    function calculerAge(dateNaissance) {
        const today = new Date();
        const birthDate = new Date(dateNaissance);
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    }

    /**
     * Valide le format du numero de licence (LICXXX)
     * @param {string} licence - Numero de licence
     * @returns {boolean} True si le format est valide
     */
    function validerFormatLicence(licence) {
        const regex = /^LIC[0-9]{3}$/i;
        return regex.test(licence);
    }

    /**
     * Verifie si le numero de licence existe deja dans la base
     * @param {string} licence - Numero de licence
     * @returns {Promise<boolean>} True si unique, false si existe deja
     */
    async function verifierLicenceUnique(licence) {
        if (!licence || !validerFormatLicence(licence)) {
            return false;
        }

        try {
            // Appel a l'API pour verifier l'existence de la licence
            const response = await fetch('http://localhost/projet_r401_toc/gestion_equipe_R401/projet-backend/JoueurAPI.php?licence=' + encodeURIComponent(licence));
            const data = await response.json();

            if (data.status_code === 200 && data.data && data.data.length > 0) {
                return false; // Licence existe deja
            }
            return true; // Licence disponible
        } catch (error) {
            console.error('Erreur lors de la verification du numero de licence:', error);
            return false;
        }
    }

    /**
     * Valide la taille
     * @param {number} taille - Taille en cm
     * @returns {boolean} True si valide
     */
    function validerTaille(taille) {
        return taille >= 100 && taille <= 250;
    }

    /**
     * Valide le poids
     * @param {number} poids - Poids en kg
     * @returns {boolean} True si valide
     */
    function validerPoids(poids) {
        return poids >= 30 && poids <= 150;
    }

    /**
     * Valide l'age
     * @param {string} dateNaissance - Date de naissance
     * @returns {boolean} True si valide (entre 16 et 50 ans)
     */
    function validerAge(dateNaissance) {
        const age = calculerAge(dateNaissance);
        return age >= 16 && age <= 50;
    }

    /**
     * Effectue toutes les validations et retourne les erreurs
     * @returns {Promise<string[]>} Liste des erreurs
     */
    async function validerFormulaire() {
        const erreurs = [];

        // Recuperation des valeurs
        const nom = document.getElementById('nom_joueur')?.value.trim();
        const prenom = document.getElementById('prenom_joueur')?.value.trim();
        const dateNaissance = document.getElementById('date_naissance')?.value;
        const licence = document.getElementById('numero_licence')?.value.trim().toUpperCase();
        const taille = parseFloat(document.getElementById('taille_cm')?.value);
        const poids = parseFloat(document.getElementById('poids_kg')?.value);

        // Validation du nom
        if (!nom) {
            erreurs.push('Le nom est obligatoire');
        } else if (nom.length < 2) {
            erreurs.push('Le nom doit contenir au moins 2 caracteres');
        }

        // Validation du prenom
        if (!prenom) {
            erreurs.push('Le prenom est obligatoire');
        } else if (prenom.length < 2) {
            erreurs.push('Le prenom doit contenir au moins 2 caracteres');
        }

        // Validation de la date de naissance
        if (!dateNaissance) {
            erreurs.push('La date de naissance est obligatoire');
        } else if (!validerAge(dateNaissance)) {
            const age = calculerAge(dateNaissance);
            erreurs.push(`Age invalide (${age} ans). Le joueur doit avoir entre 16 et 50 ans`);
        }

        // Validation du numero de licence
        if (!licence) {
            erreurs.push('Le numero de licence est obligatoire');
        } else if (!validerFormatLicence(licence)) {
            erreurs.push('Le numero de licence doit etre au format LIC001, LIC002, etc. (LIC + 3 chiffres)');
        } else {
            const isUnique = await verifierLicenceUnique(licence);
            if (!isUnique) {
                erreurs.push(`Le numero de licence ${licence} existe deja. Veuillez utiliser un autre numero.`);
            }
        }

        // Validation de la taille
        if (isNaN(taille)) {
            erreurs.push('La taille est obligatoire');
        } else if (!validerTaille(taille)) {
            erreurs.push(`La taille ${taille} cm est invalide. Elle doit etre comprise entre 100 et 250 cm`);
        }

        // Validation du poids
        if (isNaN(poids)) {
            erreurs.push('Le poids est obligatoire');
        } else if (!validerPoids(poids)) {
            erreurs.push(`Le poids ${poids} kg est invalide. Il doit etre compris entre 30 et 150 kg`);
        }

        return erreurs;
    }

    /**
     * Affiche les erreurs dans une alerte
     * @param {string[]} erreurs - Liste des erreurs
     */
    function afficherErreurs(erreurs) {
        if (erreurs.length > 0) {
            alert('Erreurs de validation :\n- ' + erreurs.join('\n- '));
        }
    }

    /**
     * Met a jour l'affichage de la validation du numero de licence
     */
    async function updateLicenceValidation() {
        const licence = licenceInput.value.trim().toUpperCase();

        if (!licence) {
            if (licenceError) licenceError.style.display = 'none';
            licenceValid = false;
            return;
        }

        if (!validerFormatLicence(licence)) {
            if (licenceError) {
                licenceError.textContent = 'Format invalide. Utilisez LIC001, LIC002, etc. (LIC + 3 chiffres)';
                licenceError.style.display = 'block';
                licenceError.style.color = '#dc3545';
            }
            licenceValid = false;
            return;
        }

        licenceChecking = true;
        if (licenceError) {
            licenceError.textContent = 'Verification en cours...';
            licenceError.style.display = 'block';
            licenceError.style.color = '#ffc107';
        }

        try {
            const isUnique = await verifierLicenceUnique(licence);

            if (isUnique) {
                if (licenceError) {
                    licenceError.textContent = 'Numero de licence disponible';
                    licenceError.style.color = '#28a745';
                }
                licenceValid = true;
            } else {
                if (licenceError) {
                    licenceError.textContent = `Le numero ${licence} existe deja. Veuillez en choisir un autre.`;
                    licenceError.style.color = '#dc3545';
                }
                licenceValid = false;
            }
        } catch (error) {
            if (licenceError) {
                licenceError.textContent = 'Erreur de verification';
                licenceError.style.color = '#dc3545';
            }
            licenceValid = false;
        } finally {
            licenceChecking = false;
        }
    }

    /**
     * Genere automatiquement le prochain numero de licence disponible
     */
    async function genererProchainLicence() {
        try {
            const response = await fetch('http://localhost/projet_r401_toc/gestion_equipe_R401/projet-backend/JoueurAPI.php');
            const data = await response.json();

            let joueurs = [];
            if (data.status_code === 200 && data.data) {
                joueurs = data.data;
            }

            // Extraction des numeros de licence existants
            const licencesExistantes = joueurs.map(j => j.numero_licence);

            // Recherche du prochain numero disponible (LIC001, LIC002, ...)
            let numero = 1;
            let nouveauLicence = '';

            do {
                nouveauLicence = 'LIC' + String(numero).padStart(3, '0');
                numero++;
            } while (licencesExistantes.includes(nouveauLicence));

            licenceInput.value = nouveauLicence;
            await updateLicenceValidation();
        } catch (error) {
            console.error('Erreur lors de la generation du numero de licence:', error);
        }
    }

    /**
     * Gere la soumission du formulaire
     * @param {Event} e - Evenement de soumission
     */
    async function handleSubmit(e) {
        // Mise a jour du numero de licence en majuscules
        if (licenceInput) {
            licenceInput.value = licenceInput.value.trim().toUpperCase();
        }

        const erreurs = await validerFormulaire();

        if (erreurs.length > 0) {
            e.preventDefault();
            afficherErreurs(erreurs);
            return false;
        }

        return true;
    }

    // =============================================
    // AJOUT DES ECOUTEURS D'EVENEMENTS
    // =============================================

    // Soumission du formulaire
    form.addEventListener('submit', handleSubmit);

    // Validation en temps reel du numero de licence
    if (licenceInput) {
        licenceInput.addEventListener('blur', updateLicenceValidation);
        licenceInput.addEventListener('input', function() {
            if (licenceError) licenceError.style.display = 'none';
            licenceValid = false;
        });

        // Mise en majuscules automatique
        licenceInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }

    // Bouton pour generer automatiquement le numero de licence
    if (licenceInput && licenceInput.parentNode) {
        const genererBtn = document.createElement('button');
        genererBtn.type = 'button';
        genererBtn.textContent = 'Generer automatiquement';
        genererBtn.style.marginLeft = '10px';
        genererBtn.style.padding = '5px 10px';
        genererBtn.style.fontSize = '12px';
        genererBtn.style.cursor = 'pointer';
        genererBtn.addEventListener('click', genererProchainLicence);
        licenceInput.parentNode.appendChild(genererBtn);
    }

    console.log('Script de validation du formulaire joueur charge');
});