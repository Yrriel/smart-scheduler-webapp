<div class="table-container">
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
                    <form class="search-box" method="get" action="">
                        <input type="hidden" name="tab" value="faculty">

                        <input
                            type="text"
                            name="faculty_search"
                            placeholder="Search for Faculty"
                            value="<?= htmlspecialchars($_GET['faculty_search'] ?? '') ?>"
                        >
                    </form>

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

                <a href="?tab=faculty&faculty_page=<?= max(1, $faculty_page-1) ?>&faculty_search=<?= urlencode($faculty_search) ?>" style="color: gray;">&laquo;</a>

                <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?tab=faculty&faculty_page=<?= $i ?>&faculty_search=<?= urlencode($faculty_search) ?>" 
                    style="color: <?= ($i == $faculty_page) ? 'black' : 'gray' ?>; font-weight: <?= ($i == $faculty_page) ? 'bold' : 'normal' ?>;">
                    <?= $i ?>
                    </a>
                <?php endfor; ?>

                <a href="?tab=faculty&faculty_page=<?= min($faculty_totalPages, $faculty_page+1) ?>&faculty_search=<?= urlencode($faculty_search) ?>" style="color: gray;">&raquo;</a>
            </div>

        </div>