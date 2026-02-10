<?php
//edit faculty
?>

<!-- FACULTYYYY FINAL -->
        <div class="modal-backdrop hidden" id="editFacultyModal">
            <div class="modal">
                <div class="modal-header">
                    <h2>Edit Faculty</h2>
                    <p>Modify the details for this faculty member</p>
                </div>

                <form method="post" action="update_faculty.php" id="editFacultyForm">
                    <input type="hidden" name="id" id="edit_faculty_id">

                    <div class="form-group">
                        <label>Faculty Name</label>
                        <input type="text" name="faculty_name" id="edit_faculty_name">
                    </div>
                    <!-- Subjects Multi-select -->
                    <!-- Subjects (table-based selection) -->
                    <div class="form-group">
                        <label>Subjects</label>

                        <input
                            type="text"
                            id="editFacultySubjectSearch"
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
                                <tbody id="editFacultySubjectTable">
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

                   

                    <div class="form-row">
                        <div class="form-group">
                            <label>Employment Type</label>
                            <input type="text" name="employment_type" id="edit_employment_type">
                        </div>

                        <div class="form-group">
                            <label>Current Status</label>
                            <input type="text" name="current_status" id="edit_current_status">
                        </div>
                    </div>

                    <!-- Preferred Days -->
                    <div class="form-group">
                        <label>Preferred Days</label>
                        <div class="day-checkbox-container" id="edit_dayContainer">
                            <label><input type="checkbox" name="preferred_day[]" value="Monday"> Monday</label>
                            <label><input type="checkbox" name="preferred_day[]" value="Tuesday"> Tuesday</label>
                            <label><input type="checkbox" name="preferred_day[]" value="Wednesday"> Wednesday</label>
                            <label><input type="checkbox" name="preferred_day[]" value="Thursday"> Thursday</label>
                            <label><input type="checkbox" name="preferred_day[]" value="Friday"> Friday</label>
                            <label><input type="checkbox" name="preferred_day[]" value="Saturday"> Saturday</label>
                        </div>
                    </div>

                    

                    <div class="form-row">
                        <div class="form-group">
                            <label>Hours per week</label>
                            <input type="text" name="total_hours_per_week" id="edit_total_hours">
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="button" id="btncancelEdit-faculty" class="btn-cancel">Cancel</button>
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
