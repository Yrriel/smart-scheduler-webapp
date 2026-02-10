<?php
//assign course subject

?>

<div class="modal-backdrop hidden" id="assignCourseSubjectModal">
    <div class="modal">
        <div class="modal-header">
            <h2>Assign Course Subject</h2>
            <p>Assign or Modify the details for course subject</p>
        </div>

        <form method="post" action="../../backend/functions/assign_course_subjects.php" id="editRoomForm">
            <input type="hidden" name="id" id="edit_room_id">

            <!-- Course -->
            <div class="form-group">
                <label>Course</label>
                <select name="section_course" id="assignSectionCourse" required>
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

            <!-- Year Level -->
            <div class="form-group">
                <label>Year Level</label>
                <select name="year_level" id="assignYearLevel" required>
                    <option value="">Select Year</option>
                    <option value="1">First Year</option>
                    <option value="2">Second Year</option>
                    <option value="3">Third Year</option>
                    <option value="4">Fourth Year</option>
                </select>
            </div>

            <!-- Subject Search -->
            <div class="form-group">
                <label>Subjects</label>
                <input 
                    type="text" 
                    id="subjectSearchAssign" 
                    placeholder="Search subject..."
                >
            </div>

            <!-- Scrollable Subject Table -->
            <div class="form-group">
                <div class="subject-table-wrapper">
                    <table class="subject-select-table">
                        <thead>
                            <tr>
                                <th style="width:40px;"></th>
                                <th>Code</th>
                                <th>Subject Name</th>
                                <th>Units</th>
                            </tr>
                        </thead>
                        <tbody id="subjectAssignTable">
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
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="4">No subjects found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Buttons -->
            <div class="button-group">
                <button type="button" id="btncancelAssign-coursesubject" class="btn-cancel">
                    Cancel
                </button>
                <button type="submit" class="btn-save">
                    Update Subject
                </button>
            </div>
        </form>
    </div>
</div>
<script>
(function() {
  const courseSelect = document.getElementById('assignSectionCourse');
  const yearSelect = document.getElementById('assignYearLevel');
  const modalBackdrop = document.getElementById('assignCourseSubjectModal');

  function setCheckboxes(assigned) {
    const boxes = document.querySelectorAll("#subjectAssignTable input[name='subjects[]']");
    const set = new Set(assigned || []);
    boxes.forEach(cb => { cb.checked = set.has(cb.value); });
  }

  function clearCheckboxes() {
    const boxes = document.querySelectorAll("#subjectAssignTable input[name='subjects[]']");
    boxes.forEach(cb => { cb.checked = false; });
  }

  async function loadAssigned() {
    const course = (courseSelect && courseSelect.value || '').trim();
    const year = (yearSelect && yearSelect.value || '').trim();
    if (!course || !year) { clearCheckboxes(); return; }
    try {
      const url = `../../backend/functions/get_assigned_course_subjects.php?section_course=${encodeURIComponent(course)}&year_level=${encodeURIComponent(year)}`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const data = await res.json();
      setCheckboxes(data.assigned_subjects || []);
    } catch (e) {
      console.error('Failed to load assigned subjects', e);
    }
  }

  function whenModalVisibleRun() {
    if (!modalBackdrop) return;
    const visible = !modalBackdrop.classList.contains('hidden');
    if (visible) loadAssigned();
  }

  if (courseSelect) courseSelect.addEventListener('change', loadAssigned);
  if (yearSelect) yearSelect.addEventListener('change', loadAssigned);

  if (modalBackdrop && typeof MutationObserver !== 'undefined') {
    const obs = new MutationObserver(muts => {
      for (const m of muts) {
        if (m.attributeName === 'class') whenModalVisibleRun();
      }
    });
    obs.observe(modalBackdrop, { attributes: true, attributeFilter: ['class'] });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => { whenModalVisibleRun(); });
  } else {
    whenModalVisibleRun();
  }
})();
</script>
