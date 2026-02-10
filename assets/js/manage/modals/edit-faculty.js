// assets/js/manage/modals/edit-faculty.js

// ---------- OPEN EDIT FACULTY MODAL ----------
document.addEventListener("click", (e) => {
    const btn = e.target.closest(".edit-btn-faculty");
    if (!btn) return;

    const modal = document.getElementById("editFacultyModal");
    if (!modal) return;

    // ---------- BASIC FIELDS ----------
    modal.querySelector("#edit_faculty_id").value = btn.dataset.id || "";
    modal.querySelector("#edit_faculty_name").value = btn.dataset.name || "";
    modal.querySelector("#edit_total_hours").value = btn.dataset.hours || "";

    // ---------- DROPDOWNS ----------
    const employmentSelect = modal.querySelector("select[name='employment_type']");
    const statusSelect = modal.querySelector("select[name='current_status']");

    if (employmentSelect) {
        employmentSelect.value = btn.dataset.employment || "Full-Time";
    }

    if (statusSelect) {
        statusSelect.value = btn.dataset.status || "Active";
    }

    // ---------- SUBJECT CHECKBOXES ----------
    const selectedSubjects = (btn.dataset.subjects || "")
        .split(",")
        .map(s => s.trim())
        .filter(Boolean);

    modal.querySelectorAll("input[name='subjects[]']").forEach(cb => {
        cb.checked = selectedSubjects.includes(cb.value);
    });

    // ---------- PREFERRED DAYS ----------
    const selectedDays = (btn.dataset.days || "").split(",");
    modal.querySelectorAll("input[name='preferred_day[]']").forEach(cb => {
        cb.checked = selectedDays.includes(cb.value);
    });

    // ---------- RESET SEARCH ----------
    const searchInput = modal.querySelector("#editFacultySubjectSearch");
    if (searchInput) searchInput.value = "";

    // Show all subject rows
    modal.querySelectorAll("#editFacultySubjectTable tr").forEach(row => {
        row.style.display = "";
    });

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

    // Reset table visibility
    modal.querySelectorAll("#editFacultySubjectTable tr").forEach(row => {
        row.style.display = "";
    });

    modal.classList.add("hidden");
});
