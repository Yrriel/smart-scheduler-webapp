<?php

include 'subjects.php';   
include 'courses.php';  
include 'faculty.php';  
include 'section.php';  
include 'room.php';  

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleadmin.css">
    <link rel="shortcut icon" href="src/logo.png" type="image/x-icon">
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

</style>
<body>
    <nav>
       <div class="nav-listdown">
            <div class="logo-container">
                <span class="img-container"><img src="src/logo.png" alt="" srcset=""></span>
                <p>SmartSched</p>
            </div>
            <a href="dashboard.php">
                <div class="nav-list nav-dashboard">
                <p>Dashboard</p>
            </div>
            </a>
            <a href="schedule.php">
                <div class="nav-list nav-schedule">
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
            <a href="">
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
    <section class="section-class">
        <!-- <h1>Manage</h1>
        <div class="manage-nav">
            <div class="manage-nav-buttons">
                <p>Course</p>
            </div>
            <div class="manage-nav-buttons">
                <p>Subject</p>
            </div>
            <div class="manage-nav-buttons">
                <p>Faculty</p>
            </div>
            <div class="manage-nav-buttons">
                <p>Section</p>
            </div>
            <div class="manage-nav-buttons">
                <p>Room</p>
            </div>
        </div> -->
        <div class="container">
            <div class="content-header">
                <div class="header-text">
                    <h1>Dashboard</h1>
                </div>

            <form method="post" action="generate_schedule.php">
                <button class="add-btn">
                    <span>⚙</span>
                    Generate Schedule
                </button>
            </form>
            </div>

            <div class="tabs">

            <button id="#" class="tab active">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 2H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 4h2v5l-1-.75L9 9V4z"/>
                </svg>
                On Day
            </button>
            <button id="#" class="tab">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
                Faculty
            </button>
            <button id="#" class="tab">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                </svg>
                Section
            </button>
            <button id="#" class="tab">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                </svg>
                Room
            </button>
            </div>

            <div id="subject-tab" class="table-container">
            <div class="content-header">
                <div class="header-text">
                    <h2>On Day Schedules</h2>
                    <p>Lisst of schedules within a day</p>
                </div>
                <!-- <button id="add-btn-subject" class="add-btn">
                    <span>+</span>
                    Add Subject
                </button> -->
            </div>

            <div class="table-header">
                <div class="table-title">All Subjects</div>
                <div class="filters">
                    <!-- <select class="filter-select" name="semester">
                        <option value="">Select Semester</option>
                        <option>1st Semester</option>
                        <option>2nd Semester</option>
                    </select>
                    <select class="filter-select" name="school_year">
                        <option value="">School Year</option>
                        <option>2023-2024</option>
                        <option>2024-2025</option>
                    </select> -->

                    <form class="search-box" method="get" action="">
                        <!-- <input 
                            type="text" 
                            name="search" 
                            placeholder="Search for subject"
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        > -->
                        <!-- <button type="submit">🔍</button> -->
                    </form>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Total Schedules</th>
                        <!-- <th>Short Name</th>
                        <th>Type</th>
                        <th>Units</th>
                        <th>Hours</th>
                        <th>Status</th>
                        <th>Action</th> -->
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Monday</td>
                        <td>testing</td>
                    </tr>
                    <tr>
                        <td>Tuesday</td>
                        <td>testing</td>
                    </tr>
                    <tr>
                        <td>Wednesday</td>
                        <td>testing</td>
                    </tr>
                    <tr>
                        <td>Thursday</td>
                        <td>testing</td>
                    </tr>
                    <tr>
                        <td>Friday</td>
                        <td>testing</td>
                    </tr>
                    <tr>
                        <td>Saturday</td>
                        <td>testing</td>
                    </tr>
                </tbody>

            </table>

           
        </div>

        </div>
    </section>
   
     
    </body>
</html>