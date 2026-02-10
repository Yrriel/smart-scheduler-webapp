<?php
$limit = 5;
$page = isset($_GET['room_page']) ? max(1, (int)$_GET['room_page']) : 1;
$start = ($page - 1) * $limit;

$search = $_GET['room_search'] ?? '';
$searchParam = "%$search%";

/* total */
if ($search !== '') {
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT room) AS total
        FROM generated_schedule
        WHERE room LIKE ?
    ");
    $stmt->bind_param("s", $searchParam);
} else {
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT room) AS total
        FROM generated_schedule
    ");
}
$stmt->execute();
$totalRows = $stmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
$stmt->close();

/* data */
if ($search !== '') {
    $stmt = $conn->prepare("
        SELECT room, COUNT(*) AS total
        FROM generated_schedule
        WHERE room LIKE ?
        GROUP BY room
        LIMIT ?, ?
    ");
    $stmt->bind_param("sii", $searchParam, $start, $limit);
} else {
    $stmt = $conn->prepare("
        SELECT room, COUNT(*) AS total
        FROM generated_schedule
        GROUP BY room
        LIMIT ?, ?
    ");
    $stmt->bind_param("ii", $start, $limit);
}
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="table-container">
    <div class="content-header">
        <div class="header-text">
            <h2>Room Schedules</h2>
            <p>Schedules grouped by room</p>
        </div>
        <form class="search-box" method="get">
            <input type="hidden" name="tab" value="room">
            <input type="text" name="room_search"
                   placeholder="Search room"
                   value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Room</th>
                <th>Total Schedules</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr class="clickable-row"
                data-href="schedule-ui.php?tab=room&view=room&room=<?= urlencode($row['room']) ?>">
                <td><?= htmlspecialchars($row['room']) ?></td>
                <td><?= $row['total'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <a href="?tab=room&room_page=<?= max(1, $page-1) ?>&room_search=<?= urlencode($search) ?>"
        style="color: gray;">&laquo;</a>

        <?php
        $maxPages = 5;
        $startPage = max(1, $page - floor($maxPages / 2));
        $endPage = min($totalPages, $startPage + $maxPages - 1);
        $startPage = max(1, $endPage - $maxPages + 1);
        ?>

        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="?tab=room&room_page=<?= $i ?>&room_search=<?= urlencode($search) ?>"
            style="color: <?= ($i == $page) ? 'black' : 'gray' ?>;
                    font-weight: <?= ($i == $page) ? 'bold' : 'normal' ?>;">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <a href="?tab=room&room_page=<?= min($totalPages, $page+1) ?>&room_search=<?= urlencode($search) ?>"
        style="color: gray;">&raquo;</a>
    </div>
    <?php endif; ?>

</div>
