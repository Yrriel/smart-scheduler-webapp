// assets/js/manage/modals/edit-course.js

document.addEventListener("click", (e) => {
    const btn = e.target.closest(".edit-btn-course");
    if (!btn) return;

    const modal = document.getElementById("editCourseModal");
    if (!modal) return;

    modal.querySelector("#edit_course_id").value = btn.dataset.id || "";
    modal.querySelector("#edit_course_name").value = btn.dataset.course || "";
    modal.querySelector("#edit_course_code").value = btn.dataset.course_name || "";
    modal.querySelector("#edit_course_short").value = btn.dataset.short_name || "";
    modal.querySelector("#edit_course_type").value = btn.dataset.type || "";
    modal.querySelector("#edit_course_units").value = btn.dataset.units || "";
    modal.querySelector("#edit_course_hours").value = btn.dataset.hours || "";
    modal.querySelector("#edit_course_status").value = btn.dataset.status || "";

    modal.classList.remove("hidden");
});
