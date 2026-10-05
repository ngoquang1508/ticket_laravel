export function initSearchBars() {
    const searchInputs = document.querySelectorAll(
        'input[id$="-search-input"]'
    );

    searchInputs.forEach((searchInput) => {
        const id = searchInput.id.replace('-input', '');
        const clearButton = document.getElementById(`${id}-clear`);

        if (!clearButton) {
            return;
        }

        updateClearButton(searchInput, clearButton);

        searchInput.addEventListener('input', () => {
            updateClearButton(searchInput, clearButton);
        });

        clearButton.addEventListener('click', () => {
            searchInput.value = '';
            updateClearButton(searchInput, clearButton);
            searchInput.focus();
        });
    });
}

function updateClearButton(searchInput, clearButton) {
    clearButton.classList.toggle(
        'hidden',
        searchInput.value === ''
    );
}