// assets/js/manage/modals/edit-section.js

document.addEventListener("click", (e) => {
    const btn = e.target.closest(".edit-btn-section");
    if (!btn) return;

    const modal = document.getElementById("editSectionModal");
    if (!modal) return;

    modal.querySelector("#edit_section_id").value = btn.dataset.id || "";
    modal.querySelector("#edit_section_name").value = btn.dataset.section_name || "";
    modal.querySelector("#edit_section_students").value = btn.dataset.total_students || "";
    modal.querySelector("#edit_section_course").value = btn.dataset.course || "";
    modal.querySelector("#edit_section_year").value = btn.dataset.year || "";

    modal.classList.remove("hidden");
});
