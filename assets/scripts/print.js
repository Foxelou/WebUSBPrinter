function updateFileName(input) {
    const fileDisplay = document.getElementById('fileDisplay');
    const filePlaceholder = document.getElementById('filePlaceholder');

    if (input.files && input.files[0]) {
        fileDisplay.textContent = input.files[0].name;
        fileDisplay.className = 'file-selected';
        filePlaceholder.style.display = 'none';
    } else {
        fileDisplay.textContent = 'Cliquez pour sélectionner un fichier PDF';
        fileDisplay.className = '';
        filePlaceholder.style.display = 'block';
    }
}

function toggleCustomPageInput(value) {
    const customInput = document.getElementById('customPagesInput');
    if (value === 'custom') {
        customInput.classList.add('show');
    } else {
        customInput.classList.remove('show');
    }
}

document.querySelector("form").addEventListener("submit", () => {
    showNotification('⏳ Traitement en cours...', 'info');
});