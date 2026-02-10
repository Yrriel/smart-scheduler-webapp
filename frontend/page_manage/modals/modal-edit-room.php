<?php
//edit room
?>
        <div class="modal-backdrop hidden" id="editRoomModal">
            <div class="modal">
                <div class="modal-header">
                    <h2>Edit Room</h2>
                    <p>Modify the details for this Room</p>
                </div>

                <form method="post" action="../../backend/functions/update_room.php" id="editRoomForm">
                    <input type="hidden" name="id" id="edit_room_id">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Room Name</label>
                            <input type="text" name="room_name" id="edit_room_name">
                        </div>
                        <div class="form-group">
                            <label>Room Capacity</label>
                            <input type="text" name="room_capacity" id="edit_room_capacity">
                        </div>
                    </div>
                    <div class="button-group">
                        <button type="button" id="btncancelEdit-room" class="btn-cancel">Cancel</button>
                        <button type="submit" class="btn-save">Update Room</button>
                    </div>
                </form>
            </div>
        </div>