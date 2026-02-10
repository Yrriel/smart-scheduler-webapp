// assets/js/manage/modals/add-room.js

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("addRoomModal");
    if (!modal) return;

    modal.querySelector("form")?.addEventListener("submit", () => {
        console.log("Add Room submitted");
    });
});
