// Utilitaires généraux
document.addEventListener('DOMContentLoaded', function() {
    // Fermer les alertes après 5 secondes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    });

    // Confirmation globale personnalisée sur les boutons qui en ont besoin
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submit = e.submitter;
            if (submit && submit.dataset.confirm) {
                if (!confirm(submit.dataset.confirm)) {
                    e.preventDefault();
                }
            }
        });
    });

    // Filtrer immédiatement en cas de changement sur le tableau de bord ingénieur
    const engineerFilterForm = document.getElementById('engineer-filter-form');
    if (engineerFilterForm) {
        engineerFilterForm.addEventListener('submit', function(e) {
            e.preventDefault();
        });

        const searchInput = document.getElementById('engineer-search');
        const checkboxes = engineerFilterForm.querySelectorAll('input[type="checkbox"]');
        const incidentRows = document.querySelectorAll('#engineer-incident-table tbody tr');

        const filterRows = () => {
            const searchValue = (searchInput ? searchInput.value.trim().toLowerCase() : '');
            const filterMyIncidents = document.getElementById('my-incidents')?.checked;
            const filterOpenOnly = document.getElementById('open-only')?.checked;

            incidentRows.forEach(row => {
                const title = row.querySelector('td:nth-child(1)')?.textContent.trim().toLowerCase() || '';
                const client = row.querySelector('td:nth-child(2)')?.textContent.trim().toLowerCase() || '';
                const status = row.dataset.status || '';
                const assignedToCurrent = row.dataset.assignedToCurrent === '1';

                const matchesSearch = !searchValue || title.includes(searchValue) || client.includes(searchValue);
                const matchesMyIncidents = !filterMyIncidents || assignedToCurrent;
                const matchesOpenOnly = !filterOpenOnly || status !== 'clôturé';

                row.style.display = (matchesSearch && matchesMyIncidents && matchesOpenOnly) ? '' : 'none';
            });
        };

        let searchTimeout;
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(filterRows, 200);
            });
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', filterRows);
        });

        filterRows();
    }

    const incidentForm = document.getElementById('incident-form');
    const fileInput = document.getElementById('files');
    const fileListContainer = document.getElementById('selected-file-list');
    let selectedFiles = [];

    const renderSelectedFiles = () => {
        if (!fileListContainer) return;
        fileListContainer.innerHTML = '';

        if (selectedFiles.length === 0) {
            fileListContainer.innerHTML = '<p style="color: #7f8c8d;">Aucun fichier sélectionné.</p>';
            return;
        }

        selectedFiles.forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'file-preview-item';
            item.style = 'display:flex; align-items:center; justify-content:space-between; padding:10px 12px; margin-top:8px; background:#f7f9fb; border:1px solid #dfe3e8; border-radius:6px;';
            item.innerHTML = `
                <div style="flex:1; min-width:0;">
                    <strong style="display:block; color:#2c3e50; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${file.name}</strong>
                    <span style="font-size:13px; color:#7f8c8d;">${Math.round(file.size / 1024)} KB</span>
                </div>
                <button type="button" class="btn btn-secondary btn-small remove-file-btn" data-index="${index}" style="margin-left:12px;">Supprimer</button>
            `;
            fileListContainer.appendChild(item);
        });
    };

    const updateFileInput = () => {
        if (!fileInput) return;
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
    };

    const addFilesToQueue = (files) => {
        for (let file of files) {
            const exists = selectedFiles.some(existing => existing.name === file.name && existing.size === file.size && existing.type === file.type);
            if (!exists) {
                selectedFiles.push(file);
            }
        }
        updateFileInput();
        renderSelectedFiles();
    };

    if (fileInput) {
        fileInput.addEventListener('change', (event) => {
            const files = Array.from(event.target.files);
            fileInput.value = '';
            addFilesToQueue(files);
        });
    }

    if (fileListContainer) {
        fileListContainer.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove-file-btn')) {
                const index = parseInt(event.target.dataset.index, 10);
                if (!Number.isNaN(index)) {
                    selectedFiles.splice(index, 1);
                    updateFileInput();
                    renderSelectedFiles();
                }
            }
        });
        renderSelectedFiles();
    }

    if (incidentForm) {
        incidentForm.addEventListener('submit', (event) => {
            if (!validateFiles()) {
                event.preventDefault();
            }
        });
    }
});

// Gestion des fichiers
function validateFiles() {
    const fileInput = document.getElementById('files');
    if (!fileInput) {
        return true;
    }

    const files = fileInput.files;
    const maxSize = 5 * 1024 * 1024; // 5 MB
    const allowedTypes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/gif',
        'text/plain',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    for (let file of files) {
        if (!allowedTypes.includes(file.type) && file.type !== '') {
            alert('Le fichier ' + file.name + ' n\'est pas dans un format accepté.');
            return false;
        }

        if (file.size > maxSize) {
            alert('Le fichier ' + file.name + ' est trop volumineux (max 5 MB)');
            return false;
        }
    }

    return true;
}

// Afficher/Masquer les éléments
function toggleElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.style.display = element.style.display === 'none' ? 'block' : 'none';
    }
}
