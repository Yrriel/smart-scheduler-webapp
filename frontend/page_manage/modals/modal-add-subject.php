<?php
//add subject
?>

    <div class="modal-backdrop hidden" id="addSubjectModal">
        <div class="modal">
        <div class="modal-header">
            <h2>Add New Subject</h2>
            <p>Input the details for the new subject</p>
        </div>

        <form method="post" action="../../backend/functions/add_subject.php">
            <div class="form-group">
                <label>Subject Name</label>
                <input type="text" name="subject_name" placeholder="e.g., Calculus 1">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Subject Code</label>
                    <input type="text" name="subject_code" placeholder="e.g.,MATH201">
                </div>
                <div class="form-group">
                    <label>Short Name</label>
                    <input type="text" name="short_name" placeholder="e.g., Cal 1">
                </div>
            </div>

            <!-- <div class="form-row">
                <div class="form-group">
                    <label>SchoolYear</label>
                    <select name="school_year">
                        <option>Select school year</option>
                        <option>2023-2024</option>
                        <option>2024-2025</option>
                        <option>2025-2026</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Semester</label>
                    <select name="semester">
                        <option>Select semester</option>
                        <option>1st Semester</option>
                        <option>2nd Semester</option>
                    </select>
                </div>
            </div> -->

            <div class="form-row">
                <div class="form-group">
                    <label>Units</label>
                    <input type="text" name="units" placeholder="e.g.,3">
                </div>
                <div class="form-group">
                    <label>Subject Type</label>
                    <select  name="type">
                        <option value="Lecture">Lecture</option>
                        <option value="Laboratory">Laboratory</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Hours per Week</label>
                    <input type="text" value="3" name="hours_per_week" placeholder="e.g.,3 Hours">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="button-group">
                <button type="button" id="btncancel" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Subject</button>
            </div>
        </form>

        </div>
    </div>