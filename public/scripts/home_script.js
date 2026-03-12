(() => {
    const headerRow = document.getElementById('table-header');
    const body = document.getElementById('table-body');
    const selectAllBtn = document.getElementById('select-all');
    const deselectAllBtn = document.getElementById('deselect-all');
    const downloadBtn = document.getElementById('download-csv');
    const startDate = document.getElementById('start-date');
    const endDate = document.getElementById('end-date');
    const dropdowns = document.querySelectorAll(".container-dropdown");
    const logoutBtn = document.getElementById('logoutBtn');

    // Initialisation des dates (Aujourd'hui par défaut pour la fin)
    const todayISO = new Date().toISOString().slice(0, 10);
    if (endDate) endDate.value = todayISO;

    // --- Descriptions des verbes et activités ---
    const registryDescriptions = {
        verbs: {
            "completed": "l'acteur a terminé l'objet (Test,Survey) associé.",
            "received": "l'acteur a reçu un élément (Survey, Feeback, Message, ...) associé.",
            "accessed": "l'Acteur a accédé à un l'élément associé (un Resultat, un Feedback, un Survey), il l'a 'visualisé' et n'a rien fait d'autre",
            "acknowledge": "L'acteur s'est vu présenté l'objet associé.",
            "answered": "L'acteur à répondu à la question référencée par l'object associé.",
            "enrolled": "L'utilisateur a accepté les condition d'utilisation du service.",
            "login": "L'utilisateur s'est connecté à l'application",
            "logout": "L'utilisateur s'est déconnecté et/ou a cloturé sa session dans l'application",
            "disconnect": "L'utilisateur à désactivé son affiliation au service.",
            "gave": "L'utilisateur à donné une information.",
            "updated": "Le verbe \"update\" indique que l'acteur a modifié l'objet."
        },
        activities: {
            "ULLAMobileApp": "Un objet dédié aux interactions avec l'app mobile ULLA",
            "ULLAMobileAppFeed": "Un objet dédié aux interactions avec le feed de l'app mobile ULLA",
            "Moods": "Un objet dédié aux émotions",
            "GenericObject": "Un objet générique sans résultat.",
            "GenericObjectWithResult": "Un objet générique avec résultat.",
            "Assessment": "Un test, QCM ou QROL",
            "AssessmentPart": "Une partie de test QCM.",
            "OpenAnswerQuestion": "Une question à réponse ouverte longue",
            "OpenAnswerQuestionCriteria": "Un critère associé à une question à réponse ouverte longue",
            "Survey": "Une enquête à caractère pédagogique orientée LA",
            "SurveyQuestion": "Une question associée à une enquête à caractère pédagogique orientée LA",
            "Feedback": "Un Feedback SMART",
            "FeedbackPart": "Un Feedback SMART pour une partie de test",
            "FeedbackMessage": "Un message associé à Feedback SMART",
            "Message": "Un message à destination des étudiants"
        }
    };

    // --- METHODES ---

    /**
     * Récupère les data-name des items cochés dans un menu spécifique
     * @param {string} dropdownName - Le nom affiché par défaut
     */
    function getValuesFromDropdown(dropdownName) {
        let result = [];
        dropdowns.forEach(container => {
            const btnText = container.querySelector('.btn-text');
            if (btnText && btnText.getAttribute('data-default') === dropdownName) {
                const checkedItems = container.querySelectorAll('.item.checked');
                checkedItems.forEach(item => {
                    const name = item.getAttribute('data-name');
                    if (name) {
                        result.push(name);
                    }
                });
            }
        });
        return result;
    }

    /**
     * Active ou désactive le bouton Télécharger selon la sélection
     */
    function checkDownloadButtonState() {
        const count = document.querySelectorAll('.item.checked').length;
        if (count > 0) {
            downloadBtn.removeAttribute('disabled');
        } else {
            downloadBtn.setAttribute('disabled', 'true');
        }
    }

    // --- INTERFACE : TABLEAU D'APERÇU ET DESCRIPTIONS ---

    function renderDescriptions() {
        const descriptionsContainer = document.getElementById('descriptions-container');
        const descriptionsList = document.getElementById('descriptions-list');
        if (!descriptionsContainer || !descriptionsList) return;

        descriptionsList.innerHTML = '';
        const selectedVerbs = getValuesFromDropdown('Verbes');
        const selectedActivities = getValuesFromDropdown('Activités');

        if (selectedVerbs.length === 0 && selectedActivities.length === 0) {
            descriptionsContainer.style.display = 'none';
            return;
        }

        descriptionsContainer.style.display = 'block';

        selectedVerbs.forEach(verb => {
            if (registryDescriptions.verbs[verb]) {
                const li = document.createElement('li');
                li.innerHTML = `<strong>Verbe "${verb}" :</strong> ${registryDescriptions.verbs[verb]}`;
                descriptionsList.appendChild(li);
            }
        });

        selectedActivities.forEach(act => {
            if (registryDescriptions.activities[act]) {
                const li = document.createElement('li');
                li.innerHTML = `<strong>Activité "${act}" :</strong> ${registryDescriptions.activities[act]}`;
                descriptionsList.appendChild(li);
            }
        });
    }

    function renderTable() {
        const headerRow = document.getElementById('table-header');
        const body = document.getElementById('table-body');

        if (!headerRow || !body) return;

        headerRow.innerHTML = '';
        body.innerHTML = '';

        const selectedVerbs = getValuesFromDropdown('Verbes');
        const selectedActivities = getValuesFromDropdown('Activités');

        // Structure des colonnes du tableau correspondant au à celles du CSV
        const xapiColumns = [
            'Statement ID',
            'Actor Type',
            'Pseudonymized Actor Account Name',
            // 'Actor Account HomePage',
            // 'Verb ID',
            'Verb Name',
            // 'Verb Display',
            'Timestamp',
            'Object Type',
            // 'Object ID',
            // 'Object Definition',
            'Object Name',
            // 'Stored',
            'Authority Type',
            'Authority Name',
            // 'Authority Account Name',
            // 'Authority Account HomePage',
            // 'Version'
        ];

        if (selectedVerbs.length === 0 && selectedActivities.length === 0) {
            const emptyTh = document.createElement('th');
            emptyTh.textContent = "Aperçu des données (sélectionnez des filtres)";
            headerRow.appendChild(emptyTh);
            return;
        }

        // Ajout des en-têtes
        xapiColumns.forEach(text => {
            const th = document.createElement('th');
            th.textContent = text;
            headerRow.appendChild(th);
        });

        let itemsToRender = [];

        // Ajout des les lignes des verbes
        selectedVerbs.forEach(v => {
            itemsToRender.push({ verb: v, activity: 'N/A' });
        });

        // Ajout des lignes des activités
        selectedActivities.forEach(a => {
            itemsToRender.push({ verb: 'N/A', activity: a });
        });

        // Remplissage avec descriptions textuelles et valeurs sélectionnées
        itemsToRender.forEach((combo) => {
            const tr = document.createElement('tr');

            // Valeurs dynamiques pour les colonnes "Verb Name" et "Object Name"
            const verbName = combo.verb !== 'N/A' ? combo.verb : 'Nom du verbe';
            const activityName = combo.activity !== 'N/A' ? combo.activity : 'Nom de l\'objet';

            // Création des données descriptives pour chaque colonne
            const rowData = [
                "ID du statement",                         // Statement ID
                "Agent ou Group",                          // Actor Type
                "Hash pseudonymisé de l'étudiant",         // Actor Account Name
                // "URL du domaine",                       // Actor Account HomePage
                // "URL du verbe",                         // Verb ID
                verbName,                                  // Verb Name (DYNAMIQUE)
                // "Texte lié au verbe",                   // Verb Display
                "Date et heure de l'action",               // Timestamp
                "Type de l'objet",                         // Object Type
                // "URL de l'activité",                    // Object ID
                // "Catégorie de l'activité",              // Object Definition
                activityName,                              // Object Name (DYNAMIQUE)
                // "Date d'enregistrement",                // Stored
                "Agent",                                   // Authority Type
                "Nom de l'application cliente",            // Authority Name
                // "Compte technique LRS",                 // Authority Account Name
                // "URL de l'autorité",                    // Authority Account HomePage
                // "1.0.0"                                 // Version
            ];

            // Insertion dans le tableau
            rowData.forEach(cellData => {
                const td = document.createElement('td');

                // Mise en évidence de la valeur active de la ligne
                if ((cellData === verbName && combo.verb !== 'N/A') ||
                    (cellData === activityName && combo.activity !== 'N/A')) {
                    td.innerHTML = `<strong style="color: #000;">${cellData}</strong>`;
                } else {
                    td.textContent = cellData;
                }
                tr.appendChild(td);
            });

            body.appendChild(tr);
        });
    }

    // --- GESTION DES INTERACTIONS ---

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

                // Mise à jour du texte du bouton
                const checkedCount = container.querySelectorAll(".checked").length;
                const defaultText = btnText.getAttribute("data-default");
                btnText.innerText = checkedCount > 0 ? `${checkedCount} Sélectionné(s)` : defaultText;

                renderTable();
                renderDescriptions(); // Mise à jour des descriptions
                checkDownloadButtonState();
            });
        });
    });

    // Fermeture des menus au clic extérieur
    window.addEventListener("click", () => {
        dropdowns.forEach(c => c.querySelector(".select-btn").classList.remove("open"));
    });


    // --- BOUTONS GLOBAUX ---

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', () => {
            document.querySelectorAll('.item').forEach(i => i.classList.add('checked'));
            dropdowns.forEach(container => {
                const count = container.querySelectorAll(".checked").length;
                const label = container.querySelector(".btn-text");
                label.innerText = `${count} Sélectionné(s)`;
            });
            renderTable();
            renderDescriptions(); // Mise à jour des descriptions
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
            renderDescriptions(); // Mise à jour des descriptions
            checkDownloadButtonState();
        });
    }

    // --- EXPORT VERS LE SERVEUR ---

    if (downloadBtn) {
        downloadBtn.addEventListener('click', (e) => {
            const verbsList = getValuesFromDropdown('Verbes');
            const objectsList = getValuesFromDropdown('Activités');

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

            form.submit(); // Envoi

            document.body.removeChild(form);
        });
    }

    if(logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            window.location.href = '/api/logout';
        });
    }

    // Initialisation au chargement de la page
    renderTable();
    renderDescriptions();
    checkDownloadButtonState();

})();