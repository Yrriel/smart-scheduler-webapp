<?php
$tab = $_GET['tab'] ?? 'day';
$view = $_GET['view'] ?? '';
$day  = $_GET['day'] ?? '';


$allowedTabs = ['day', 'faculty', 'section', 'room'];
if (!in_array($tab, $allowedTabs)) {
    $tab = 'day';
}



include __DIR__ . '/../../backend/connection/connection.php';
//Schedule



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styleadmin.css">
    <link rel="shortcut icon" href="../../src/logo.png">
    <title>SmartSched</title>
</head>
<style>
    /* container: keep header text left and buttons right */
.content-header {
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  gap: 16px;
  width: 100%;
  box-sizing: border-box;
}

/* action container: force horizontal row, do not wrap */
.header-actions {
  display: flex !important;
  flex-direction: row !important;
  flex-wrap: nowrap !important;         /* prevent stacking */
  gap: 10px !important;
  align-items: center !important;
  justify-content: flex-end !important;
}

/* individual buttons: do not expand to full width */
.header-actions .add-btn,
.content-header .add-btn {
  display: inline-flex !important;
  flex: 0 0 auto !important;            /* prevent growing/shrinking */
  width: auto !important;
  min-width: 0 !important;
  white-space: nowrap !important;
  align-items: center !important;
  gap: 6px !important;
  box-sizing: border-box !important;
}

/* if your .add-btn has width:100% somewhere, this cancels it */
.header-actions .add-btn[style] {
  /* nothing, but keeps inline-style precedence if used below */
}

/* Optional: make the plus look neat */
.header-actions .add-btn span {
  display: inline-flex;
  width: 20px;
  height: 20px;
  justify-content: center;
  align-items: center;
  border-radius: 4px;
  font-weight: 700;
}

.clickable-row {
    cursor: pointer;
}

.clickable-row td {
    padding: 20px;
    color: inherit;
    text-decoration: none;
}

.clickable-row:hover {
    background: #f5f7fa;
}

.modal-xl {
    width: 95%;
    max-width: 1200px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-close {
    font-size: 20px;
    text-decoration: none;
    color: #666;
}

.time-cell {
    width: 140px;
    background: #fafafa;
    font-weight: 500;
}

.class-card {
    background: #4a6cf7;
    color: white;
    border-radius: 6px;
    padding: 6px;
    font-size: 13px;
}

.class-card small {
    opacity: 0.9;
}

/* #background-class-card{
    
} */

/* ==== Loading Modal ==== */
.gen-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.6);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}

.gen-modal {
  background: #0b0f19;
  color: #00ff9c;
  width: 80%;
  max-width: 750px;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 0 40px rgba(0,255,156,.25);
}

.gen-modal h3 {
  margin-bottom: 10px;
}

.gen-modal pre {
  background: #000;
  height: 320px;
  overflow-y: auto;
  padding: 10px;
  font-size: 12px;
}

.spinner {
  width: 28px;
  height: 28px;
  border: 4px solid #222;
  border-top: 4px solid #00ff9c;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 10px;
}

/* .schedule-grid {
  font-size: 13px;
  display: grid;
  grid-template-columns:
    130px repeat(var(--col-count), 160px);
  grid-auto-rows: 48px;
  background: #e5e7eb;
  border-radius: 12px;
  position: relative;
} */

  .schedule-grid {
    font-size: 13px;
  display: grid;
  grid-template-columns:
    180px repeat(var(--col-count), 160px);
  grid-auto-rows: 48px;
  background: transparent;
  position: relative;
  overflow: visible;
}


.schedule-grid {
  overflow: visible; /* DO NOT scroll grid itself */
}

.schedule-grid {
  grid-auto-columns: 0;
}

.schedule-wrapper {
  overflow-x: auto;
  overflow-y: auto;
}



/* .schedule-grid > * {
  outline: .5px dashed gray;
} */


@keyframes spin {
  to { transform: rotate(360deg); }
}



