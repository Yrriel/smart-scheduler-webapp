<?php
//add course
?>
    <div class="modal-backdrop hidden" id="addCourseModal">
        <div class="modal">
        <div class="modal-header">
            <h2>Add New Course</h2>
            <p>Input the details for the new course</p>
        </div>

        <form method="post" action="../../backend/functions/add_course.php">
            <div class="form-group">
                <label>Course Name</label>
                <input type="text" name="course" placeholder="e.g., Bachelor of Science in Computer Science">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Course Acronyms</label>
                    <input type="text" name="course_name" placeholder="e.g.,BSCS">
                </div>
                <div class="form-group">
                    <label>Short Name</label>
                    <input type="text" name="short_name" placeholder="e.g., Cal 1">
                </div>
            </div>


            <div class="form-row">
                <div class="form-group">
                    <label>Type</label>
                    <select name="type">
                 
                        <option>SHS</option>
                        <option>College</option>
                    </select>
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
                <button type="button" id="btncancel-course" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Course</button>
            </div>
        </form>

        </div>
    </div>