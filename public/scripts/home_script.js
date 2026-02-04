// Dynamic filters and table (visual-only)
(() => {
    const filters = Array.from(document.querySelectorAll('.filter-checkbox'));
    const headerRow = document.getElementById('table-header');
    const body = document.getElementById('table-body');
    const selectAllBtn = document.getElementById('select-all');
    const deselectAllBtn = document.getElementById('deselect-all');
    const downloadBtn = document.getElementById('download-csv');
    const startDate = document.getElementById('start-date');
    const endDate = document.getElementById('end-date');

    // set default end date to today
    const todayISO = new Date().toISOString().slice(0, 10);
    endDate.value = todayISO;

    function updateDownloadState() {
        const anyChecked = filters.some(f => f.checked);
        downloadBtn.disabled = !anyChecked;
    }

    function renderTable() {
        // clear
        headerRow.innerHTML = '';
        body.innerHTML = '';
        // always include a left index column to simulate Excel
        const indexTh = document.createElement('th');
        indexTh.textContent = '#';
        headerRow.appendChild(indexTh);

        const activeFilters = filters.filter(f => f.checked).map(f => f.dataset.column);

        activeFilters.forEach(col => {
            const th = document.createElement('th');
            th.textContent = col;
            headerRow.appendChild(th);
        });

        // add some fake rows (visual only)
        for (let r = 1; r <= 5; r++) {
            const tr = document.createElement('tr');
            const idxTd = document.createElement('td');
            idxTd.textContent = r;
            tr.appendChild(idxTd);
            activeFilters.forEach(col => {
                const td = document.createElement('td');
                td.textContent = `${col} ${r}`;
                tr.appendChild(td);
            });
            body.appendChild(tr);
        }
    }

    filters.forEach(f => {
        f.addEventListener('change', () => {
            renderTable();
            updateDownloadState();
        });
    });

    selectAllBtn.addEventListener('click', () => {
        filters.forEach(f => f.checked = true);
        renderTable();
        updateDownloadState();
    });
    deselectAllBtn.addEventListener('click', () => {
        filters.forEach(f => f.checked = false);
        renderTable();
        updateDownloadState();
    });

    downloadBtn.addEventListener('click', () => {
        if (downloadBtn.disabled) return;
        const activeFilters = filters.filter(f => f.checked).map(f => f.dataset.column);
        // build CSV header from active filters (dates are not included)
        const header = ['#', ...activeFilters];
        const rows = [];
        for (let r = 1; r <= 8; r++) {
            const row = [r, ...activeFilters.map(c => `${c} ${r}`)];
            rows.push(row);
        }
        const csv = [header.join(','), ...rows.map(r => r.join(','))].join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'export.csv';
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    });

    // initial render
    renderTable();
    updateDownloadState();
})();
