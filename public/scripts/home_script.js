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

    // --- FONCTIONS DE MISE À JOUR ---

    function getSelectedValues() {
        // Récupère tous les items cochés dans tous les dropdowns
        return Array.from(document.querySelectorAll('.item.checked .item-text'))
            .map(span => span.textContent);
    }

    function updateDownloadState() {
        const selected = getSelectedValues();
        downloadBtn.disabled = selected.length === 0;
    }

    function renderTable() {
        headerRow.innerHTML = '';
        body.innerHTML = '';

        const selectedItems = getSelectedValues();
        if (selectedItems.length === 0) return;

        // Header
        const indexTh = document.createElement('th');
        indexTh.textContent = '#';
        headerRow.appendChild(indexTh);

        selectedItems.forEach(text => {
            const th = document.createElement('th');
            th.textContent = text;
            headerRow.appendChild(th);
        });

        // Simulation de données (Traces LRS)
        for (let r = 1; r <= 5; r++) {
            const tr = document.createElement('tr');
            const idxTd = document.createElement('td');
            idxTd.textContent = r;
            tr.appendChild(idxTd);

            selectedItems.forEach(text => {
                const td = document.createElement('td');
                td.textContent = `${text}`;
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
        const defaultText = btnText.innerText;

        selectBtn.addEventListener("click", (e) => {
            e.stopPropagation(); // Empêche la fermeture immédiate via window.click
            selectBtn.classList.toggle("open");
        });

        items.forEach(item => {
            item.addEventListener("click", () => {
                item.classList.toggle("checked");

                let checkedCount = container.querySelectorAll(".checked").length;
                btnText.innerText = checkedCount > 0 ? `${checkedCount} Sélectionné(s)` : defaultText;

                // Liaison avec le tableau et le bouton
                renderTable();
                updateDownloadState();
            });
        });
    });

    // --- BOUTONS D'ACTIONS GLOBALES ---

    selectAllBtn.addEventListener('click', () => {
        document.querySelectorAll('.item').forEach(i => i.classList.add('checked'));
        // Mise à jour des labels de texte pour chaque dropdown
        dropdowns.forEach(container => {
            const count = container.querySelectorAll(".checked").length;
            const label = container.querySelector(".btn-text");
            if (count > 0) label.innerText = `${count} Sélectionné(s)`;
        });
        renderTable();
        updateDownloadState();
    });

    deselectAllBtn.addEventListener('click', () => {
        // 1. Décoche tous les items
        document.querySelectorAll('.item').forEach(i => i.classList.remove('checked'));

        // 2. Réinitialise les textes en utilisant l'attribut data-default
        dropdowns.forEach(container => {
            const btnText = container.querySelector(".btn-text");
            btnText.innerText = btnText.getAttribute("data-default");
        });

        // 3. Rafraîchir l'interface
        renderTable();
        updateDownloadState();
    });

    // --- EXPORT CSV ---

    downloadBtn.addEventListener('click', () => {
        const selectedItems = getSelectedValues();
        const header = ['#', ...selectedItems];
        const rows = [];
        for (let r = 1; r <= 8; r++) {
            const row = [r, ...selectedItems.map(c => `${c} data_${r}`)];
            rows.push(row);
        }
        const csv = [header.join(','), ...rows.map(r => r.join(','))].join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `LRS_Export_${new Date().getTime()}.csv`;
        a.click();
    });

    // Fermeture des menus au clic extérieur
    window.addEventListener("click", () => {
        dropdowns.forEach(c => c.querySelector(".select-btn").classList.remove("open"));
    });

    // Initialisation
    renderTable();
    updateDownloadState();
})();