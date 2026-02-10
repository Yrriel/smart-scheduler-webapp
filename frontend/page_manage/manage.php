<?php

$tab = $_GET['tab'] ?? 'subject';

$allowedTabs = ['subject', 'course', 'faculty', 'section', 'room'];
if (!in_array($tab, $allowedTabs)) {
    $tab = 'subject';
}

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
                <div class="nav-list nav-schedule"><span class="img-container-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-heart-icon lucide-calendar-heart"><path d="M12.127 22H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5.125"/><path d="M14.62 18.8A2.25 2.25 0 1 1 18 15.836a2.25 2.25 0 1 1 3.38 2.966l-2.626 2.856a.998.998 0 0 1-1.507 0z"/><path d="M16 2v4"/><path d="M3 10h18"/><path d="M8 2v4"/></svg>
                </span>
                    <p>Schedule</p></div>
            </a>

            <a href="../page_manage/manage.php?tab=course">
                <div class="nav-list nav-schedule active"><span class="img-container-logo">
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
        <h1><b>Manage Data</b></h1>

        <div class="tabs">
            <a href="manage.php?tab=course" class="tab <?= $tab==='course'?'active':'' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open-icon lucide-book-open"><path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"/>
                    <path d="M19 2H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 4h2v5l-1-.75L9 9V4z"/>
                </svg>
                Course
            </a>
            <a href="manage.php?tab=subject" class="tab <?= $tab==='subject'?'active':'' ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 2H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 4h2v5l-1-.75L9 9V4z"/>
                </svg>
                
                Subject
            </a>

            
            <a href="manage.php?tab=faculty" class="tab <?= $tab==='faculty'?'active':'' ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
                Faculty
            </a>
            <a href="manage.php?tab=section" class="tab <?= $tab==='section'?'active':'' ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                </svg>    
                Section
            </a>

            <a href="manage.php?tab=room" class="tab <?= $tab==='room'?'active':'' ?>">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                </svg>
                Room
            </a>

        </div>

        <?php include __DIR__ . "/tabs/tab-$tab.php"; ?>

    </section>
        <?php include __DIR__ . '/modals/modal-add-subject.php'; ?>
        <?php include __DIR__ . '/modals/modal-edit-subject.php'; ?>

        <?php include __DIR__ . '/modals/modal-add-faculty.php'; ?>
        <?php include __DIR__ . '/modals/modal-edit-faculty.php'; ?>

        <?php include __DIR__ . '/modals/modal-add-course.php'; ?>
        <?php include __DIR__ . '/modals/modal-edit-course.php'; ?>

        <?php include __DIR__ . '/modals/modal-add-section.php'; ?>
        <?php include __DIR__ . '/modals/modal-edit-section.php'; ?>

        <?php include __DIR__ . '/modals/modal-add-room.php'; ?>
        <?php include __DIR__ . '/modals/modal-edit-room.php'; ?>

        <?php include __DIR__ . '/modals/modal-assign-course-subject.php'; ?>

    <script>

    document.addEventListener("DOMContentLoaded", () => {

        /* ============================
        HELPERS
        ============================ */
        const byId = id => document.getElementById(id);

        function bindModal(modalId, openBtnId, cancelBtnId) {
            const modal = byId(modalId);
            const openBtn = byId(openBtnId);
            const cancelBtn = byId(cancelBtnId);

            if (!modal || !openBtn || !cancelBtn) return;

            openBtn.onclick = () => modal.classList.remove("hidden");
            cancelBtn.onclick = () => modal.classList.add("hidden");

            modal.onclick = e => {
                if (e.target === modal) modal.classList.add("hidden");
            };
        }

        /* ============================
        ADD MODALS
        ============================ */
        bindModal("addSubjectModal", "add-btn-subject", "btncancel");
        bindModal("addFacultyModal", "add-btn-faculty", "btncancel-faculty");
        bindModal("addCourseModal", "add-btn-course", "btncancel-course");
        bindModal("addSectionModal", "add-btn-section", "btncancel-section");
        bindModal("addRoomModal", "add-btn-room", "btncancel-room");
        bindModal(
            "assignCourseSubjectModal",
            "assign-btn-course-subject",
            "btncancelAssign-coursesubject"
        );

        /* ============================
        EDIT SUBJECT MODAL
        ============================ */
        (() => {
            const modal = byId("editSubjectModal");
            const cancel = byId("btncancelEdit");
            const buttons = document.querySelectorAll(".edit-btn-subject");

            if (!modal || !buttons.length || !cancel) return;

            buttons.forEach(btn => {
                btn.onclick = () => {
                    byId("edit_id").value = btn.dataset.id || "";
                    byId("edit_subject_name").value = btn.dataset.name || "";
                    byId("edit_subject_code").value = btn.dataset.subject || "";
                    byId("edit_short_name").value = btn.dataset.short || "";
                    byId("edit_type").value = btn.dataset.type || "";
                    byId("edit_units").value = btn.dataset.units || "";
                    byId("edit_hours").value = btn.dataset.hours || "";
                    byId("edit_status").value = btn.dataset.status || "";
                    modal.classList.remove("hidden");
                };
            });

            cancel.onclick = () => modal.classList.add("hidden");
        })();

        /* ============================
        EDIT COURSE MODAL
        ============================ */
        (() => {
            const modal = byId("editCourseModal");
            const cancel = byId("btncancelEdit-course");
            const buttons = document.querySelectorAll(".edit-btn-course");

            if (!modal || !buttons.length || !cancel) return;

            buttons.forEach(btn => {
                btn.onclick = () => {
                    byId("edit_course_id").value = btn.dataset.id || "";
                    byId("edit_course_name").value = btn.dataset.course || "";
                    byId("edit_course_code").value = btn.dataset.course_name || "";
                    byId("edit_course_short").value = btn.dataset.short_name || "";
                    byId("edit_course_type").value = btn.dataset.type || "";
                    //byId("edit_course_units").value = btn.dataset.units || "";
                    //byId("edit_course_hours").value = btn.dataset.hours || "";
                    byId("edit_course_status").value = btn.dataset.status || "";
                    modal.classList.remove("hidden");
                };
            });

            cancel.onclick = () => modal.classList.add("hidden");
        })();

        /* ============================
        EDIT ROOM MODAL
        ============================ */
        (() => {
            const modal = byId("editRoomModal");
            const cancel = byId("btncancelEdit-room");
            const buttons = document.querySelectorAll(".edit-btn-room");

            if (!modal || !buttons.length || !cancel) return;

            buttons.forEach(btn => {
                btn.onclick = () => {
                    byId("edit_room_id").value = btn.dataset.id_room || "";
                    byId("edit_room_name").value = btn.dataset.name_room || "";
                    byId("edit_room_capacity").value = btn.dataset.capacity_room || "";
                    modal.classList.remove("hidden");
                };
            });

            cancel.onclick = () => modal.classList.add("hidden");
        })();

        /* ============================
        EDIT SECTION MODAL
        ============================ */
        (() => {
            const modal = byId("editSectionModal");
            const cancel = byId("btncancelEdit-section");
            const buttons = document.querySelectorAll(".edit-btn-section");

            if (!modal || !buttons.length || !cancel) return;

            buttons.forEach(btn => {
                btn.onclick = () => {
                    byId("edit_section_id").value = btn.dataset.id || "";
                    byId("edit_section_name").value = btn.dataset.section_name || "";
                    byId("edit_section_students").value = btn.dataset.total_students || "";
                    //byId("edit_section_course").value = btn.dataset.total_units || "";
                    //byId("edit_section_year").value = btn.dataset.total_hours || "";
                    modal.classList.remove("hidden");
                };
            });

            cancel.onclick = () => modal.classList.add("hidden");
        })();

        /* ============================
        EDIT FACULTY MODAL
        ============================ */
        (() => {
            const modal = byId("editFacultyModal");
            const cancel = byId("btncancelEdit-faculty");
            const buttons = document.querySelectorAll(".edit-btn-faculty");

            if (!modal || !buttons.length || !cancel) return;

            const subjectSearch = byId("edit_subjectSearch");
            const suggestionBox = byId("edit_subjectSuggestions");
            const selectedBox = byId("edit_selectedSubjects");
            const hiddenInput = byId("edit_subjects_list");

            if (!subjectSearch || !suggestionBox || !selectedBox || !hiddenInput) return;

            let selectedSubjects = [];

            function renderSubjects() {
                selectedBox.innerHTML = "";
                selectedSubjects.forEach(s => {
                    const tag = document.createElement("div");
                    tag.className = "subject-tag";
                    tag.innerHTML = `${s} <button type="button" data-subj="${s}">×</button>`;
                    selectedBox.appendChild(tag);
                });
                hiddenInput.value = selectedSubjects.join(",");
            }

            selectedBox.onclick = e => {
                if (e.target.tagName === "BUTTON") {
                    selectedSubjects = selectedSubjects.filter(s => s !== e.target.dataset.subj);
                    renderSubjects();
                }
            };

            buttons.forEach(btn => {
                btn.onclick = () => {
                    byId("edit_faculty_id").value = btn.dataset.id || "";
                    byId("edit_faculty_name").value = btn.dataset.name || "";
                    byId("edit_employment_type").value = btn.dataset.employment || "";
                    byId("edit_current_status").value = btn.dataset.status || "";
                    byId("edit_total_hours").value = btn.dataset.hours || "";

                    selectedSubjects = btn.dataset.subjects
                        ? btn.dataset.subjects.split(",").filter(Boolean)
                        : [];

                    renderSubjects();
                    modal.classList.remove("hidden");
                };
            });

            cancel.onclick = () => modal.classList.add("hidden");
        })();

    });
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const searchInput = document.getElementById("addFacultySubjectSearch");
        const tableBody = document.getElementById("addFacultySubjectTable");

        if (!searchInput || !tableBody) return;

        const rows = tableBody.querySelectorAll("tr");

        searchInput.addEventListener("input", () => {
            const keyword = searchInput.value.toLowerCase().trim();

            rows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(keyword)
                    ? ""
                    : "none";
            });
        });
    });
    </script>

    <!-- 
    

    <script src="../../assets/js/manage/tabs/subject.js"></script>
    <script src="../../assets/js/manage/tabs/course.js"></script>
    <script src="../../assets/js/manage/tabs/faculty.js"></script>
    <script src="../../assets/js/manage/tabs/section.js"></script>
    <script src="../../assets/js/manage/tabs/room.js"></script>

    <script src="../../assets/js/manage/modals/add-subject.js"></script>
    <script src="../../assets/js/manage/modals/edit-subject.js"></script>
    

    
    -->
    <script src="../../assets/js/core/helpers.js"></script>
    <script src="../../assets/js/core/modal-base.js"></script>
    <script src="../../assets/js/core/tabs.js"></script>

    <script src="../../assets/js/manage/modals/edit-faculty.js"></script>
    <script src="../../assets/js/manage/modals/assign-course-subject.js"></script>
    <script src="../../assets/js/manage/manage-init.js"></script> 

    </body>
</html>