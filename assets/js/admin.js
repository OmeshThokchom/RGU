document.addEventListener('DOMContentLoaded', function() {
    // Enable form fields for input
    const formInputs = document.querySelectorAll('form[data-type] input, form[data-type] textarea, form[data-type] select');
    formInputs.forEach(input => {
        input.removeAttribute('disabled');
    });

    // Handle form submissions
    const forms = document.querySelectorAll('form[data-type]');
    forms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const type = form.dataset.type;
            
            // Check for ID field based on form type
            const idFieldName = type === 'students' ? 'student_id' : 
                              type === 'faculties' ? 'faculty_id' :
                              type === 'notices' ? 'notice_id' :
                              type === 'events' ? 'event_id' : 'dept_id';
            
            // Determine if this is an update operation by checking for the specific ID field
            const isUpdate = form.querySelector(`input[name="${idFieldName}"]`) !== null;
            
            formData.append('action', isUpdate ? 'update' : 'create');
            formData.append('table', type);
            
            try {
                const response = await fetch('ajax_handlers.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message || 'Successfully saved!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(result.message || 'An error occurred', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred while saving the data', 'error');
            }
        });
    });

    // Handle add button clicks
    const addButtons = document.querySelectorAll('[data-action="add"]');
    addButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const section = button.closest('[data-type]');
            const type = section ? section.dataset.type : button.dataset.type;
            
            if (type) {
                // Find the form for this type
                const form = document.querySelector(`form[data-type="${type}"]`);
                if (form) {
                    // Reset form and clear any existing ID
                    form.reset();
                    const idField = form.querySelector('input[type="hidden"]');
                    if (idField) {
                        idField.remove();
                    }
                    
                    // Scroll to form
                    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                    // Focus first input
                    const firstInput = form.querySelector('input:not([type="hidden"]), select, textarea');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }
            }
        });
    });

    // Add deleteRecord function globally
    window.deleteRecord = async function(table, idField, id) {
        if (!confirm('Are you sure you want to delete this record?')) {
            return;
        }

        try {
            const response = await fetch('ajax_handlers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&table=${table}&id_field=${idField}&id=${id}`
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const result = await response.json();
            
            if (result.success) {
                showToast('Record deleted successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(result.message || 'Error deleting record', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error deleting record', 'error');
        }
    };

    // Add showToast function globally
    window.showToast = function(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `glass-toast ${type}`;
        toast.innerHTML = `
            <i class="fi fi-sr-${type === 'success' ? 'check' : 'cross-circle'}"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('active');
        }, 100);
        
        setTimeout(() => {
            toast.classList.remove('active');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };

    // Dashboard search functionality
    let searchTimeout;
    const searchInput = document.getElementById('dashboardSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value;

            // Clear results if search is empty
            if (!searchTerm) {
                document.querySelectorAll('.search-results').forEach(div => div.innerHTML = '');
                document.querySelectorAll('.dashboard-card').forEach(card => {
                    const countElement = card.querySelector('.count');
                    if (countElement) {
                        countElement.style.display = 'block';
                    }
                });
                return;
            }

            // Hide counts when showing search results
            document.querySelectorAll('.dashboard-card').forEach(card => {
                const countElement = card.querySelector('.count');
                if (countElement) {
                    countElement.style.display = 'none';
                }
            });

            // Debounce the search
            searchTimeout = setTimeout(() => {
                fetch('ajax_handlers.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=search&term=${encodeURIComponent(searchTerm)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update each section with its results
                        Object.keys(data).forEach(section => {
                            if (section === 'success') return;
                            const resultsDiv = document.querySelector(`[data-section="${section}"] .search-results`);
                            if (resultsDiv && Array.isArray(data[section])) {
                                resultsDiv.innerHTML = data[section].map(item => {
                                    const displayName = item.title || item.name || 
                                                      (item.first_name ? `${item.first_name} ${item.last_name}` : '');
                                    return `<div class="search-item">${displayName}</div>`;
                                }).join('');
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
            }, 300);
        });
    }

    // Admin card search functionality
    const adminSearchInput = document.getElementById('adminSearch');
    if (adminSearchInput) {
        adminSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const adminCards = document.querySelectorAll('.admin-card');
            
            adminCards.forEach(card => {
                const searchData = card.dataset.search.toLowerCase();
                if (searchData.includes(searchTerm)) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    }
});