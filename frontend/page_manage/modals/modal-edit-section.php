<?php
//edit section
?>
        <div class="modal-backdrop hidden" id="editSectionModal">
            <div class="modal">
                <div class="modal-header">
                    <h2>Edit Subject</h2>
                    <p>Modify the details for this subject</p>
                </div>

                <form method="post" action="../../backend/functions/update_section.php" id="editSubjectForm">
                    <input type="hidden" name="id" id="edit_section_id">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Section Name</label>
                            <input type="text" name="section_name" id="edit_section_name">
                        </div>
                        <div class="form-group">
                            <label>Year level</label>
                            <select name="section_year" required>
        
                                <option value="1">First Year</option>
                                <option value="2">Second Year</option>
                                <option value="3">Third Year</option>
                                <option value="4">Fourth Year</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Total Students</label>
                            <input type="text" name="total_students" id="edit_section_students">
                        </div>
                        <div class="form-group">
                            <label>Section Course</label>
                            <select name="section_course" id="edit_section_course">
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
                        <button type="button" id="btncancelEdit-section" class="btn-cancel">Cancel</button>
                        <button type="submit" class="btn-save">Update Subject</button>
                    </div>
                </form>
            </div>
        </div>
