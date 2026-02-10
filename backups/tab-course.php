<div class="table-container">
           
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
                                data-units="<?php // = htmlspecialchars($course['units']) ?>"
                                data-hours="<?php //= htmlspecialchars($course['hours']) ?>"
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
                <a href="?tab=course&courses_page=<?= max(1, $courses_page-1) ?>&courses_search=<?= urlencode($courses_search) ?>" 
                style="color: gray;">&laquo;</a>

                <!-- Page Numbers -->
                <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?tab=course&courses_page=<?= $i ?>&courses_search=<?= urlencode($courses_search) ?>" 
                    style="color: <?= ($i == $courses_page) ? 'black' : 'gray' ?>; 
                            font-weight: <?= ($i == $courses_page) ? 'bold' : 'normal' ?>;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <!-- Next Arrow -->
                <a href="?tab=course&courses_page=<?= min($courses_totalPages, $courses_page+1) ?>&courses_search=<?= urlencode($courses_search) ?>" 
                style="color: gray;">&raquo;</a>
            </div>


        </div>