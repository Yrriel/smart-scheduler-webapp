// assets/js/manage/modals/edit-subject.js

document.addEventListener("click", (e) => {
    const btn = e.target.closest(".edit-btn-subject");
    if (!btn) return;

    const modal = document.getElementById("editSubjectModal");
    if (!modal) return;

    modal.querySelector("#edit_id").value = btn.dataset.id || "";
    modal.querySelector("#edit_subject_code").value = btn.dataset.subject || "";
    modal.querySelector("#edit_subject_name").value = btn.dataset.name || "";
    modal.querySelector("#edit_short_name").value = btn.dataset.short || "";
    modal.querySelector("#edit_type").value = btn.dataset.type || "";
    modal.querySelector("#edit_units").value = btn.dataset.units || "";
    modal.querySelector("#edit_hours").value = btn.dataset.hours || "";
    modal.querySelector("#edit_status").value = btn.dataset.status || "";

    modal.classList.remove("hidden");
});
