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

        <?php include __DIR__ . '/tabs/tab-subject.php'; ?>
        <?php include __DIR__ . '/tabs/tab-course.php'; ?>
        <?php include __DIR__ . '/tabs/tab-faculty.php'; ?>
        <?php include __DIR__ . '/tabs/tab-section.php'; ?>
        <?php include __DIR__ . '/tabs/tab-room.php'; ?>

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