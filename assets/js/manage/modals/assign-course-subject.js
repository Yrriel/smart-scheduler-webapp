document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("assignCourseSubjectModal");
    if (!modal) return;

    const courseSelect = modal.querySelector("select[name='section_course']");
    const yearSelect   = modal.querySelector("select[name='year_level']");
    const tableBody    = modal.querySelector("#subjectAssignTable");
    const searchInput  = modal.querySelector("#subjectSearchAssign");

    function resetCheckboxes() {
        modal.querySelectorAll("input[name='subjects[]']").forEach(cb => {
            cb.checked = false;
        });
    }

    function loadAssignedSubjects() {
        const course = courseSelect.value;
        const year   = yearSelect.value;

        resetCheckboxes();

        if (!course || !year) return;

        fetch(`/project-4thyear/backend/functions/get_assigned_course_subjects.php?course=${encodeURIComponent(course)}&year=${encodeURIComponent(year)}`)
            .then(res => res.json())
            .then(subjects => {
                modal.querySelectorAll("input[name='subjects[]']").forEach(cb => {
                    cb.checked = subjects.includes(cb.value);
                });
            })
            .catch(err => console.error("Load assigned subjects error:", err));
    }

    courseSelect.addEventListener("change", loadAssignedSubjects);
    yearSelect.addEventListener("change", loadAssignedSubjects);

    /* =========================
       SEARCH FILTER
    ========================= */
    searchInput.addEventListener("input", () => {
        const q = searchInput.value.toLowerCase();

        tableBody.querySelectorAll("tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(q)
                ? ""
                : "none";
        });
    });

    /* =========================
       CANCEL BUTTON
    ========================= */
    document.getElementById("btncancelAssign-coursesubject")
        ?.addEventListener("click", () => {
            resetCheckboxes();
            searchInput.value = "";
            tableBody.querySelectorAll("tr").forEach(r => r.style.display = "");
            modal.classList.add("hidden");
        });
});
