<?php

include __DIR__ . '/../../backend/page_manage/subjects.php';   
include __DIR__ . '/../../backend/page_manage/courses.php';  
include  __DIR__ . '/../../backend/page_manage/faculty.php';  
include  __DIR__ . '/../../backend/page_manage/section.php';  
include  __DIR__ . '/../../backend/page_manage/room.php';  

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

</style>
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
        <h1>Manage Data</h1>

        <div class="tabs">
            <button id="course-btn" class="tab">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
                </svg>
                Course
            </button>
            <button id="subject-btn" class="tab active">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 2H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 4h2v5l-1-.75L9 9V4z"/>
                </svg>
                Subject
            </button>
            <button id="faculty-btn" class="tab">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
                Faculty
            </button>
            <button id="section-btn" class="tab">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                </svg>
                Section
            </button>
            <button id="room-btn" class="tab">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                </svg>
                Room
            </button>
        </div>

        <div id="subject-tab" class="table-container">
            <div class="content-header">
                <div class="header-text">
                    <h2>Subject Management</h2>
                    <p>Add and manage Subject</p>
                </div>
                <button id="add-btn-subject" class="add-btn">
                    <span>+</span>
                    Add Subject
                </button>
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
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search for subject"
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        >
                        <!-- <button type="submit">🔍</button> -->
                    </form>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Subject Name</th>
                        <th>Short Name</th>
                        <th>Type</th>
                        <th>Units</th>
                        <th>Hours</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($subjects): ?>
                    <?php foreach ($subjects as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['subject']) ?></td>
                        <td><?= htmlspecialchars($row['subject_name']) ?></td>
                        <td><?= htmlspecialchars($row['short_name']) ?></td>
                        <td><?= htmlspecialchars($row['type']) ?></td>
                        <td><?= htmlspecialchars($row['units']) ?></td>
                        <td><?= htmlspecialchars($row['hours']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <button 
                                class="edit-btn edit-btn-subject"
                                data-id="<?= $row['id'] ?>"
                                data-subject="<?= htmlspecialchars($row['subject']) ?>"
                                data-name="<?= htmlspecialchars($row['subject_name']) ?>"
                                data-short="<?= htmlspecialchars($row['short_name']) ?>"
                                data-type="<?= htmlspecialchars($row['type']) ?>"
                                data-units="<?= htmlspecialchars($row['units']) ?>"
                                data-hours="<?= htmlspecialchars($row['hours']) ?>"
                                data-status="<?= htmlspecialchars($row['status']) ?>"
                            >
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8">No subjects found.</td></tr>
                <?php endif; ?>
                </tbody>

            </table>

            <div class="pagination" style="margin-top:10px;">
                <?php
                if($totalPages > 1):

                    // Determine start and end page numbers (limit to 5 pages max)
                    $maxPagesToShow = 5;
                    $startPage = max(1, $page - floor($maxPagesToShow / 2));
                    $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);

                    // Adjust startPage if we're near the end
                    $startPage = max(1, $endPage - $maxPagesToShow + 1);
                ?>
                
                <a href="?page=<?= max(1, $page-1) ?>&search=<?= urlencode($search) ?>" style="color: gray; text-decoration: none;">&laquo;</a>

                <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" 
                    style="color: <?= ($i == $page) ? 'black' : 'gray' ?>; font-weight: <?= ($i == $page) ? 'bold' : 'normal' ?>; margin:0 5px; text-decoration:none;">
                    <?= $i ?>
                    </a>
                <?php endfor; ?>

                <a href="?page=<?= min($totalPages, $page+1) ?>&search=<?= urlencode($search) ?>" style="color: gray; text-decoration: none;">&raquo;</a>

                <?php endif; ?>
            </div>
        </div>
        <div id="course-tab" class="table-container">
            <!-- <div class="content-header">
                <div class="header-text">
                    <h2>Courses Management</h2>
                    <p>Add and manage Courses</p>
                </div>
                <div class="header-actions">
                    <button id="assign-btn-course" class="add-btn">
                    <span>+</span>
                    Assign Subjects
                    </button>
                    <button id="add-btn-course" class="add-btn">
                        <span>+</span>
                        Add Courses
                    </button>
                </div>
                
            </div> -->
            <div class="content-header">
            <div class="header-text">
                <h2>Courses Management</h2>
                <p>Add and manage Courses</p>
            </div>

            <div class="header-actions">
                <button id="assign-btn-course-subject" class="add-btn">
                <span>+</span>
                    Assign Subjects
                </button>

                <button id="add-btn-course" class="add-btn">
                <span>+</span>
                Add Courses
                </button>
            </div>
            </div>

            <div class="table-header">
                <div class="table-title">All Courses</div>
                <div class="filters">
                    <select class="filter-select">
                        <option>1st Semester</option>
                        <option>2nd Semester</option>
                    </select>
                    <select class="filter-select">
                        <option>School Year</option>
                        <option>2023-2024</option>  
                        <option>2024-2025</option>
                    </select>
                    <div class="search-box">
                        <input type="text" placeholder="Search for Courses">
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Course Name</th>
                        <th>Short Name</th>
                        <th>Type</th>
                        <th>Units</th>
                        <th>Hours</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($courses_rows): ?>
                        <?php foreach($courses_rows as $course): ?>
                        <tr>
                            <td><?= htmlspecialchars($course['course']) ?></td>
                            <td><?= htmlspecialchars($course['course_name']) ?></td>
                            <td><?= htmlspecialchars($course['short_name']) ?></td>
                            <td><?= htmlspecialchars($course['type']) ?></td>
                            <td><?= htmlspecialchars($course['units']) ?></td>
                            <td><?= htmlspecialchars($course['hours']) ?></td>
                            <td><?= htmlspecialchars($course['status']) ?></td>
                            <td>
                            <button 
                                class="edit-btn edit-btn-course"
                                data-id="<?= $row['id'] ?>"
                                data-course="<?= htmlspecialchars($course['course']) ?>"
                                data-course_name="<?= htmlspecialchars($course['course_name']) ?>"
                                data-short_name="<?= htmlspecialchars($course['short_name']) ?>"
                                data-type="<?= htmlspecialchars($course['type']) ?>"
                                data-units="<?= htmlspecialchars($course['units']) ?>"
                                data-hours="<?= htmlspecialchars($course['hours']) ?>"
                                data-status="<?= htmlspecialchars($course['status']) ?>"
                            >
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                            </button>

                            </td>
                            
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7">No courses found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php
                $maxPages = 5;
                $startPage = max(1, $courses_page - floor($maxPages / 2));
                $endPage = min($courses_totalPages, $startPage + $maxPages - 1);
                $startPage = max(1, $endPage - $maxPages + 1);
                ?>

                <!-- Previous Arrow -->
                <a href="?courses_page=<?= max(1, $courses_page-1) ?>&courses_search=<?= urlencode($courses_search) ?>" 
                style="color: gray;">&laquo;</a>

                <!-- Page Numbers -->
                <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?courses_page=<?= $i ?>&courses_search=<?= urlencode($courses_search) ?>" 
                    style="color: <?= ($i == $courses_page) ? 'black' : 'gray' ?>; 
                            font-weight: <?= ($i == $courses_page) ? 'bold' : 'normal' ?>;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <!-- Next Arrow -->
                <a href="?courses_page=<?= min($courses_totalPages, $courses_page+1) ?>&courses_search=<?= urlencode($courses_search) ?>" 
                style="color: gray;">&raquo;</a>
            </div>


        </div>
        <div id="faculty-tab" class="table-container">
            <div class="content-header">
                <div class="header-text">
                    <h2>Faculty Management</h2>
                    <p>Add and manage Faculty</p>
                </div>
                <button id="add-btn-faculty" class="add-btn">
                    <span>+</span>
                    Add Faculty
                </button>
            </div>
            <div class="table-header">
                <div class="table-title">All Faculty</div>
                <div class="filters">
                    <!-- <select class="filter-select">
                        <option>1st Semester</option>
                        <option>2nd Semester</option>
                    </select>
                    <select class="filter-select">
                        <option>School Year</option>
                        <option>2023-2024</option>  
                        <option>2024-2025</option>
                    </select> -->
                    <div class="search-box">
                        <input type="text" placeholder="Search for Faculty">
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Employment Type</th>
                        <th>Status</th>
                        <!-- <th>Total Units</th> -->
                        <th>Total Hours Per Week</th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($faculty_rows): ?>
                    <?php foreach ($faculty_rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['faculty_name']) ?></td>
                        <td><?= htmlspecialchars($row['employment_type']) ?></td>
                        <td><?= htmlspecialchars($row['current_status']) ?></td>
                       
                        <td><?= htmlspecialchars($row['total_hours_per_week']) ?></td>
                        <td>
                            <?php
                            $faculty_name = $row['faculty_name'];
                            $daysQuery = $conn->prepare("SELECT day FROM manage_faculty_days WHERE faculty_name = ?");
                            $daysQuery->bind_param("s", $faculty_name);
                            $daysQuery->execute();
                            $daysResult = $daysQuery->get_result();

                            $facultyDays = [];
                            while ($d = $daysResult->fetch_assoc()) {
                                $facultyDays[] = $d['day'];
                            }

                            $daysString = implode(",", $facultyDays);
                            // Fetch subjects from manage_faculty_subject
                            $subjQuery = $conn->prepare("SELECT subject FROM manage_faculty_subject WHERE faculty_name = ?");
                            $subjQuery->bind_param("s", $faculty_name);
                            $subjQuery->execute();
                            $subjResult = $subjQuery->get_result();

                            $facultySubjects = [];
                            while ($s = $subjResult->fetch_assoc()) {
                                $facultySubjects[] = $s['subject'];
                            }

                            $subjString = implode(",", $facultySubjects);

                            ?>
                            <button 
                                class="edit-btn edit-btn-faculty"
                                data-id="<?= $row['id'] ?>"
                                data-subjects="<?= htmlspecialchars($subjString) ?>"
                                data-name="<?= htmlspecialchars($row['faculty_name']) ?>"
                                data-employment="<?= htmlspecialchars($row['employment_type']) ?>"
                                data-status="<?= htmlspecialchars($row['current_status']) ?>"
                                data-units="<?= htmlspecialchars($row['total_units']) ?>"
                                data-days="<?= htmlspecialchars($daysString) ?>"
                                data-hours="<?= htmlspecialchars($row['total_hours_per_week']) ?>"
                            >
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">No faculty found.</td></tr>
                <?php endif; ?>
                </tbody>

            </table>

            <div class="pagination">
                <?php
                $maxPages = 5;
                $startPage = max(1, $faculty_page - floor($maxPages / 2));
                $endPage = min($faculty_totalPages, $startPage + $maxPages - 1);
                $startPage = max(1, $endPage - $maxPages + 1);
                ?>

                <a href="?faculty_page=<?= max(1, $faculty_page-1) ?>&faculty_search=<?= urlencode($faculty_search) ?>" style="color: gray;">&laquo;</a>

                <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?faculty_page=<?= $i ?>&faculty_search=<?= urlencode($faculty_search) ?>" 
                    style="color: <?= ($i == $faculty_page) ? 'black' : 'gray' ?>; font-weight: <?= ($i == $faculty_page) ? 'bold' : 'normal' ?>;">
                    <?= $i ?>
                    </a>
                <?php endfor; ?>

                <a href="?faculty_page=<?= min($faculty_totalPages, $faculty_page+1) ?>&faculty_search=<?= urlencode($faculty_search) ?>" style="color: gray;">&raquo;</a>
            </div>

        </div>
        <div id="section-tab" class="table-container">
            <div class="content-header">
                <div class="header-text">
                    <h2>Section Management</h2>
                    <p>Add and manage Section</p>
                </div>
                <button id="add-btn-section" class="add-btn">
                    <span>+</span>
                    Add Section
                </button>
            </div>
            <div class="table-header">
                <div class="table-title">All Section</div>
                <div class="filters">
                    <!-- <select class="filter-select">
                        <option>1st Semester</option>
                        <option>2nd Semester</option>
                    </select>
                    <select class="filter-select">
                        <option>School Year</option>
                        <option>2023-2024</option>  
                        <option>2024-2025</option>
                    </select> -->
                    <div class="search-box">
                        <input type="text" placeholder="Search for Section">
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Section Name</th>
                        <th>Total Students</th>
                        <th>Course</th>
                        <th>Year level</th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($section_rows): ?>
                    <?php foreach ($section_rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['section_name']) ?></td>
                        <td><?= htmlspecialchars($row['total_students']) ?></td>
                        <td><?= htmlspecialchars($row['section_course']) ?></td>
                        <td><?= htmlspecialchars($row['section_year']) ?></td>
                        <td>
                            <button 
                                class="edit-btn edit-btn-section"
                                data-id="<?= $row['id'] ?>"
                                data-section_name="<?= htmlspecialchars($row['section_name']) ?>"
                                data-total_students="<?= htmlspecialchars($row['total_students']) ?>"
                                data-total_units="<?= htmlspecialchars($row['section_course']) ?>"
                                data-total_hours="<?= htmlspecialchars($row['section_year']) ?>"
                            >
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No sections found.</td></tr>
                <?php endif; ?>
                </tbody>

            </table>

            <div class="pagination" style="margin-top:10px;">
                <?php if($section_totalPages > 1):

                    $maxPagesToShow = 5;
                    $startPage = max(1, $section_page - floor($maxPagesToShow / 2));
                    $endPage = min($section_totalPages, $startPage + $maxPagesToShow - 1);
                    $startPage = max(1, $endPage - $maxPagesToShow + 1);

                ?>

                <a href="?section_page=<?= max(1, $section_page-1) ?>&section_search=<?= urlencode($section_search) ?>" style="color: gray; text-decoration: none;">&laquo;</a>

                <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?section_page=<?= $i ?>&section_search=<?= urlencode($section_search) ?>"
                        style="color: <?= ($i == $section_page) ? 'black' : 'gray' ?>;
                            font-weight: <?= ($i == $section_page) ? 'bold' : 'normal' ?>;
                            margin:0 5px; text-decoration:none;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <a href="?section_page=<?= min($section_totalPages, $section_page+1) ?>&section_search=<?= urlencode($section_search) ?>" style="color: gray; text-decoration: none;">&raquo;</a>

                <?php endif; ?>
            </div>

        </div>
        <div id="room-tab" class="table-container">
            <div class="content-header">
                <div class="header-text">
                    <h2>Room Management</h2>
                    <p>Add and manage Room</p>
                </div>
                <button id="add-btn-room" class="add-btn">
                    <span>+</span>
                    Add Room
                </button>
            </div>
            <div class="table-header">
                <div class="table-title">All Room</div>
                <div class="filters">
                    <!-- <select class="filter-select">
                        <option>1st Semester</option>
                        <option>2nd Semester</option>
                    </select>
                    <select class="filter-select">
                        <option>School Year</option>
                        <option>2023-2024</option>  
                        <option>2024-2025</option>
                    </select> -->
                    <div class="search-box">
                        <input type="text" placeholder="Search for Room">
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>

                        <th>Room Name</th>
                        <th>Room Capacity</th>
                        <th> </th>
                
                    </tr>
                </thead>
                <tbody>
                <?php if ($room_rows): ?>
                    <?php foreach ($room_rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['room_name']) ?></td>
                        <td><?= htmlspecialchars($row['room_capacity']) ?></td>
                        <td>
                            <button 
                                class="edit-btn edit-btn-room"
                                data-id_room="<?= $row['id'] ?>"
                                data-name_room="<?= htmlspecialchars($row['room_name']) ?>"
                                data-capacity_room="<?= htmlspecialchars($row['room_capacity']) ?>"
                            >
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">No rooms found.</td></tr>
                <?php endif; ?>
                </tbody>

            </table>

            <div class="pagination" style="margin-top:10px;">
                <?php if($room_totalPages > 1):

                    $maxPagesToShow = 5;
                    $startPage = max(1, $room_page - floor($maxPagesToShow / 2));
                    $endPage = min($room_totalPages, $startPage + $maxPagesToShow - 1);
                    $startPage = max(1, $endPage - $maxPagesToShow + 1);

                ?>

                <a href="?room_page=<?= max(1, $room_page-1) ?>&room_search=<?= urlencode($room_search) ?>" style="color: gray; text-decoration: none;">&laquo;</a>

                <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?room_page=<?= $i ?>&room_search=<?= urlencode($room_search) ?>"
                        style="color: <?= ($i == $room_page) ? 'black' : 'gray' ?>;
                            font-weight: <?= ($i == $room_page) ? 'bold' : 'normal' ?>;
                            margin:0 5px; text-decoration:none;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <a href="?room_page=<?= min($room_totalPages, $room_page+1) ?>&room_search=<?= urlencode($room_search) ?>" style="color: gray; text-decoration: none;">&raquo;</a>

                <?php endif; ?>
            </div>

        </div>
    </section>
    <!-- ADD SUBJECT -->
    <div class="modal-backdrop hidden" id="addSubjectModal">
        <div class="modal">
        <div class="modal-header">
            <h2>Add New Subject</h2>
            <p>Input the details for the new subject</p>
        </div>

        <form method="post" action="add_subject.php">
            <div class="form-group">
                <label>Subject Name</label>
                <input type="text" name="subject_name" placeholder="e.g., Calculus 1">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Subject Code</label>
                    <input type="text" name="subject_code" placeholder="e.g.,MATH201">
                </div>
                <div class="form-group">
                    <label>Short Name</label>
                    <input type="text" name="short_name" placeholder="e.g., Cal 1">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>SchoolYear</label>
                    <select name="school_year">
                        <option>Select school year</option>
                        <option>2023-2024</option>
                        <option>2024-2025</option>
                        <option>2025-2026</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Semester</label>
                    <select name="semester">
                        <option>Select semester</option>
                        <option>1st Semester</option>
                        <option>2nd Semester</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Units</label>
                    <input type="text" name="units" placeholder="e.g.,3">
                </div>
                <div class="form-group">
                    <label>Subject Type</label>
                    <select  name="type">
                        <option value="Lecture">Lecture</option>
                        <option value="Laboratory">Laboratory</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Hours per Week</label>
                    <input type="text" name="hours_per_week" placeholder="e.g.,3 Hours">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option>Select status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="button-group">
                <button type="button" id="btncancel" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Subject</button>
            </div>
        </form>

        </div>
    </div>
    <!-- ADD COURSE -->

    <div class="modal-backdrop hidden" id="addCourseModal">
        <div class="modal">
        <div class="modal-header">
            <h2>Add New Course</h2>
            <p>Input the details for the new course</p>
        </div>

        <form method="post" action="add_course.php">
            <div class="form-group">
                <label>Course Name</label>
                <input type="text" name="course" placeholder="e.g., Bachelor of Science in Computer Science">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Course Acronyms</label>
                    <input type="text" name="course_name" placeholder="e.g.,BSCS">
                </div>
                <div class="form-group">
                    <label>Short Name</label>
                    <input type="text" name="short_name" placeholder="e.g., Cal 1">
                </div>
            </div>


            <div class="form-row">
                <div class="form-group">
                    <label>Units</label>
                    <input type="text" name="units" placeholder="e.g.,3">
                </div>
                <div class="form-group">
                    <label>Hours per Week</label>
                    <input type="text" name="hours" placeholder="e.g.,3 Hours">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Type</label>
                    <select name="type">
                        <option>Select status</option>
                        <option>SHS</option>
                        <option>College</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option>Select status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="button-group">
                <button type="button" id="btncancel-course" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Course</button>
            </div>
        </form>

        </div>
    </div>


    <!-- ADD FACULTY -->

    <div class="modal-backdrop hidden" id="addFacultyModal">
        <div class="modal">
        <div class="modal-header">
            <h2>Add New Faculty</h2>
            <p>Input the details for the new Faculty</p>
        </div>

        <form method="post" action="add_faculty.php">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="faculty_name" placeholder="e.g., Juan Dela Cruz">
            </div>
            <div class="form-group search-wrapper">
                    <label>Subjects</label>
                    <input type="text" id="subjectSearch" placeholder="Search subject…">
                    <div id="selectedSubjects" class="selected-items"></div>
                    <input type="hidden" name="subjects" id="subjectsHidden">
                    <div class="suggestions" id="subjectSuggestions"></div>
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
                
                <div class="form-group">
                    <label>Preferred Day</label>
                    <div class="checkbox-group">
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
                    <label>Hours per Week</label>
                    <input type="text" name="total_hours_per_week" placeholder="e.g.,3 Hours">
                </div>  
                </div>  
           

            <div class="button-group">
                <button type="button" id="btncancel-faculty" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Course</button>
            </div>
        </form>

        </div>
    </div>

    <!-- ADD SECTION -->

    <div class="modal-backdrop hidden" id="addSectionModal">
        <div class="modal">
        <div class="modal-header">
            <h2>Add New Section</h2>
            <p>Input the details for the new section</p>
        </div>

        <form method="post" action="add_section.php">
            <div class="form-row">
                <div class="form-group">
                    <label>Section Name</label>
                    <input type="text" name="section_name" placeholder="e.g.,CS41A">
                </div>
                <div class="form-group">
                    <label>Year level</label>
                    <input type="text" name="section_year" placeholder="e.g.,3">
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
                <button type="submit" class="btn-save">Save Course</button>
            </div>
        </form>

        </div>
    </div>

    <!-- ADD ROOM -->

    <div class="modal-backdrop hidden" id="addRoomModal">
        <div class="modal">
        <div class="modal-header">
            <h2>Add New Room</h2>
            <p>Input the details for the new room</p>
        </div>

        <form method="post" action="add_room.php">
            <div class="form-row">
                <div class="form-group">
                    <label>Room name</label>
                    <input type="text" name="room_name" placeholder="e.g.,201, 301">
                </div>
                <div class="form-group">
                    <label>Room capacity</label>
                    <input type="text" name="room_capacity" placeholder="e.g.,30">
                </div>
            </div>


            <div class="button-group">
                <button type="button" id="btncancel-room" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save">Save Course</button>
            </div>
        </form>

        </div>
    </div>

    <!-- ASSIGN COURSE SUBJECTS -->
