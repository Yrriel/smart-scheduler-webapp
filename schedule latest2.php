<?php
include "connection.php";

/* =======================
   FILTERS
======================= */
$course_filter  = $_GET['course']  ?? '';
$section_filter = $_GET['section'] ?? '';

$where = "WHERE 1=1";
$params = [];
$types = "";

if ($course_filter !== '') {
    $where .= " AND section LIKE ?";
    $params[] = "%$course_filter%";
    $types .= "s";
}

if ($section_filter !== '') {
    $where .= " AND section = ?";
    $params[] = $section_filter;
    $types .= "s";
}

/* =======================
   FETCH SCHEDULE LIST
======================= */
$sql = "
    SELECT DISTINCT section, faculty
    FROM generated_schedule
    $where
    ORDER BY section ASC
";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$schedule_rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* =======================
   DROPDOWNS
======================= */
$courses = $conn->query("SELECT DISTINCT course_name FROM manage_courses ORDER BY course_name ASC");
$sections = $conn->query("SELECT DISTINCT section_name FROM manage_sections ORDER BY section_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule | SmartSched</title>
    <link rel="stylesheet" href="styleadmin.css">
    <link rel="shortcut icon" href="src/logo.png">
</head>
<body>

<!-- =======================
     NAV (COPIED FROM MANAGE)
======================= -->
<nav>
    <div class="nav-listdown">
        <div class="logo-container">
            <span class="img-container">
                <img src="src/logo.png" alt="">
            </span>
            <p>SmartSched</p>
        </div>

        <a href="dashboard.html">
            <div class="nav-list nav-dashboard">
                <p>Dashboard</p>
            </div>
        </a>

        <a href="schedule.php">
            <div class="nav-list nav-schedule active">
                <p>Schedule</p>
            </div>
        </a>

        <a href="manage.php">
            <div class="nav-list nav-schedule">
                <p>Manage</p>
            </div>
        </a>
    </div>

    <div class="nav-listdown-below">
        <a href="#">
            <div class="nav-list nav-schedule">
                <p>Account Settings</p>
            </div>
        </a>
        <a href="backend_logout.php">
            <div class="nav-list nav-schedule">
                <p>Log Out</p>
            </div>
        </a>
    </div>
</nav>

<!-- =======================
     MAIN CONTENT
======================= -->
<section>

<div id="schedule-tab" class="table-container">

    <div class="content-header">
        <div class="header-text">
            <h2>Schedule Management</h2>
            <p>Generate and manage schedules</p>
        </div>

        <form method="post" action="generate_schedule.php">
            <button class="add-btn">
                <span>⚙</span>
                Generate Schedule
            </button>
        </form>
    </div>

    <div class="table-header">
        <div class="table-title">All Schedules</div>

        <div class="filters">
            <form method="get" style="display:flex; gap:10px;">
                <select class="filter-select" name="course">
                    <option value="">Select Course</option>
                    <?php while ($c = $courses->fetch_assoc()): ?>
                        <option value="<?= $c['course_name'] ?>" <?= $course_filter === $c['course_name'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['course_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <select class="filter-select" name="section">
                    <option value="">Select Section</option>
                    <?php while ($s = $sections->fetch_assoc()): ?>
                        <option value="<?= $s['section_name'] ?>" <?= $section_filter === $s['section_name'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['section_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit" class="btn-save">Filter</button>
            </form>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Section</th>
                <th>Faculty</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($schedule_rows): ?>
            <?php foreach ($schedule_rows as $row): ?>
                <tr class="schedule-row"
                    data-section="<?= htmlspecialchars($row['section']) ?>">
                    <td><?= htmlspecialchars($row['section']) ?></td>
                    <td><?= htmlspecialchars($row['faculty']) ?></td>
                    <td>
                        <button class="edit-btn">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No schedules found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

</div>

</section>

<script>
document.querySelectorAll(".schedule-row").forEach(row => {
    row.addEventListener("click", () => {
        const section = row.dataset.section;
        window.location.href = "schedule-show.php?section=" + encodeURIComponent(section);
    });
});
</script>

</body>
</html>
