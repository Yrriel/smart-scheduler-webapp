// assets/js/manage/modals/add-section.js

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("addSectionModal");
    if (!modal) return;

    modal.querySelector("form")?.addEventListener("submit", () => {
        console.log("Add Section submitted");
    });
});
