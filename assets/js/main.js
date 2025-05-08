// Realtime Student Search
document.addEventListener('DOMContentLoaded', function() {
    const studentSearchInput = document.querySelector('.students-header .search-form input[name="search"]');
    if (studentSearchInput) {
        let searchTimeout;
        
        studentSearchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const searchValue = e.target.value.trim();
            const deptCode = new URLSearchParams(window.location.search).get('dept');
            
            searchTimeout = setTimeout(() => {
                const url = new URL(window.location.href);
                if (searchValue) {
                    url.searchParams.set('search', searchValue);
                } else {
                    url.searchParams.delete('search');
                }
                
                fetch(`${url.pathname}?${url.searchParams.toString()}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newStudentCards = doc.querySelector('.student-cards');
                        if (newStudentCards) {
                            document.querySelector('.student-cards').innerHTML = newStudentCards.innerHTML;
                        }
                    });
            }, 300); // Debounce time
        });
    }
});