<div class="modal-backdrop hidden" id="assignCourseSubjectModal">
    <div class="modal">
        <div class="modal-header">
            <h2>Assign Course Subject</h2>
            <p>Assign or Modify the details for course subject</p>
        </div>

        <form method="post" action="assign_course_subjects.php" id="editRoomForm">
            <input type="hidden" name="id" id="edit_room_id">

            <!-- Course -->
            <div class="form-group">
                <label>Course</label>
                <select name="section_course" required>
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
                <select name="year_level" required>
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


     <!-- EDIT ROOM-->
        <div class="modal-backdrop hidden" id="editRoomModal">
            <div class="modal">
                <div class="modal-header">
                    <h2>Edit Room</h2>
                    <p>Modify the details for this Room</p>
                </div>

                <form method="post" action="update_room.php" id="editRoomForm">
                    <input type="hidden" name="id" id="edit_room_id">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Section Name</label>
                            <input type="text" name="room_name" id="edit_room_name">
                        </div>
                        <div class="form-group">
                            <label>Year level</label>
                            <input type="text" name="room_capacity" id="edit_room_capacity">
                        </div>
                    </div>
                    <div class="button-group">
                        <button type="button" id="btncancelEdit-room" class="btn-cancel">Cancel</button>
                        <button type="submit" class="btn-save">Update Subject</button>
                    </div>
                </form>
            </div>
        </div>

    <!-- EDIT SECTION-->
        <div class="modal-backdrop hidden" id="editSectionModal">
            <div class="modal">
                <div class="modal-header">
                    <h2>Edit Subject</h2>
                    <p>Modify the details for this subject</p>
                </div>

                <form method="post" action="update_section.php" id="editSubjectForm">
                    <input type="hidden" name="id" id="edit_section_id">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Section Name</label>
                            <input type="text" name="section_name" id="edit_section_name">
                        </div>
                        <div class="form-group">
                            <label>Year level</label>
                            <input type="text" name="section_year" id="edit_section_year">
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

    <!-- EDIT SUBJECT-->
        <div class="modal-backdrop hidden" id="editSubjectModal">
            <div class="modal">
                <div class="modal-header">
                    <h2>Edit Subject</h2>
                    <p>Modify the details for this subject</p>
                </div>

                <form method="post" action="update_subject.php" id="editSubjectForm">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="form-group">
                        <label>Subject Name</label>
                        <input type="text" name="subject_name" id="edit_subject_name">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Subject Code</label>
                            <input type="text" name="subject_code" id="edit_subject_code">
                        </div>
                        <div class="form-group">
                            <label>Short Name</label>
                            <input type="text" name="short_name" id="edit_short_name">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Units</label>
                            <input type="text" name="units" id="edit_units">
                        </div>
                        <div class="form-group">
                            <label>Subject Type</label>
                            <select name="type" id="edit_type">
                                <option value="Lecture">Lecture</option>
                                <option value="Laboratory">Laboratory</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Hours per Week</label>
                            <input type="text" name="hours_per_week" id="edit_hours">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="edit_status">
                                <option>Active</option>
                                <option>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="button" id="btncancelEdit" class="btn-cancel">Cancel</button>
                        <button type="submit" class="btn-save">Update Subject</button>
                    </div>
                </form>
            </div>
        </div>

    <!-- EDIT COURSE-->
        <div class="modal-backdrop hidden" id="editCourseModal">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal">
                <div class="modal-header">
                    <h2>Edit Course</h2>
                    <p>Modify the details for this subject</p>
                </div>

                    <form method="post" action="update_course.php" id="editCourseForm">
                        <input type="hidden" name="id" id="edit_course_id">

                        <div class="form-group">
                            <label>Course Name</label>
                            <input type="text" name="course" id="edit_course_name" placeholder="e.g., Bachelor of Science in Computer Science">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Course Acronyms</label>
                                <input type="text" name="course_name" id="edit_course_code" placeholder="e.g., BSCS">
                            </div>
                            <div class="form-group">
                                <label>Short Name</label>
                                <input type="text" name="short_name" id="edit_course_short" placeholder="e.g., COMSCI">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Units</label>
                                <input type="text" name="units" id="edit_course_units" placeholder="e.g.,3">
                            </div>
                            <div class="form-group">
                                <label>Hours per Week</label>
                                <input type="text" name="hours" id="edit_course_hours" placeholder="e.g.,3 Hours">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Type</label>
                                <input type="text" name="type" id="edit_course_type" placeholder="e.g., College, Shs">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="edit_course_status">
                                    <option>Select status</option>
                                    <option>Active</option>
                                    <option>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="button" id="btncancelEdit-course" class="btn-cancel">Cancel</button>
                            <button type="submit" class="btn-save">Save Changes</button>
                        </div>
                    </form>                         
            </div>
        </div>

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
                    <div class="form-group">
                        <label>Subjects</label>
                        <input type="text" id="edit_subjectSearch" placeholder="Search subject...">
                        <div id="edit_subjectSuggestions" class="subject-suggestion-box hidden"></div>
                        <div id="edit_selectedSubjects" class="selected-subject-tags"></div>
                        <input type="hidden" name="subjects_list" id="edit_subjects_list">
