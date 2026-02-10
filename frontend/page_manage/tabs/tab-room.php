<div class="table-container">
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
                    <form class="search-box" method="get" action="">
                        <input type="hidden" name="tab" value="room">

                        <input
                            type="text"
                            name="room_search"
                            placeholder="Search for Room"
                            value="<?= htmlspecialchars($_GET['room_search'] ?? '') ?>"
                        >
                    </form>

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

                <a href="?tab=room&room_page=<?= max(1, $room_page-1) ?>&room_search=<?= urlencode($room_search) ?>" style="color: gray; text-decoration: none;">&laquo;</a>

                <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?tab=room&room_page=<?= $i ?>&room_search=<?= urlencode($room_search) ?>"
                        style="color: <?= ($i == $room_page) ? 'black' : 'gray' ?>;
                            font-weight: <?= ($i == $room_page) ? 'bold' : 'normal' ?>;
                            margin:0 5px; text-decoration:none;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <a href="?tab=room&room_page=<?= min($room_totalPages, $room_page+1) ?>&room_search=<?= urlencode($room_search) ?>" style="color: gray; text-decoration: none;">&raquo;</a>

                <?php endif; ?>
            </div>

        </div>