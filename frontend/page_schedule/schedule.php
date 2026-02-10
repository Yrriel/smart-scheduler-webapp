<?php
include __DIR__ . '/../../backend/connection/connection.php';

// Fetch courses (course_name)
$coursesResult = $conn->query("SELECT DISTINCT course_name FROM manage_courses ORDER BY course_name ASC");

// Fetch sections
$sectionsResult = $conn->query("SELECT section_name FROM manage_sections ORDER BY section_name ASC");

// Build timeslots (07:00 AM – 05:30 PM, every 30 mins)
$timeSlots = [];
$start = strtotime('07:00');
$end   = strtotime('17:30');
while ($start < $end) {
    $next = $start + 30 * 60;
    $timeSlots[] = date('g:i A', $start) . ' - ' . date('g:i A', $next);
    $start = $next;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styleadmin.css">
    <link rel="shortcut icon" href="../../src/logo.png">
    <title>SmartSched</title>
   
   
   <style>.day-cell {
    padding: 0 !important;
    vertical-align: top;
}

/* rowspan cells must allow absolute children */
td[rowspan] {
    position: relative;
    padding: 0 !important;
}

/* The wrapper MUST be absolutely positioned */
.merged-wrapper {
    position: absolute; 
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;

    background: #4a6cf7;
    color: white;

    padding: 6px;
    box-sizing: border-box;
    border-radius: 6px;

    width: 100%;
    height: 100%;
}

/* Text box stays inside wrapper */
.class-box {
    position: relative;
    z-index: 2;
    font-size: 12px;
    line-height: 1.3em;
}

.room-label {
    font-size: 10px;
    opacity: 0.9;
}

</style>




</head>
<body>
    <nav>


        <div class="nav-listdown">
            <div class="logo-container">
                <span class="img-container"><img src="../../src/logo.png" alt="" srcset=""></span>
                <p>SmartSched</p>
            </div>
        <a href="../page_dashboard/dashboard.php">
            <div class="nav-list nav-dashboard active"><p>Dashboard</p></div>
        </a>

        <a href="../page_schedule/schedule.php">
            <div class="nav-list nav-schedule"><p>Schedule</p></div>
        </a>

        <a href="../page_manage/manage.php">
            <div class="nav-list nav-schedule"><p>Manage</p></div>
        </a>
        </div>

        <div class="nav-listdown-below">
            <a href="">
                <div class="nav-list nav-schedule">
                <p>Account Settings</p>
            </div>
            </a>
            <a href="../../backend/login/backend_logout.php">
                <div class="nav-list nav-schedule">
                <p>Log Out</p>
            </div>
            </a>
        </div>
    </nav>

    <section class="sectionschedule">
    <div id="schedule-tab" class="table-container">
        <!-- Header (similar style to Subject Management) -->
        <div class="content-header">
            <div class="header-text">
                <h2>Schedule Management</h2>
                <p>Generate and view class schedules</p>
            </div>
            <form method="post" action="../../backend/functions/generate_schedule.php">
                <button type="submit" class="add-btn">
                    <span>⚙</span> Generate Schedule
                </button>
            </form>

            
        </div>

        <!-- Table header bar with filters (course + section + search) -->
        <div class="table-header">
            <div class="table-title">Weekly Tmetable</div>
            <div class="filters">

                <!-- Course dropdown -->
                <select class="filter-select" id="schedule-course">
                    <option value="">Select Course</option>
                    <?php if ($coursesResult && $coursesResult->num_rows > 0): ?>
                        <?php while ($c = $coursesResult->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($c['course_name']) ?>">
                                <?= htmlspecialchars($c['course_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>

                <!-- Section dropdown -->
                <select class="filter-select" id="schedule-section">
                    <option value="">Select Section</option>
                    <?php if ($sectionsResult && $sectionsResult->num_rows > 0): ?>
                        <?php while ($s = $sectionsResult->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($s['section_name']) ?>">
                                <?= htmlspecialchars($s['section_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>

                <!-- Optional search box (for future filtering) -->
                <form class="search-box" onsubmit="return false;">
                    <input 
                        type="text" 
                        id="schedule-search-input"
                        placeholder="Search in schedule"
                    >
                </form>
            </div>
        </div>

        <!-- Timetable grid -->
        <div class="schedule-table-wrapper">
            <table class="schedule-grid">
                <thead>
                    <tr>
                        <th class="time-header">Time</th>
                        <th>mON</th>
                        <th>TUE</th>
                        <th>WED</th>
                        <th>THU</th>
                        <th>FRI</th>
                        <th>SAT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($timeSlots as $slot): ?>
                        <tr>
                            <td class="time-cell"><?= htmlspecialchars($slot) ?></td>
                            <td class="day-cell" data-day="Monday"></td>
                            <td class="day-cell" data-day="Tuesday"></td>
                            <td class="day-cell" data-day="Wednesday"></td>
                            <td class="day-cell" data-day="Thursday"></td>
                            <td class="day-cell" data-day="Friday"></td>
                            <td class="day-cell" data-day="Saturday"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>


<!-- <script>
document.getElementById('btn-generate-schedule').addEventListener('click', () => {
    alert('This is where AI schedule generation / loading from DB will happen.');
});
</script> -->

<?php
// Fetch the generated schedule
$scheduleQuery = $conn->query("SELECT * FROM generated_schedule ORDER BY day, time_start");
$scheduleData = [];
while ($row = $scheduleQuery->fetch_assoc()) {
    $scheduleData[] = $row;
}
?>
<script>
const schedule = <?= json_encode($scheduleData) ?>;

document.addEventListener("DOMContentLoaded", () => {
    const rows = document.querySelectorAll("tbody tr");

    schedule.forEach(entry => {
        const day = entry.day;
        const startMin = toMinutes(entry.time_start);
        const endMin   = toMinutes(entry.time_end);

        let matchedCells = [];

        // collect all cells touched by the class time range
        rows.forEach(row => {
            const timeRange = row.querySelector(".time-cell").textContent.trim();
            const [slotStart, slotEnd] = timeRange.split(" - ").map(t => toMinutes(t));

            // Overlap detection
            if (startMin < slotEnd && endMin > slotStart) {
                const cell = row.querySelector(`.day-cell[data-day="${day}"]`);
                if (cell) matchedCells.push(cell);
            }
        });

        if (matchedCells.length > 0) {
            const firstCell = matchedCells[0];

            // Insert the class content ONLY in the top cell
            firstCell.innerHTML = `
                <div class="merged-wrapper">
                    <div class="class-box">
                        <strong>${entry.subject}</strong><br>
                        ${entry.subject_name}<br>
                        ${entry.faculty}<br>
                        <span class="room-label">${entry.room}</span>
                    </div>
                </div>
            `;

            // rowspan merge
            firstCell.setAttribute("rowspan", matchedCells.length);

            // REMOVE the lower cells completely (fixes extra column bug)
            for (let i = 1; i < matchedCells.length; i++) {
                matchedCells[i].remove();
            }
        }
    });
});

// Helpers
function toMinutes(time) {
    time = time.trim().toUpperCase();
    if (time.includes("AM") || time.includes("PM")) return convertTo24Hour(time);
    let [h, m] = time.split(":").map(Number);
    return h * 60 + m;
}

function convertTo24Hour(t) {
    let [time, mer] = t.split(" ");
    let [h, m] = time.split(":").map(Number);
    if (mer === "PM" && h !== 12) h += 12;
    if (mer === "AM" && h === 12) h = 0;
    return h * 60 + m;
}
</script>


</body>
</html>
