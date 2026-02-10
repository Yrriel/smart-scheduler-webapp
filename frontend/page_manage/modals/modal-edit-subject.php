<?php
//edit subject
?>
        <div class="modal-backdrop hidden" id="editSubjectModal">
            <div class="modal">
                <div class="modal-header">
                    <h2>Edit Subject</h2>
                    <p>Modify the details for this subject</p>
                </div>

                <form method="post" action="../../backend/functions/update_subject.php" id="editSubjectForm">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="form-group">
                        <label>Subject Name</label>
                        <input type="text" name="subject_name" id="edit_subject_name">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Subject Code</label>
                            <input type="text" name="subject_code" id="edit_subject_code">
                        </div>
                        <div class="form-group">
                            <label>Short Name</label>
                            <input type="text" name="short_name" id="edit_short_name">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Units</label>
                            <input type="text" name="units" id="edit_units">
                        </div>
                        <div class="form-group">
                            <label>Subject Type</label>
                            <select name="type" id="edit_type">
                                <option value="Lecture">Lecture</option>
                                <option value="Laboratory">Laboratory</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Hours per Week</label>
                            <input type="text" name="hours_per_week" id="edit_hours">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="edit_status">
                                <option>Active</option>
                                <option>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="button" id="btncancelEdit" class="btn-cancel">Cancel</button>
                        <button type="submit" class="btn-save">Update Subject</button>
                    </div>
                </form>
            </div>
        </div>