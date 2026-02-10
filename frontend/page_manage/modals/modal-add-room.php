<?php
//add room
?>


    <div class="modal-backdrop hidden" id="addRoomModal">
        <div class="modal">
        <div class="modal-header">
            <h2>Add New Room</h2>
            <p>Input the details for the new room</p>
        </div>

        <form method="post" action="../../backend/functions/add_room.php">
            <div class="form-row">
                <div class="form-group">
                    <label>Room name</label>
                    <input type="text" name="room_name" placeholder="e.g.,201, 301">
                </div>
                <div class="form-group">
                    <label>Room capacity</label>
                    <input type="text" name="room_capacity" placeholder="e.g.,30">
                </div>
            </div>


            <div class="button-group">
                <button type="button" id="btncancel-room" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Room</button>
            </div>
        </form>

        </div>
    </div>