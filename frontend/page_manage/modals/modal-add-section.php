<?php
//add section
?>

    <div class="modal-backdrop hidden" id="addSectionModal">
        <div class="modal">
        <div class="modal-header">
            <h2>Add New Section</h2>
            <p>Input the details for the new section</p>
        </div>

        <form method="post" action="../../backend/functions/add_section.php">
            <div class="form-row">
                <div class="form-group">
                    <label>Section Name</label>
                    <input type="text" name="section_name" placeholder="e.g.,CS41A">
                </div>
                <div class="form-group">
                    <label>Year level</label>
                    <!-- <input type="text" name="section_year" placeholder="e.g.,3"> -->
                     <select name="section_year" required>
        
                                <option value="1">First Year</option>
                                <option value="2">Second Year</option>
                                <option value="3">Third Year</option>
                                <option value="4">Fourth Year</option>
                            </select>
                </div>
            </div>

            <div class="form-row">
                <input type="text" hidden>
                <div class="form-group">
                    <label>Total Students</label>
                    <input type="text" name="total_students" placeholder="e.g.,24">
                </div>
                <div class="form-group">
                <label>Section Course</label>
                <select name="section_course">
                    <option value="">Select one...</option>

                    <?php
                    include "connection.php";

                    $sql = "SELECT course_name FROM manage_courses ORDER BY course_name ASC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['course_name']) . '">' . 
                                    htmlspecialchars($row['course_name']) . 
                                '</option>';
                        }
                    }
                    ?>
                </select>


                </div>
            </div>


            <div class="button-group">
                <button type="button" id="btncancel-section" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Section</button>
            </div>
        </form>

        </div>
    </div>