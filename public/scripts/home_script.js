(() => {
    const headerRow = document.getElementById('table-header');
    const body = document.getElementById('table-body');
    const selectAllBtn = document.getElementById('select-all');
    const deselectAllBtn = document.getElementById('deselect-all');
    const downloadBtn = document.getElementById('download-csv');
    const startDate = document.getElementById('start-date');
    const endDate = document.getElementById('end-date');
    const dropdowns = document.querySelectorAll(".container-dropdown");

    // Initialisation des dates
    const todayISO = new Date().toISOString().slice(0, 10);
    endDate.value = todayISO;

    // --- LOGIQUE DE RÉCUPÉRATION ---

    function getSelectedValues() {
        return Array.from(document.querySelectorAll('.item.checked .item-text'))
                    .map(span => span.textContent);
    }

    // --- INTERFACE : TABLEAU FACTICE ---

    function renderTable() {
        headerRow.innerHTML = '';
        body.innerHTML = '';

        const selectedItems = getSelectedValues();
        
        // Colonne d'index toujours présente
        const indexTh = document.createElement('th');
        indexTh.textContent = '#';
        headerRow.appendChild(indexTh);

        if (selectedItems.length === 0) {
            // État vide mais visible
            const emptyTh = document.createElement('th');
            emptyTh.textContent = "Aperçu des données (sélectionnez des filtres)";
            headerRow.appendChild(emptyTh);

            return;
        }

        // Colonnes dynamiques
        selectedItems.forEach(text => {
            const th = document.createElement('th');
            th.textContent = text;
            headerRow.appendChild(th);
        });

        // Remplissage factice
        for (let r = 1; r <= 5; r++) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${r}</td>`;
            selectedItems.forEach(text => {
                const td = document.createElement('td');
                td.textContent = `${text} ${r}`;
                tr.appendChild(td);
            });
            body.appendChild(tr);
        }
    }

    // --- GESTION DES DROPDOWNS ---

    dropdowns.forEach(container => {
        const selectBtn = container.querySelector(".select-btn");
        const items = container.querySelectorAll(".item");
        const btnText = container.querySelector(".btn-text");

        selectBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            selectBtn.classList.toggle("open");
        });

        items.forEach(item => {
            item.addEventListener("click", (e) => {
                e.stopPropagation(); // Empêche la fermeture du menu
                item.classList.toggle("checked");

                const checkedCount = container.querySelectorAll(".checked").length;
                const defaultText = btnText.getAttribute("data-default");
                
                btnText.innerText = checkedCount > 0 ? `${checkedCount} Sélectionné(s)` : defaultText;

                renderTable();
                downloadBtn.disabled = getSelectedValues().length === 0;
            });
        });
    });

    // --- ACTIONS GLOBALES ---

    selectAllBtn.addEventListener('click', () => {
        document.querySelectorAll('.item').forEach(i => i.classList.add('checked'));
        dropdowns.forEach(container => {
            const count = container.querySelectorAll(".checked").length;
            const label = container.querySelector(".btn-text");
            label.innerText = `${count} Sélectionné(s)`;
        });
        renderTable();
        downloadBtn.disabled = false;
    });

    deselectAllBtn.addEventListener('click', () => {
        document.querySelectorAll('.item').forEach(i => i.classList.remove('checked'));
        dropdowns.forEach(container => {
            const btnText = container.querySelector(".btn-text");
            btnText.innerText = btnText.getAttribute("data-default");
        });
        renderTable();
        downloadBtn.disabled = true;
    });

    // --- EXPORT VERS BACK-END (PHP/SLIM) ---

    downloadBtn.addEventListener('click', () => {
        // Construction de l'objet de filtres
        const payload = {
            start_date: startDate.value,
            end_date: endDate.value,
            verbs: Array.from(document.querySelectorAll('.container-dropdown:first-of-type .item.checked .item-text')).map(s => s.textContent),
            objects: Array.from(document.querySelectorAll('.container-dropdown:last-of-type .item.checked .item-text')).map(s => s.textContent)
        };

        // Envoi via formulaire invisible pour gérer le téléchargement natif
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/export/csv'; // Route Slim à définir

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'filters';
        input.value = JSON.stringify(payload);

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    });

    // Fermeture automatique au clic extérieur
    window.addEventListener("click", () => {
        dropdowns.forEach(c => c.querySelector(".select-btn").classList.remove("open"));
    });

    // Init
    renderTable();
})();