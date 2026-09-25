class MyFilter extends HTMLElement {
    connectedCallback() {
        const type = this.getAttribute('data-type');
        const name = this.getAttribute('data-name');
        const placeholder = this.getAttribute('data-placeholder') || 'Выберите';
        const allLabel = this.getAttribute('data-all') || 'Все';

        this.innerHTML = `
            <select name="${name}" class="filter-select">
                <option value="">${allLabel}</option>
            </select>
        `;

        fetch('/scripts/get_filter_data.php?type=' + type)
            .then(res => res.json())
            .then(data => {
                const select = this.querySelector('select');
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.ID;
                    option.textContent = item.Name || item.Type;
                    select.appendChild(option);
                });

                const params = new URLSearchParams(window.location.search);
                const saved = params.get(name);
                if (saved !== null) select.value = saved;
            });
    }
}
customElements.define('my-filter', MyFilter);