<!-- 
                        <input type="text" id="subjectSearch">
                        <div id="subjectSuggestions" class="subject-suggestion-box hidden"></div>
                        <div id="selectedSubjects" class="selected-subject-tags"></div>
                        <input type="hidden" name="subjects_list" id="subjects_list"> -->

                    </div>
                    <!-- <div class="form-group search-wrapper">
                        <label>Subjects</label>
                        <input type="text" id="subjectSearch" placeholder="Search subject…">
                        <div id="selectedSubjects" class="selected-items"></div>
                        <input type="hidden" name="subjects" id="subjectsHidden">
                        <div class="suggestions" id="subjectSuggestions"></div>
                    </div> -->

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

     
                
               

    <script>
        // TABS SA MANAGE -----------------------------------------
        // Tab buttons (always visible, just change styling)
    const tabButtons = {
        course: document.getElementById("course-btn"),
        subject: document.getElementById("subject-btn"),
        faculty: document.getElementById("faculty-btn"),
        section: document.getElementById("section-btn"),
        room: document.getElementById("room-btn")
    };

    const headerContent = {
        course: document.getElementById("course-header"),
        subject: document.getElementById("subject-header"),
        faculty: document.getElementById("faculty-header"),
        section: document.getElementById("section-header"),
        room: document.getElementById("room-header")
    }

    // Content panels (these get hidden/shown)
    const contentPanels = {
        course: document.getElementById("course-tab"),
        subject: document.getElementById("subject-tab"),
        faculty: document.getElementById("faculty-tab"),
        section: document.getElementById("section-tab"),
        room: document.getElementById("room-tab")
    };

    function showTab(activeTabName) {
    // Remove 'active' class from all buttons
    Object.values(tabButtons).forEach(btn => {
        if (btn) btn.classList.remove("active");
    });
    
    // Hide all content panels
    Object.values(contentPanels).forEach(panel => {
        if (panel) panel.style.display = "none";
    });
    
    // Hide all header content
    Object.values(headerContent).forEach(header => {
        if (header) header.style.display = "none";
    });
    
    // Add 'active' class to clicked button
    if (tabButtons[activeTabName]) {
        tabButtons[activeTabName].classList.add("active");
    }
    
    // Show the selected content panel
    if (contentPanels[activeTabName]) {
        contentPanels[activeTabName].style.display = "block";
    }
    
    // Show the selected header content
    if (headerContent[activeTabName]) {
        headerContent[activeTabName].style.display = "block";
    }
}

    // Add click listeners to buttons
    Object.keys(tabButtons).forEach(tabName => {
        if (tabButtons[tabName]) {
            tabButtons[tabName].onclick = () => showTab(tabName);
        }
    });

    //------------------ - MODALS DITOOOO

    //FOR ADD MODALS

    //ADD SUBJECT
        const modal = document.getElementById('addSubjectModal');
        const cancelBtn = document.getElementById('btncancel');
        const addBtn = document.getElementById('add-btn-subject');

        // Function to open modal
        function openModal() {
            modal.classList.remove('hidden');
        }

        // Function to close modal
        function closeModal() {
            modal.classList.add('hidden');
        }

        addBtn.onclick = openModal;

        // Close modal when clicking cancel button
        cancelBtn.onclick = closeModal;
        

        // Close modal when clicking outside the modal content
        modal.onclick = function(event) {
            if (event.target === modal) {
                closeModal();
            }
        }


        // ================= ASSIGN COURSE SUBJECT MODAL =================

        const modalAssign = document.getElementById('assignCourseSubjectModal');
        const cancelAssign = document.getElementById('btncancelAssign-coursesubject');
        const addBtnAssign = document.getElementById('assign-btn-course-subject');

        function openModalAssign() {
            modalAssign.classList.remove('hidden');
        }

        function closeModalAssign() {
            modalAssign.classList.add('hidden');
        }

        addBtnAssign.onclick = openModalAssign;
        cancelAssign.onclick = closeModalAssign;
        modalAssign.onclick = function(event) {
            if (event.target === modalAssign) {
                closeModalAssign();
            }
        }

        // ================= ADD SECTION =================
        const modalSection = document.getElementById('addSectionModal');
        const cancelSection = document.getElementById('btncancel-section');
        const addBtnSection = document.getElementById('add-btn-section');

        function openModalSection() {
            modalSection.classList.remove('hidden');
        }

        function closeModalSection() {
            modalSection.classList.add('hidden');
        }

        addBtnSection.onclick = openModalSection;
        cancelSection.onclick = closeModalSection;
        modalSection.onclick = function(event) {
            if (event.target === modalSection) {
                closeModalSection();
            }
        }

        // ================= ADD COURSE =================
        const modalCourse = document.getElementById('addCourseModal');
        const cancelCourse = document.getElementById('btncancel-course');
        const addBtnCourse = document.getElementById('add-btn-course');

        function openModalCourse() {
            modalCourse.classList.remove('hidden');
        }

        function closeModalCourse() {
            modalCourse.classList.add('hidden');
        }

        addBtnCourse.onclick = openModalCourse;
        cancelCourse.onclick = closeModalCourse;
        modalCourse.onclick = function(event) {
            if (event.target === modalCourse) {
                closeModalCourse();
            }
        }

        
        // ================= ADD ROOM =================
        const modalRoom = document.getElementById('addRoomModal');
        const cancelRoom = document.getElementById('btncancel-room');
        const addBtnRoom = document.getElementById('add-btn-room');

        function openModalRoom() {
            modalRoom.classList.remove('hidden');
        }

        function closeModalRoom() {
            modalRoom.classList.add('hidden');
        }

        addBtnRoom.onclick = openModalRoom;
        cancelRoom.onclick = closeModalRoom;
        modalRoom.onclick = function(event) {
            if (event.target === modalRoom) {
                closeModalRoom();
            }
        }


        // ================= ADD FACULTY =================
        const modalFaculty = document.getElementById('addFacultyModal');
        const cancelFaculty = document.getElementById('btncancel-faculty');
        const addBtnFaculty = document.getElementById('add-btn-faculty');

        function openModalFaculty() {
            modalFaculty.classList.remove('hidden');
        }

        function closeModalFaculty() {
            modalFaculty.classList.add('hidden');
        }

        addBtnFaculty.onclick = openModalFaculty;
        cancelFaculty.onclick = closeModalFaculty;
        modalFaculty.onclick = function(event) {
            if (event.target === modalFaculty) {
                closeModalFaculty();
            }
        }


    //FOR EDIT MODALAS

        document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById("editSubjectModal");
            const cancelBtn = document.getElementById("btncancelEdit");
            const editButtons = document.querySelectorAll(".edit-btn-subject");

            editButtons.forEach(button => {
                button.addEventListener("click", () => {
                    // Populate modal fields from data attributes
                    document.getElementById("edit_id").value = button.dataset.id;
                    document.getElementById("edit_subject_name").value = button.dataset.name;
                    document.getElementById("edit_subject_code").value = button.dataset.subject;
                    document.getElementById("edit_short_name").value = button.dataset.short;
                    document.getElementById("edit_type").value = button.dataset.type;
                    document.getElementById("edit_units").value = button.dataset.units;
                    document.getElementById("edit_hours").value = button.dataset.hours;
                    document.getElementById("edit_status").value = button.dataset.status;

                    // Show modal
                    modal.classList.remove("hidden");
                });
            });

            cancelBtn.addEventListener("click", () => {
                modal.classList.add("hidden");
            });
        });
        

        // ================= EDIT COURSE MODAL =================
        document.addEventListener("DOMContentLoaded", () => {
            const modalCourse = document.getElementById("editCourseModal");
            const cancelCourse = document.getElementById("btncancelEdit-course");
            const editButtonsCourse = document.querySelectorAll(".edit-btn-course");

            editButtonsCourse.forEach(button => {
                button.addEventListener("click", () => {
                    document.getElementById("edit_course_id").value = button.dataset.id;
                    document.getElementById("edit_course_name").value = button.dataset.course;
                    document.getElementById("edit_course_code").value = button.dataset.course_name;
                    document.getElementById("edit_course_short").value = button.dataset.short_name;
                    document.getElementById("edit_course_type").value = button.dataset.type;
                    document.getElementById("edit_course_units").value = button.dataset.units;
                    document.getElementById("edit_course_hours").value = button.dataset.hours;
                    document.getElementById("edit_course_status").value = button.dataset.status;

                    modalCourse.classList.remove("hidden");
                });
            });

            cancelCourse.addEventListener("click", () => {
                modalCourse.classList.add("hidden");
            });
        });


        // ================= EDIT ROOM MODAL =================
        document.addEventListener("DOMContentLoaded", () => {
            const modalRoom = document.getElementById("editRoomModal");
            const cancelRoom = document.getElementById("btncancelEdit-room");
            const editButtonsRoom = document.querySelectorAll(".edit-btn-room");

            editButtonsRoom.forEach(button => {
                button.addEventListener("click", () => {
                    document.getElementById("edit_room_id").value = button.dataset.id_room;
                    document.getElementById("edit_room_name").value = button.dataset.name_room;
                    document.getElementById("edit_room_capacity").value = button.dataset.capacity_room;

                    modalRoom.classList.remove("hidden");
                });
            });

            cancelRoom.addEventListener("click", () => {
                modalRoom.classList.add("hidden");
            });
        });


        // ================= EDIT SECTION MODAL =================
        document.addEventListener("DOMContentLoaded", () => {
            const modalSection = document.getElementById("editSectionModal");
            const cancelSection = document.getElementById("btncancelEdit-section");
            const editButtonsSection = document.querySelectorAll(".edit-btn-section");

            editButtonsSection.forEach(button => {
                button.addEventListener("click", () => {
                    document.getElementById("edit_section_id").value = button.dataset.id;
                    document.getElementById("edit_section_name").value = button.dataset.section_name;
                    document.getElementById("edit_section_students").value = button.dataset.total_students;
                    document.getElementById("edit_section_course").value = button.dataset.total_units;
                    document.getElementById("edit_section_year").value = button.dataset.total_hours;

                    modalSection.classList.remove("hidden");
                });
            });

            cancelSection.addEventListener("click", () => {
                modalSection.classList.add("hidden");
            });
        });


        
        // ================= EDIT FACULTY MODAL =================


        // ================= EDIT FACULTY MODAL =================
