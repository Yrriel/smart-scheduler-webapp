// assets/js/manage/modals/add-course.js

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("addCourseModal");
    if (!modal) return;

    modal.querySelector("form")?.addEventListener("submit", () => {
        console.log("Add Course submitted");
    });
});
