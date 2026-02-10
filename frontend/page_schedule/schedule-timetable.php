<?php
include __DIR__ . '/../../backend/connection/connection.php';

// Determine timetable mode based on view
$mode = $_GET['view'] ?? 'day'; 

/* -------------------------
   BUILD COLUMNS
------------------------- */

$columns = [];

if ($mode === 'day') {
    // Original behavior: columns = rooms
    $res = $conn->query("
        SELECT room_name 
        FROM manage_rooms 
        ORDER BY room_name ASC
    ");
    while ($r = $res->fetch_assoc()) {
        $columns[] = $r['room_name'];
    }

} else {
    // Faculty / Section / Room views: columns = days
    $columns = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
}

/* -------------------------
   BUILD TIME SLOTS (ROWS)
------------------------- */
$slots = [];
$slotMap = [];

$start = strtotime('07:00');
$end   = strtotime('17:30');

while ($start < $end) {
    $next = $start + 30 * 60;

    $label = date('g:i A', $start) . ' - ' . date('g:i A', $next);
    $key   = date('H:i', $start);

    $slots[] = [
        'key' => $key,
        'label' => $label
    ];

    $slotMap[$key] = [];

    $start = $next;
}

/* -------------------------
   FETCH DAY SCHEDULE
------------------------- */
// $sql = "SELECT * FROM generated_schedule $filterSql";
$sql = "
    SELECT 
        gs.*,
        ms.short_name
    FROM generated_schedule gs
    LEFT JOIN manage_subjects ms
        ON gs.subject = ms.subject
    $filterSql
";

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("s", count($params)), ...$params);
$stmt->execute();
$res = $stmt->get_result();

$schedules = [];
while ($row = $res->fetch_assoc()) {
    $schedules[] = $row;
}

/* -------------------------
   MAP SCHEDULE INTO SLOTS
------------------------- */
foreach ($schedules as $s) {

    $startMin = strtotime($s['time_start']);
    $endMin   = strtotime($s['time_end']);
    $rowspan  = ($endMin - $startMin) / (30 * 60);

    $slotKey = date('H:i', $startMin);

    $columnKey = ($mode === 'day') ? $s['room'] : $s['day'];

    $slotMap[$slotKey][$columnKey] = [
        'rowspan' => $rowspan,
        'data' => $s
    ];

}
?>
<div class="schedule-wrapper">
    <div class="schedule-grid" style="--col-count: <?= count($columns) ?>;">
        <!-- GRID HEADER ROW -->
        <div class="grid-header time-header">Time</div>

        <?php foreach ($columns as $col): ?>
            <div class="grid-header"><?= htmlspecialchars($col) ?></div>
        <?php endforeach; ?>

        <!-- =========================
            GRID BODY
        ========================= -->

        <?php
        // Build an index map for slot keys → grid row number
        $slotIndexMap = [];
        foreach ($slots as $i => $slot) {
            $slotIndexMap[$slot['key']] = $i;
        }
        ?>

        <?php foreach ($slots as $i => $slot): ?>

            <!-- TIME COLUMN -->
            <div class="time-col" style="grid-row: <?= $i + 2 ?>;">
                <?= htmlspecialchars($slot['label']) ?>
            </div>

            <!-- EMPTY GRID CELLS -->
            <?php foreach ($columns as $col): ?>
                <div class="cell" style="grid-row: <?= $i + 2 ?>;"></div>
            <?php endforeach; ?>

        <?php endforeach; ?>


        <!-- =========================
            SCHEDULE EVENTS
        ========================= -->

        <?php foreach ($schedules as $data): ?>

        <?php
            // Determine start & end grid rows
            $startKey = date('H:i', strtotime($data['time_start']));
            $endKey   = date('H:i', strtotime($data['time_end']));

            if (!isset($slotIndexMap[$startKey]) || !isset($slotIndexMap[$endKey])) {
                continue;
            }

            $rowStart = $slotIndexMap[$startKey];
            $rowEnd   = $slotIndexMap[$endKey];

            // Determine column index
            $columnValue = ($mode === 'day') ? $data['room'] : $data['day'];
            $colIndex = array_search($columnValue, $columns, true);

            if ($colIndex === false || $colIndex >= count($columns)) {
                continue;
            }
        ?>

        <div class="event"
            style="
                grid-column: <?= $colIndex + 2 ?>;
                grid-row: <?= $rowStart + 2 ?> / <?= $rowEnd + 2 ?>;
            ">

            <div class="subject">
                <?= htmlspecialchars($data['subject']) ?>
                <?php if (!empty($data['short_name'])): ?>
                    (<?= htmlspecialchars($data['short_name']) ?>)
                <?php endif; ?>
            </div>

            <?php if ($mode === 'day'): ?>
                <div><?= htmlspecialchars($data['section']) ?></div>
                <div class="faculty"><?= htmlspecialchars($data['faculty']) ?></div>

            <?php elseif ($mode === 'faculty'): ?>
                <div><?= htmlspecialchars($data['section']) ?></div>
                <div class="faculty"><?= htmlspecialchars($data['room']) ?></div>

            <?php elseif ($mode === 'section'): ?>
                <div><?= htmlspecialchars($data['faculty']) ?></div>
                <div class="faculty"><?= htmlspecialchars($data['room']) ?></div>

            <?php elseif ($mode === 'room'): ?>
                <div><?= htmlspecialchars($data['section']) ?></div>
                <div class="faculty"><?= htmlspecialchars($data['faculty']) ?></div>
            <?php endif; ?>

        </div>

        <?php endforeach; ?>

    </div>    
</div>

