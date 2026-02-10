<div class="table-container">
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
                    <form class="search-box" method="GET" action="">
                        <input type="hidden" name="tab" value="section">

                        <input
                            type="text"
                            name="section_search"
                            placeholder="Search for Section"
                            value="<?= htmlspecialchars($_GET['section_search'] ?? '') ?>"
                        >
                    </form>
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

                <a href="?tab=section&section_page=<?= max(1, $section_page-1) ?>&section_search=<?= urlencode($section_search) ?>" style="color: gray; text-decoration: none;">&laquo;</a>

                <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?tab=section&section_page=<?= $i ?>&section_search=<?= urlencode($section_search) ?>"
                        style="color: <?= ($i == $section_page) ? 'black' : 'gray' ?>;
                            font-weight: <?= ($i == $section_page) ? 'bold' : 'normal' ?>;
                            margin:0 5px; text-decoration:none;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <a href="?tab=section&section_page=<?= min($section_totalPages, $section_page+1) ?>&section_search=<?= urlencode($section_search) ?>" style="color: gray; text-decoration: none;">&raquo;</a>

                <?php endif; ?>
            </div>

        </div>