// ================= EDIT FACULTY MODAL =================

// ================= EDIT FACULTY MODAL (robust version) =================

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("editFacultyModal");
    const cancelBtn = document.getElementById("btncancelEdit-faculty");
    const editButtons = document.querySelectorAll(".edit-btn-faculty");

    const subjectSearch = document.getElementById("edit_subjectSearch");
    const suggestionBox = document.getElementById("edit_subjectSuggestions");
    const selectedContainer = document.getElementById("edit_selectedSubjects");
    const hiddenSubjects = document.getElementById("edit_subjects_list");

    if (!subjectSearch || !suggestionBox || !selectedContainer || !hiddenSubjects) {
        console.error("Edit subject UI elements missing. Check IDs: edit_subjectSearch, edit_subjectSuggestions, edit_selectedSubjects, edit_subjects_list");
        return;
    }

    let selectedSubjects = [];

    // Render selected subject tags
    function updateSubjectTags() {
        selectedContainer.innerHTML = "";
        selectedSubjects.forEach(s => {
            const tag = document.createElement("div");
            tag.classList.add("subject-tag");
            tag.innerHTML = `${s} <button type="button" class="remove-tag" data-subj="${s}" aria-label="Remove ${s}">×</button>`;
            selectedContainer.appendChild(tag);
        });
        hiddenSubjects.value = selectedSubjects.join(",");
    }

    // -------------------- Load modal data --------------------
    editButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            // Basic fields
            document.getElementById("edit_faculty_id").value = btn.dataset.id || "";
            document.getElementById("edit_faculty_name").value = btn.dataset.name || "";
            document.getElementById("edit_employment_type").value = btn.dataset.employment || "";
            document.getElementById("edit_current_status").value = btn.dataset.status || "";
            document.getElementById("edit_total_hours").value = btn.dataset.hours || "";

            // Fetch subjects for this faculty from backend endpoint (if available), else from dataset
            const facultyName = btn.dataset.name || "";
            if (facultyName) {
                // try server fetch first (recommended)
                fetch("get_faculty_subjects.php?faculty_name=" + encodeURIComponent(facultyName))
                    .then(r => r.json())
                    .then(arr => {
                        if (Array.isArray(arr)) {
                            selectedSubjects = arr.filter(Boolean);
                            updateSubjectTags();
                        } else {
                            // fallback to dataset attribute if backend returns unexpected value
                            selectedSubjects = btn.dataset.subjects ? btn.dataset.subjects.split(",").filter(Boolean) : [];
                            updateSubjectTags();
                        }
                    })
                    .catch(err => {
                        console.warn("get_faculty_subjects failed, fallback to dataset:", err);
                        selectedSubjects = btn.dataset.subjects ? btn.dataset.subjects.split(",").filter(Boolean) : [];
                        updateSubjectTags();
                    });
            } else {
                // fallback: use data-subjects on the button
                selectedSubjects = btn.dataset.subjects ? btn.dataset.subjects.split(",").filter(Boolean) : [];
                updateSubjectTags();
            }

            // Load Preferred Days
            const selectedDays = btn.dataset.days ? btn.dataset.days.split(",") : [];
            document.querySelectorAll("input[name='preferred_day[]']").forEach(chk => {
                chk.checked = Array.isArray(selectedDays) && selectedDays.includes(chk.value);
            });

            // clear search box and hide suggestions
            subjectSearch.value = "";
            suggestionBox.innerHTML = "";
            suggestionBox.classList.add("hidden");

            // show modal
            modal.classList.remove("hidden");
        });
    });

    // -------------------- Close modal --------------------
    cancelBtn.addEventListener("click", () => {
        modal.classList.add("hidden");
    });

    // prevent clicks inside suggestion box from bubbling to modal/backdrop
    suggestionBox.addEventListener("mousedown", e => e.stopPropagation());
    subjectSearch.addEventListener("mousedown", e => e.stopPropagation());

    // -------------------- Suggestion fetch + delegation --------------------
    let lastFetchId = 0;
    subjectSearch.addEventListener("input", () => {
        const q = subjectSearch.value.trim();
        if (!q) {
            suggestionBox.innerHTML = "";
            suggestionBox.classList.add("hidden");
            return;
        }

        const fetchId = ++lastFetchId;
        fetch("search_subjects.php?q=" + encodeURIComponent(q))
            .then(res => res.json())
            .then(data => {
                // ignore out-of-order responses
                if (fetchId !== lastFetchId) return;

                if (!Array.isArray(data) || data.length === 0) {
                    suggestionBox.innerHTML = "";
                    suggestionBox.classList.add("hidden");
                    return;
                }

                // create suggestion items (escape values)
                suggestionBox.innerHTML = data.map(item => {
                    const subj = String(item.subject).replace(/</g, "&lt;").replace(/>/g, "&gt;");
                    const name = String(item.subject_name || "").replace(/</g, "&lt;").replace(/>/g, "&gt;");
                    return `<div class="subject-option" data-subj="${subj}">${subj}${name ? " — " + name : ""}</div>`;
                }).join("");

                suggestionBox.classList.remove("hidden");
            })
            .catch(err => {
                console.error("search_subjects.php error:", err);
                suggestionBox.classList.add("hidden");
            });
    });

    // Use event delegation: handle mousedown on suggestionBox (fires before blur)
    suggestionBox.addEventListener("mousedown", (ev) => {
        const target = ev.target.closest(".subject-option");
        if (!target) return;
        ev.stopPropagation();
        ev.preventDefault(); // prevent focus change issues

        const subj = target.dataset.subj;
        if (!subj) return;

        if (!selectedSubjects.includes(subj)) {
            selectedSubjects.push(subj);
            updateSubjectTags();
        }

        // clear and hide suggestion box
        suggestionBox.innerHTML = "";
        suggestionBox.classList.add("hidden");
        subjectSearch.value = "";

        // keep focus where appropriate
        subjectSearch.focus();
    });

    // hide after a short delay when input loses focus (so mousedown can fire)
    subjectSearch.addEventListener("blur", () => {
        setTimeout(() => {
            suggestionBox.classList.add("hidden");
        }, 150);
    });

    // allow removal via delegated click on tags (in case tags are dynamic)
    selectedContainer.addEventListener("click", (ev) => {
        const btn = ev.target.closest(".remove-tag");
        if (!btn) return;
        ev.stopPropagation();
        const subj = btn.dataset.subj;
        selectedSubjects = selectedSubjects.filter(s => s !== subj);
        updateSubjectTags();
    });

    // DEBUG helper: press Ctrl+Shift+S to log current state
    document.addEventListener("keydown", (e) => {
        if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 's') {
            console.log("selectedSubjects:", selectedSubjects, "hiddenSubjects.value:", hiddenSubjects.value);
        }
    });

}); // DOMContentLoaded end

        
        // -----dropwdown faculty subj --------------

        document.addEventListener("DOMContentLoaded", () => {

        const searchInput = document.getElementById("subjectSearch");
        const suggestionBox = document.getElementById("subjectSuggestions");
        const selectedBox = document.getElementById("selectedSubjects");
        const hiddenInput = document.getElementById("subjectsHidden");

        let selectedSubjects = [];

        // Fetch subjects live as user types
        searchInput.addEventListener("keyup", function () {
            let query = this.value.trim();

            if (query.length < 1) {
                suggestionBox.style.display = "none";
                return;
            }

            // AJAX request to get subjects
            fetch("search_subjects.php?q=" + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    suggestionBox.innerHTML = "";
                    if (data.length > 0) {
                        suggestionBox.style.display = "block";
                        data.forEach(sub => {
                            let div = document.createElement("div");
                            div.textContent = sub.subject + " - " + sub.subject_name;
                            div.dataset.value = sub.subject;
                            suggestionBox.appendChild(div);

                            div.onclick = () => {
                                addSubject(div.dataset.value);
                                searchInput.value = "";
                                suggestionBox.style.display = "none";
                            };
                        });
                    } else {
                        suggestionBox.style.display = "none";
                    }
                });
            });

            // Add subject to selected list
            function addSubject(code) {
                if (!selectedSubjects.includes(code)) {
                    selectedSubjects.push(code);
                    updateSelected();
                }
            }

            // Remove subject
            function removeSubject(code) {
                selectedSubjects = selectedSubjects.filter(c => c !== code);
                updateSelected();
            }

            // Update display + hidden field
            function updateSelected() {
                selectedBox.innerHTML = "";
                selectedSubjects.forEach(code => {
                    let item = document.createElement("div");
                    item.classList.add("item");
                    item.innerHTML = `
                        ${code} <span data-remove="${code}">&times;</span>
                    `;
                    selectedBox.appendChild(item);

                    item.querySelector("span").onclick = () => removeSubject(code);
                });

                hiddenInput.value = selectedSubjects.join(","); // comma-separated list
            }

        });


    </script>
    </body>
</html>