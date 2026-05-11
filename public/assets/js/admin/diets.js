const priceTable = document.getElementById('priceTable');
const addPriceRow = document.getElementById('addPriceRow');

if (priceTable && addPriceRow) {
    const buildRow = () => {
        const row = document.createElement('div');
        row.className = 'table-row';
        row.dataset.row = '';
        row.innerHTML = `
            <div>
                <input type="number" name="duration_days[]" min="1" placeholder="30">
            </div>
            <div>
                <input type="number" name="price[]" min="1" step="0.01" placeholder="15000">
            </div>
            <div class="row-actions">
                <button class="button danger" type="button" data-remove>Retirer</button>
            </div>
        `;
        return row;
    };

    addPriceRow.addEventListener('click', () => {
        priceTable.appendChild(buildRow());
    });

    priceTable.addEventListener('click', (event) => {
        const target = event.target;
        if (target && target.matches('[data-remove]')) {
            const row = target.closest('[data-row]');
            if (row) {
                row.remove();
            }
        }
    });
}
