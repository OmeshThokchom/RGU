// Realtime Student and Faculty Search
document.addEventListener('DOMContentLoaded', function() {
    // Realtime Student Search
    const studentSearchInput = document.querySelector('.student-search-form input[name="student_search"]');
    if (studentSearchInput) {
        let studentSearchTimeout;
        
        studentSearchInput.addEventListener('input', function(e) {
            clearTimeout(studentSearchTimeout);
            const searchValue = e.target.value.trim();
            const form = e.target.closest('form');
            const deptCode = form.querySelector('input[name="dept"]').value;
            
            studentSearchTimeout = setTimeout(() => {
                const url = new URL(window.location.href);
                if (searchValue) {
                    url.searchParams.set('student_search', searchValue);
                } else {
                    url.searchParams.delete('student_search');
                }
                url.searchParams.set('dept', deptCode);
                
                fetch(`${url.pathname}?${url.searchParams.toString()}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newStudentCards = doc.querySelector('.student-cards');
                        if (newStudentCards) {
                            document.querySelector('.student-cards').innerHTML = newStudentCards.innerHTML;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }, 300);
        });
    }

    // Realtime Faculty Search
    const facultySearchInput = document.querySelector('.faculty-search-form input[name="faculty_search"]');
    if (facultySearchInput) {
        let facultySearchTimeout;
        
        facultySearchInput.addEventListener('input', function(e) {
            clearTimeout(facultySearchTimeout);
            const searchValue = e.target.value.trim();
            const form = e.target.closest('form');
            const deptCode = form.querySelector('input[name="dept"]').value;
            
            facultySearchTimeout = setTimeout(() => {
                const url = new URL(window.location.href);
                if (searchValue) {
                    url.searchParams.set('faculty_search', searchValue);
                } else {
                    url.searchParams.delete('faculty_search');
                }
                url.searchParams.set('dept', deptCode);
                
                fetch(`${url.pathname}?${url.searchParams.toString()}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newFacultyGrid = doc.querySelector('.faculty-grid');
                        if (newFacultyGrid) {
                            document.querySelector('.faculty-grid').innerHTML = newFacultyGrid.innerHTML;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }, 300);
        });
    }
});