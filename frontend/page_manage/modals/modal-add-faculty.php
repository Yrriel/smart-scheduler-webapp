<?php
//add faculty
?>

    <div class="modal-backdrop hidden" id="addFacultyModal">
        <div class="modal">
        <div class="modal-header">
            <h2>Add New Faculty</h2>
            <p>Input the details for the new Faculty</p>
        </div>

        

        <form method="post" action="../../backend/functions/add_faculty.php">
            
            
            <div class="form-row">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="faculty_name" placeholder="e.g., Juan Dela Cruz">
                </div>
                    <div class="form-group">
                    <label>Hours per Week</label>
                    <input type="text" name="total_hours_per_week" placeholder="e.g.,3 Hours">
                </div>  
                </div>
                <div class="form-row">
                <div class="form-group">
                    <label>Employment Type</label>
                    <select name="employment_type">
                        <option>Part-time</option>
                        <option>Full-time</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="current_status">
                        <option>Select status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                    
                </div>
            </div>
            <!-- Subjects (table-based selection) -->
            <div class="form-group">
                <label>Subjects</label>

                <input
                    type="text"
                    id="addFacultySubjectSearch"
                    placeholder="Search subject..."
                    class="subject-search"
                >

                <div class="subject-table-wrapper" style="max-height:200px; overflow-y:auto;">
                    <table class="subject-select-table">
                        <thead>
                            <tr>
                                <th style="width:40px;"></th>
                                <th>Code</th>
                                <th>Subject Name</th>
                                <th>Units</th>
                            </tr>
                        </thead>
                        <tbody id="addFacultySubjectTable">
                            <?php
                            $subSql = "SELECT subject, subject_name, units FROM manage_subjects ORDER BY subject_name ASC";
                            $subRes = $conn->query($subSql);

                            if ($subRes && $subRes->num_rows > 0):
                                while ($sub = $subRes->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        name="subjects[]"
                                        value="<?= htmlspecialchars($sub['subject']) ?>"
                                    >
                                </td>
                                <td><?= htmlspecialchars($sub['subject']) ?></td>
                                <td><?= htmlspecialchars($sub['subject_name']) ?></td>
                                <td><?= htmlspecialchars($sub['units']) ?></td>
                            </tr>
                            <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- <div class="form-group search-wrapper">
                    <label>Subjects</label>
                    <input type="text" id="subjectSearch" placeholder="Search subject…">
                    <div id="selectedSubjects" class="selected-items"></div>
                    <input type="hidden" name="subjects" id="subjectsHidden">
                    <div class="suggestions" id="subjectSuggestions"></div>
            </div> -->


            <div class="form-group">
                <label>Preferred Days</label>

                <div class="day-pill-container" id="edit_dayContainer">
                    <label class="day-pill">
                    <input type="checkbox" name="preferred_day[]" value="Monday">
                    <span>Monday</span>
                    </label>

                    <label class="day-pill">
                    <input type="checkbox" name="preferred_day[]" value="Tuesday">
                    <span>Tuesday</span>
                    </label>

                    <label class="day-pill">
                    <input type="checkbox" name="preferred_day[]" value="Wednesday">
                    <span>Wednesday</span>
                    </label>

                    <label class="day-pill">
                    <input type="checkbox" name="preferred_day[]" value="Thursday">
                    <span>Thursday</span>
                    </label>

                    <label class="day-pill">
                    <input type="checkbox" name="preferred_day[]" value="Friday">
                    <span>Friday</span>
                    </label>

                    <label class="day-pill">
                    <input type="checkbox" name="preferred_day[]" value="Saturday">
                    <span>Saturday</span>
                    </label>
                </div>
                </div>
                
                <!-- <div class="form-group">
                    <label>Preferred Day</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="preferred_day[]" value="Monday"> Monday</label>
                        <label><input type="checkbox" name="preferred_day[]" value="Tuesday"> Tuesday</label>
                        <label><input type="checkbox" name="preferred_day[]" value="Wednesday"> Wednesday</label>
                        <label><input type="checkbox" name="preferred_day[]" value="Thursday"> Thursday</label>
                        <label><input type="checkbox" name="preferred_day[]" value="Friday"> Friday</label>
                        <label><input type="checkbox" name="preferred_day[]" value="Saturday"> Saturday</label>
                    </div>
                </div> -->
            
                  
           

            <div class="button-group">
                <button type="button" id="btncancel-faculty" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Faculty</button>
            </div>
        </form>

        </div>
    </div>