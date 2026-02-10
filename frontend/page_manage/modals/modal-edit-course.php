<?php
//edit course
?>
        <div class="modal-backdrop hidden" id="editCourseModal">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal">
                <div class="modal-header">
                    <h2>Edit Course</h2>
                    <p>Modify the details for this subject</p>
                </div>

                    <form method="post" action="../../backend/functions/update_course.php" id="editCourseForm">
                        <input type="hidden" name="id" id="edit_course_id">

                        <div class="form-group">
                            <label>Course Name</label>
                            <input type="text" name="course" id="edit_course_name" placeholder="e.g., Bachelor of Science in Computer Science">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Course Acronyms</label>
                                <input type="text" name="course_name" id="edit_course_code" placeholder="e.g., BSCS">
                            </div>
                            <div class="form-group">
                                <label>Short Name</label>
                                <input type="text" name="short_name" id="edit_course_short" placeholder="e.g., COMSCI">
                            </div>
                        </div>

                        <!-- <div class="form-row">
                            <div class="form-group">
                                <label>Units</label>
                                <input type="text" name="units" id="edit_course_units" placeholder="e.g.,3">
                            </div>
                            <div class="form-group">
                                <label>Hours per Week</label>
                                <input type="text" name="hours" id="edit_course_hours" placeholder="e.g.,3 Hours">
                            </div>
                        </div> -->

                        <div class="form-row">
                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" id="edit_course_type">
                 
                                    <option>SHS</option>
                                    <option>College</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="edit_course_status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="button" id="btncancelEdit-course" class="btn-cancel">Cancel</button>
                            <button type="submit" class="btn-save">Save Changes</button>
                        </div>
                    </form>                         
            </div>
        </div>
