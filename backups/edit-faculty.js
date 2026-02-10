// assets/js/manage/modals/edit-faculty.js

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("editFacultyModal");
    const editButtons = document.querySelectorAll(".edit-btn-faculty");

    editButtons.forEach(btn => {
        btn.addEventListener("click", () => {

            // Basic fields
            document.getElementById("edit_faculty_id").value = btn.dataset.id;
            document.getElementById("edit_faculty_name").value = btn.dataset.name;
            document.getElementById("edit_employment_type").value = btn.dataset.employment;
            document.getElementById("edit_current_status").value = btn.dataset.status;
            document.getElementById("edit_total_hours").value = btn.dataset.hours;

            // Subjects
            const selectedSubjects = (btn.dataset.subjects || "")
                .split(",")
                .map(s => s.trim())
                .filter(Boolean);

            // Reset all checkboxes first
            document
                .querySelectorAll("#editFacultySubjectTable input[type='checkbox']")
                .forEach(cb => {
                    cb.checked = selectedSubjects.includes(cb.value);
                });

            // Preferred days
            const days = (btn.dataset.days || "").split(",");
            document.querySelectorAll("input[name='preferred_day[]']").forEach(cb => {
                cb.checked = days.includes(cb.value);
            });

            // Show modal
            modal.classList.remove("hidden");
        });
    });
});


document.addEventListener("click", (e) => {
    const btn = e.target.closest(".edit-btn-faculty");
    if (!btn) return;

    const modal = document.getElementById("editFacultyModal");
    if (!modal) return;

    // ---------- BASIC FIELDS ----------
    modal.querySelector("#edit_faculty_id").value = btn.dataset.id || "";
    modal.querySelector("#edit_faculty_name").value = btn.dataset.name || "";
    modal.querySelector("#edit_employment_type").value = btn.dataset.employment || "";
    modal.querySelector("#edit_current_status").value = btn.dataset.status || "";
    modal.querySelector("#edit_total_hours").value = btn.dataset.hours || "";

    // ---------- PREFERRED DAYS ----------
    const selectedDays = (btn.dataset.days || "").split(",");
    modal.querySelectorAll("input[name='preferred_day[]']").forEach(cb => {
        cb.checked = selectedDays.includes(cb.value);
    });

    // ---------- SUBJECT CHECKBOXES ----------
    const selectedSubjects = (btn.dataset.subjects || "").split(",");

    modal.querySelectorAll("input[name='subjects[]']").forEach(cb => {
        cb.checked = selectedSubjects.includes(cb.value);
    });

    // ---------- RESET SEARCH ----------
    const searchInput = modal.querySelector("#editFacultySubjectSearch");
    if (searchInput) searchInput.value = "";

    modal.classList.remove("hidden");
});

// ---------- SUBJECT SEARCH FILTER ----------
document.addEventListener("DOMContentLoaded", () => {
    const search = document.getElementById("editFacultySubjectSearch");
    const table = document.getElementById("editFacultySubjectTable");

    if (!search || !table) return;

    search.addEventListener("input", () => {
        const q = search.value.toLowerCase();

        table.querySelectorAll("tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(q)
                ? ""
                : "none";
        });
    });
});

// ---------- CANCEL BUTTON ----------
document.addEventListener("click", (e) => {
    const cancelBtn = e.target.closest("#btncancelEdit-faculty");
    if (!cancelBtn) return;

    const modal = document.getElementById("editFacultyModal");
    if (!modal) return;

    // Clear search
    const searchInput = modal.querySelector("#editFacultySubjectSearch");
    if (searchInput) searchInput.value = "";

    // Show all rows again
    modal.querySelectorAll("#editFacultySubjectTable tr").forEach(row => {
        row.style.display = "";
    });

    modal.classList.add("hidden");
});
