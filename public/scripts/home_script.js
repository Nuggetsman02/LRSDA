(() => {
    console.log("--- SCRIPT LRS DATA ANALYST CHARGÉ ---");

    const headerRow = document.getElementById('table-header');
    const body = document.getElementById('table-body');
    const selectAllBtn = document.getElementById('select-all');
    const deselectAllBtn = document.getElementById('deselect-all');
    const downloadBtn = document.getElementById('download-csv');
    const startDate = document.getElementById('start-date');
    const endDate = document.getElementById('end-date');
    const dropdowns = document.querySelectorAll(".container-dropdown");

    // Initialisation des dates (Aujourd'hui par défaut pour la fin)
    const todayISO = new Date().toISOString().slice(0, 10);
    if(endDate) endDate.value = todayISO;

    // --- 1. FONCTIONS UTILITAIRES ---

    /**
     * Récupère les data-name des items cochés dans un menu spécifique
     * @param {string} dropdownName - Le nom affiché par défaut (ex: "Verbes", "Activités")
     */
    function getValuesFromDropdown(dropdownName) {
        let result = [];
        dropdowns.forEach(container => {
            const btnText = container.querySelector('.btn-text');
            // On vérifie si c'est le bon menu en regardant son attribut data-default
            if (btnText && btnText.getAttribute('data-default') === dropdownName) {
                const checkedItems = container.querySelectorAll('.item.checked');
                result = Array.from(checkedItems).map(item => item.getAttribute('data-name'));
            }
        });
        return result;
    }

    /**
     * Récupère simplement le texte pour l'affichage du tableau de prévisualisation
     */
    function getSelectedLabels() {
        return Array.from(document.querySelectorAll('.item.checked .item-text'))
            .map(span => span.textContent);
    }

    /**
     * Active ou désactive le bouton Télécharger selon la sélection
     */
    function checkDownloadButtonState() {
        const count = document.querySelectorAll('.item.checked').length;
        if (count > 0) {
            downloadBtn.removeAttribute('disabled');
            // downloadBtn.classList.remove('disabled'); // Décommenter si vous avez une classe CSS spécifique
        } else {
            downloadBtn.setAttribute('disabled', 'true');
        }
    }

    // --- 2. INTERFACE : TABLEAU D'APERÇU ---

    function renderTable() {
        if (!headerRow || !body) return;

        headerRow.innerHTML = '';
        body.innerHTML = '';

        const selectedLabels = getSelectedLabels();

        // Colonne d'index toujours présente
        const indexTh = document.createElement('th');
        indexTh.textContent = '#';
        headerRow.appendChild(indexTh);

        if (selectedLabels.length === 0) {
            const emptyTh = document.createElement('th');
            emptyTh.textContent = "Aperçu des données (sélectionnez des filtres)";
            headerRow.appendChild(emptyTh);
            return;
        }

        // Colonnes dynamiques
        selectedLabels.forEach(text => {
            const th = document.createElement('th');
            th.textContent = text;
            headerRow.appendChild(th);
        });

        // Remplissage factice (5 lignes pour l'exemple)
        for (let r = 1; r <= 5; r++) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${r}</td>`;
            selectedLabels.forEach(text => {
                const td = document.createElement('td');
                td.textContent = `${text} ${r}`;
                tr.appendChild(td);
            });
            body.appendChild(tr);
        }
    }

    // --- 3. GESTION DES INTERACTIONS (Menus Déroulants) ---

    dropdowns.forEach(container => {
        const selectBtn = container.querySelector(".select-btn");
        const items = container.querySelectorAll(".item");
        const btnText = container.querySelector(".btn-text");

        // Ouverture / Fermeture
        selectBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            // Ferme les autres menus
            dropdowns.forEach(c => {
                if (c !== container) c.querySelector(".select-btn").classList.remove("open");
            });
            selectBtn.classList.toggle("open");
        });

        // Clic sur un item
        items.forEach(item => {
            item.addEventListener("click", (e) => {
                e.stopPropagation();
                item.classList.toggle("checked");

                // Mise à jour du texte du bouton (ex: "3 Sélectionné(s)")
                const checkedCount = container.querySelectorAll(".checked").length;
                const defaultText = btnText.getAttribute("data-default");
                btnText.innerText = checkedCount > 0 ? `${checkedCount} Sélectionné(s)` : defaultText;

                renderTable();
                checkDownloadButtonState();
            });
        });
    });

    // --- 4. BOUTONS GLOBAUX ---

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', () => {
            document.querySelectorAll('.item').forEach(i => i.classList.add('checked'));
            dropdowns.forEach(container => {
                const count = container.querySelectorAll(".checked").length;
                const label = container.querySelector(".btn-text");
                label.innerText = `${count} Sélectionné(s)`;
            });
            renderTable();
            checkDownloadButtonState();
        });
    }

    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', () => {
            document.querySelectorAll('.item').forEach(i => i.classList.remove('checked'));
            dropdowns.forEach(container => {
                const btnText = container.querySelector(".btn-text");
                btnText.innerText = btnText.getAttribute("data-default");
            });
            renderTable();
            checkDownloadButtonState();
        });
    }

    // --- 5. EXPORT VERS LE SERVEUR (PHP) ---

    if (downloadBtn) {
        downloadBtn.addEventListener('click', (e) => {
            // e.preventDefault(); // Utile si le bouton est dans un <form> existant

            // Récupération via la fonction robuste (par Nom de menu)
            // ATTENTION : Ces noms doivent correspondre à l'attribut data-default dans votre HTML
            const verbsList = getValuesFromDropdown('Verbes');
            const objectsList = getValuesFromDropdown('Activités'); 

            console.log("Export en cours...");
            console.log("- Verbes:", verbsList);
            console.log("- Objets:", objectsList);

            const payload = {
                start_date: startDate ? startDate.value : '',
                end_date: endDate ? endDate.value : '',
                verbs: verbsList,
                objects: objectsList
            };

            // Création du formulaire invisible pour l'envoi POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/api/export';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'filters';
            input.value = JSON.stringify(payload);

            form.appendChild(input);
            document.body.appendChild(form);
            
            form.submit(); // Envoi réel
            
            document.body.removeChild(form);
        });
    }

    // Fermeture des menus au clic extérieur
    window.addEventListener("click", () => {
        dropdowns.forEach(c => c.querySelector(".select-btn").classList.remove("open"));
    });

    // Initialisation au chargement de la page
    renderTable();
    checkDownloadButtonState();

})();