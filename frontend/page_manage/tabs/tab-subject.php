<?php
// tab-subject.php
// USES: $subjects, $page, $totalPages, $search (already included)
?>

        <div class="table-container">
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
                        <!-- <td>
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
                        </td> -->
                        <td>
                        <?php if ($row['type'] === 'Lecture'): ?>
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
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/>
                                </svg>
                            </button>
                        <?php endif; ?>
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
