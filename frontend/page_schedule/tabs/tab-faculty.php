<?php
$limit = 5;
$page = isset($_GET['faculty_page']) ? max(1, (int)$_GET['faculty_page']) : 1;
$start = ($page - 1) * $limit;

$search = $_GET['faculty_search'] ?? '';
$searchParam = "%$search%";

/* total rows */
if ($search !== '') {
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT faculty) AS total
        FROM generated_schedule
        WHERE faculty LIKE ?
    ");
    $stmt->bind_param("s", $searchParam);
} else {
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT faculty) AS total
        FROM generated_schedule
    ");
}
$stmt->execute();
$totalRows = $stmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
$stmt->close();

/* paginated data */
if ($search !== '') {
    $stmt = $conn->prepare("
        SELECT faculty, COUNT(*) AS total
        FROM generated_schedule
        WHERE faculty LIKE ?
        GROUP BY faculty
        LIMIT ?, ?
    ");
    $stmt->bind_param("sii", $searchParam, $start, $limit);
} else {
    $stmt = $conn->prepare("
        SELECT faculty, COUNT(*) AS total
        FROM generated_schedule
        GROUP BY faculty
        LIMIT ?, ?
    ");
    $stmt->bind_param("ii", $start, $limit);
}
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="table-container">
    <div class="table-header">
        <div class="header-text">
            <h2>Faculty Schedules</h2>
            <p>Schedules grouped by faculty</p>
        </div>
        <form class="search-box" method="get">
            <input type="hidden" name="tab" value="faculty">
            <input type="text" name="faculty_search"
                   placeholder="Search faculty"
                   value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Faculty</th>
                <th>Total Schedules</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr class="clickable-row"
                data-href="schedule-ui.php?tab=faculty&view=faculty&faculty=<?= urlencode($row['faculty']) ?>">
                <td><?= htmlspecialchars($row['faculty']) ?></td>
                <td><?= $row['total'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <a href="?tab=faculty&faculty_page=<?= max(1, $page-1) ?>&faculty_search=<?= urlencode($search) ?>">&laquo;</a>
        <?php for ($i=1;$i<=$totalPages;$i++): ?>
            <a href="?tab=faculty&faculty_page=<?= $i ?>&faculty_search=<?= urlencode($search) ?>"
               style="font-weight:<?= $i==$page?'bold':'normal' ?>">
               <?= $i ?>
            </a>
        <?php endfor; ?>
        <a href="?tab=faculty&faculty_page=<?= min($totalPages,$page+1) ?>&faculty_search=<?= urlencode($search) ?>">&raquo;</a>
    </div>
    <?php endif; ?>
</div>
