<?php
include __DIR__ . '/../../backend/connection/connection.php';

/* =======================
   BASIC COUNTS
======================= */
$activeSubjects   = $conn->query("SELECT COUNT(*) c FROM manage_subjects WHERE status='Active'")->fetch_assoc()['c'] ?? 0;
$totalFaculty     = $conn->query("SELECT COUNT(*) c FROM manage_faculty")->fetch_assoc()['c'] ?? 0;
$totalSections    = $conn->query("SELECT SUM(total_students) s FROM manage_sections")->fetch_assoc()['s'] ?? 0;
$totalRooms       = $conn->query("SELECT COUNT(*) c FROM manage_rooms")->fetch_assoc()['c'] ?? 0;

$lectureCount = $conn->query("SELECT COUNT(*) c FROM manage_subjects WHERE type='Lecture'")->fetch_assoc()['c'] ?? 0;
$labCount     = $conn->query("SELECT COUNT(*) c FROM manage_subjects WHERE type='Laboratory'")->fetch_assoc()['c'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard | SmartSched</title>
<link rel="stylesheet" href="../../styleadmin.css">
<link rel="shortcut icon" href="../../src/logo.png">
</head>

<body>

<!-- =======================
     SIDEBAR (UNCHANGED)
======================= -->
<nav>
    <div class="nav-listdown">
        <div class="logo-container">
            <span class="img-container"><img src="../../src/logo.png"></span>
            <p>SmartSched</p>
        </div>

        <a href="../page_dashboard/dashboard.php">
            <div class="nav-list nav-dashboard active"><span class="img-container-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard-icon lucide-layout-dashboard"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
            </span>
                <p>Dashboard</p>
            </div>
        </a>

        <a href="../page_schedule/schedule-ui.php">
            <div class="nav-list nav-schedule"><span class="img-container-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-heart-icon lucide-calendar-heart"><path d="M12.127 22H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5.125"/><path d="M14.62 18.8A2.25 2.25 0 1 1 18 15.836a2.25 2.25 0 1 1 3.38 2.966l-2.626 2.856a.998.998 0 0 1-1.507 0z"/><path d="M16 2v4"/><path d="M3 10h18"/><path d="M8 2v4"/></svg>
            </span>
                <p>Schedule</p></div>
        </a>

        <a href="../page_manage/manage.php?tab=course">
            <div class="nav-list nav-schedule"><span class="img-container-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-kanban-icon lucide-folder-kanban"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/><path d="M8 10v4"/><path d="M12 10v2"/><path d="M16 10v6"/></svg>
            </span>
                <p>Manage</p></div>
        </a>
    </div>

    <div class="nav-listdown-below">
        <a href="#"><div class="nav-list"><span class="img-container-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-cog-icon lucide-user-cog"><path d="M10 15H6a4 4 0 0 0-4 4v2"/><path d="m14.305 16.53.923-.382"/><path d="m15.228 13.852-.923-.383"/><path d="m16.852 12.228-.383-.923"/><path d="m16.852 17.772-.383.924"/><path d="m19.148 12.228.383-.923"/><path d="m19.53 18.696-.382-.924"/><path d="m20.772 13.852.924-.383"/><path d="m20.772 16.148.924.383"/><circle cx="18" cy="15" r="3"/><circle cx="9" cy="7" r="4"/></svg>
        </span>
            <p>User Settings</p></div></a>
        <a href="../../backend/login/backend_logout.php"><div class="nav-list"><span class="img-container-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out-icon lucide-log-out"><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>
        </span><p>Log Out</p></div></a>
    </div>
</nav>

<!-- =======================
     MAIN DASHBOARD
======================= -->
<section class="section-class">
  <div class="container">

    <!-- LEFT COLUMN -->
    <div class="dashboard-main">

      <!-- Header -->
      <div class="content-header">
        <div class="header-text">
          <h1>Dashboard</h1>
        </div>
      </div>

      <!-- SUMMARY CARDS -->
      <div class="dashboard-cards">
        <div class="dashboard-card">
          <p>Active Subjects</p>
          <h2><?= $activeSubjects ?></h2>
        </div>

        <div class="dashboard-card">
          <p>Total Faculty</p>
          <h2><?= $totalFaculty ?></h2>
        </div>

        <div class="dashboard-card">
          <p>Total Students</p>
          <h2><?= $totalSections ?></h2>
        </div>

        <div class="dashboard-card">
          <p>Total Rooms</p>
          <h2><?= $totalRooms ?></h2>
        </div>
      </div>

      <!-- SUBJECT DISTRIBUTION -->
      <div class="dashboard-panel">
        <h3>Subject Distribution</h3>
        <p class="panel-subtitle">Breakdown by subject type</p>

        <div class="panel-row column">
          <span>Laboratory Subjects</span>
          <div class="progress">
            <div class="progress-fill"
                 style="width: <?= ($activeSubjects > 0) ? ($labCount / $activeSubjects) * 100 : 0 ?>%">
            </div>
          </div>
        </div>

        <div class="panel-row column">
          <span>Lecture Subjects</span>
          <div class="progress alt">
            <div class="progress-fill"
                 style="width: <?= ($activeSubjects > 0) ? ($lectureCount / $activeSubjects) * 100 : 0 ?>%">
            </div>
          </div>
        </div>
      </div>

      <!-- FACULTY WORKLOAD -->
      <div class="dashboard-panel">
        <h3>Faculty Workload</h3>
        <p class="panel-subtitle">Estimated average teaching load</p>

        <div class="panel-row">
          <span>Subjects per Faculty</span>
          <span class="badge">
            <?= ($totalFaculty > 0) ? round($activeSubjects / $totalFaculty, 1) : 0 ?>
          </span>
        </div>

        <div class="panel-row">
          <span>Students per Section</span>
          <span class="badge">
            <?= ($totalSections > 0) ? round($totalSections / max(1, $activeSubjects)) : 0 ?>
          </span>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="dashboard-right">

      <!-- SYSTEM STATUS -->
      <div class="dashboard-panel">
        <h3>System Status</h3>

        <div class="panel-row">
          <span>Scheduling Engine</span>
          <span class="status ok">● Operational</span>
        </div>

        <div class="panel-row">
          <span>Data Readiness</span>
          <span class="status ok">● Ready</span>
        </div>

        <div class="panel-row">
          <span>Last Schedule Generated</span>
          <span class="muted">Not yet generated</span>
        </div>
      </div>

      <!-- CAPACITY OVERVIEW -->
      <div class="dashboard-panel">
        <h3>Capacity Overview</h3>

        <div class="panel-row column">
          <span>Faculty Utilization</span>
          <div class="progress">
            <div class="progress-fill"
                 style="width: <?= min(100, ($totalFaculty > 0) ? ($activeSubjects / $totalFaculty) * 10 : 0) ?>%">
            </div>
          </div>
        </div>

        <div class="panel-row column">
          <span>Room Availability</span>
          <div class="progress alt">
            <div class="progress-fill"
                 style="width: <?= min(100, ($totalSections > 0) ? ($totalRooms / $totalSections) * 100 : 0) ?>%">
            </div>
          </div>
        </div>
      </div>

      <!-- NEXT ACTIONS -->
      <div class="dashboard-panel">
        <h3>Next Actions</h3>

        <ul class="action-list">
          <li>Review faculty workload distribution</li>
          <li>Generate preliminary schedule</li>
          <li>Resolve room capacity constraints</li>
        </ul>
      </div>

    </div>

  </div>
</section>

</body>
</html>