</style>
<body>
    <nav>
        <div class="nav-listdown">
            <div class="logo-container">
                <span class="img-container"><img src="../../src/logo.png"></span>
                <p>SmartSched</p>
            </div>

            <a href="../page_dashboard/dashboard.php">
                <div class="nav-list nav-dashboard"><span class="img-container-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard-icon lucide-layout-dashboard"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                </span>
                    <p>Dashboard</p>
                </div>
            </a>

            <a href="../page_schedule/schedule-ui.php">
                <div class="nav-list nav-schedule active"><span class="img-container-logo">
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
            <a href="../page_account/account.php"><div class="nav-list"><span class="img-container-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-cog-icon lucide-user-cog"><path d="M10 15H6a4 4 0 0 0-4 4v2"/><path d="m14.305 16.53.923-.382"/><path d="m15.228 13.852-.923-.383"/><path d="m16.852 12.228-.383-.923"/><path d="m16.852 17.772-.383.924"/><path d="m19.148 12.228.383-.923"/><path d="m19.53 18.696-.382-.924"/><path d="m20.772 13.852.924-.383"/><path d="m20.772 16.148.924.383"/><circle cx="18" cy="15" r="3"/><circle cx="9" cy="7" r="4"/></svg>
            </span>
                <p>User Settings</p></div></a>
            <a href="../../backend/login/backend_logout.php"><div class="nav-list"><span class="img-container-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out-icon lucide-log-out"><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>
            </span><p>Log Out</p></div></a>
        </div>
    </nav>
    <section class="section-class">

        <div class="container">
            <div class="content-header">
                <div class="header-text">
                    <h1><b>View and Edit your Schedule</b></h1>
                </div>

                <div class="header-actions">
                    <a href="../../backend/functions/export_excel_all.php">
                        <button>Export All (Excel)</button>
                    </a>
                    <a href="../../backend/functions/edit_schedule-ui.php">
                        <button class="add-btn">
                            <span>✎</span>
                            Edit Schedule
                        </button>
                    </a>
                    

                    <button class="add-btn" id="generateBtn">
                        <span>⚙</span>
                        Generate Schedule
                    </button>
                </div>
            </div>


            <div class="tabs">

            <a href="schedule-ui.php?tab=day" class="tab <?= $tab === 'day' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 2H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 4h2v5l-1-.75L9 9V4z"/>
                </svg>
                On Day
            </a>
            <a href="schedule-ui.php?tab=faculty" class="tab <?= $tab === 'faculty' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
                Faculty
            </a>
            <a href="schedule-ui.php?tab=section" class="tab <?= $tab === 'section' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                </svg>
                Section
            </a>
            <a href="schedule-ui.php?tab=room" class="tab <?= $tab === 'room' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                </svg>
                Room
            </a>
            </div>

    <?php include __DIR__ . "/tabs/tab-$tab.php"; ?>

        
    </section>
    
<?php
    $showModal = false;
    $title = '';
    $filterSql = '';
    $params = [];

    /* =========================
    MODAL FILTER CONTROLLER
    ========================= */

    if ($view === 'day' && !empty($_GET['day'])) {
        $showModal = true;
        $title = "Schedule for " . $_GET['day'];
        $filterSql = "WHERE day = ?";
        $params[] = $_GET['day'];

    } elseif ($view === 'faculty' && !empty($_GET['faculty'])) {
        $showModal = true;
        $title = "Schedule for Faculty: " . $_GET['faculty'];
        $filterSql = "WHERE faculty = ?";
        $params[] = $_GET['faculty'];

    } elseif ($view === 'section' && !empty($_GET['section'])) {
        $showModal = true;
        $title = "Schedule for Section: " . $_GET['section'];
        $filterSql = "WHERE section = ?";
        $params[] = $_GET['section'];

    } elseif ($view === 'room' && !empty($_GET['room'])) {
        $showModal = true;
        $title = "Schedule for Room: " . $_GET['room'];
        $filterSql = "WHERE room = ?";
        $params[] = $_GET['room'];
    }

    ?>
    <?php if ($showModal): ?>
    <div class="modal-backdrop" id="scheduleModal">
        <div class="modal modal-xl">
            <div class="modal-header">
                <div>
                    <h2><?= htmlspecialchars($title) ?></h2>
                    <p>Timetable Overview</p>
                </div>
                <a href="schedule-ui.php?tab=<?= urlencode($tab) ?>" class="modal-close">✕</a>
            </div>

            <div class="modal-body">
                <?php include __DIR__ . '/schedule-timetable.php'; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>



    <script>
    document.querySelectorAll(".clickable-row").forEach(row => {
        row.addEventListener("click", () => {
            if (row.dataset.href) {
                window.location.href = row.dataset.href;
            }
        });
    });
    
    </script>

    <div id="generationModal" style="display:none;">
        <div class="gen-backdrop">
            <div class="gen-modal">
            <h3>Generating Schedule</h3>

            <div class="spinner"></div>

            <pre id="generationLogs">Starting…</pre>
            </div>
        </div>
    </div>

    <script>
    let logTimer = null;

    document.getElementById('generateBtn').addEventListener('click', () => {
        // show modal
        document.getElementById('generationModal').style.display = 'block';
        document.getElementById('generationLogs').textContent = 'Starting...\n';

        // start generation (background)
        fetch('../../backend/functions/generate_schedule_with_available_slots1.php', {
            method: 'POST'
        });

        // poll logs
        logTimer = setInterval(fetchLogs, 1500);
    });

    function fetchLogs() {
        fetch('../../backend/functions/read_generation_debug.php')
            .then(res => res.text())
            .then(text => {
                const box = document.getElementById('generationLogs');
                box.textContent = text;
                box.scrollTop = box.scrollHeight;

                if (text.includes('Done Generating')) {
                    clearInterval(logTimer);

                    setTimeout(() => {
                        window.location.href = '../../backend/functions/edit_schedule-ui.php';
                    }, 1000);
                }
            });
    }
    </script>


    </body>
    

</html>