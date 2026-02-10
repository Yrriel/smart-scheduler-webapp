document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("addFacultyModal");
    if (!modal) return;

    const searchInput = modal.querySelector("#addFacultySubjectSearch");
    const tableBody  = modal.querySelector("#addFacultySubjectTable");

    /* =========================
       SUBJECT SEARCH FILTER
    ========================= */
    if (searchInput && tableBody) {
        searchInput.addEventListener("input", () => {
            const q = searchInput.value.toLowerCase();

            tableBody.querySelectorAll("tr").forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(q)
                    ? ""
                    : "none";
            });
        });
    }

    /* =========================
       CANCEL BUTTON
    ========================= */
    document.getElementById("btncancel-faculty")
        ?.addEventListener("click", () => {
            // Clear inputs
            modal.querySelector("form").reset();

            // Uncheck subjects
            modal.querySelectorAll("input[name='subjects[]']").forEach(cb => {
                cb.checked = false;
            });

            // Reset search + rows
            if (searchInput) searchInput.value = "";
            tableBody.querySelectorAll("tr").forEach(r => r.style.display = "");

            modal.classList.add("hidden");
        });
});
