console.log("assign-course-subject loaded");


window.initAssignCourseSubjectModal = function () {
    const modal = createModal('assignCourseSubjectModal');
    if (!modal) return;

    const openBtn = document.getElementById('assign-btn-course-subject');
    const cancelBtn = document.getElementById('btncancelAssign-coursesubject');

    const searchInput = document.getElementById('subjectSearchAssign');
    const tableBody = document.getElementById('subjectAssignTable');

    if (openBtn) openBtn.onclick = modal.open;
    if (cancelBtn) cancelBtn.onclick = modal.close;

    // 🔍 SEARCH FUNCTION (client-side, no reload)
    if (searchInput && tableBody) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.toLowerCase();

            qsa('tr', tableBody).forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
        });
    }
};
