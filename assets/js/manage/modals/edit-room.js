// assets/js/manage/modals/edit-room.js

document.addEventListener("click", (e) => {
    const btn = e.target.closest(".edit-btn-room");
    if (!btn) return;

    const modal = document.getElementById("editRoomModal");
    if (!modal) return;

    modal.querySelector("#edit_room_id").value = btn.dataset.id_room || "";
    modal.querySelector("#edit_room_name").value = btn.dataset.name_room || "";
    modal.querySelector("#edit_room_capacity").value = btn.dataset.capacity_room || "";

    modal.classList.remove("hidden");
});
