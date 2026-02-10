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

<table class="schedule-grid">
    <thead>
        <tr>
            <th class="time-cell">Time</th>
            <?php foreach ($columns as $col): ?>
                <th><?= htmlspecialchars($col) ?></th>
            <?php endforeach; ?>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($slots as $slot): ?>
            <tr>
                <td class="time-cell"><?= htmlspecialchars($slot['label']) ?></td>

                <?php foreach ($columns as $col): ?>

                    <?php
                    // Skip cells covered by rowspan
                    if (
                        isset($slotMap[$slot['key']]['__skip__'][$col])
                    ) continue;

                    if (isset($slotMap[$slot['key']][$col])):
                        $entry = $slotMap[$slot['key']][$col];
                        $data  = $entry['data'];

                        // Mark future rows as skipped
                        for ($i = 1; $i < $entry['rowspan']; $i++) {
                            $skipTime = date(
                                'H:i',
                                strtotime($slot['key']) + (30 * 60 * $i)
                            );
                            $slotMap[$skipTime]['__skip__'][$col] = true;
                        }
                    ?>
                        <td rowspan="<?= $entry['rowspan'] ?>" style="background-color: #4a6cf7;">
                            <div class="class-card">
                            <?php if ($mode === 'day'): ?>
                                <strong><?= htmlspecialchars($data['subject']) ?>
                                <?php if (!empty($data['short_name'])): ?>
                                    (<?= htmlspecialchars($data['short_name']) ?>)
                                <?php endif; ?>
                            </strong><br>
                                <?= htmlspecialchars($data['section']) ?><br>
                                <small><?= htmlspecialchars($data['faculty']) ?></small>

                            <?php elseif ($mode === 'faculty'): ?>
                                <strong><?= htmlspecialchars($data['subject']) ?>
                                <?php if (!empty($data['short_name'])): ?>
                                    (<?= htmlspecialchars($data['short_name']) ?>)
                                <?php endif; ?>
                            </strong><br>
                                <?= htmlspecialchars($data['section']) ?><br>
                                <small><?= htmlspecialchars($data['room']) ?></small>

                            <?php elseif ($mode === 'section'): ?>
                                <strong><?= htmlspecialchars($data['subject']) ?>
                                <?php if (!empty($data['short_name'])): ?>
                                    (<?= htmlspecialchars($data['short_name']) ?>)
                                <?php endif; ?>
                            </strong><br>
                                <?= htmlspecialchars($data['faculty']) ?><br>
                                <small><?= htmlspecialchars($data['room']) ?></small>

                            <?php elseif ($mode === 'room'): ?>
                                <strong><?= htmlspecialchars($data['subject']) ?>
                                <?php if (!empty($data['short_name'])): ?>
                                    (<?= htmlspecialchars($data['short_name']) ?>)
                                <?php endif; ?>
                            </strong><br>
                                <?= htmlspecialchars($data['section']) ?><br>
                                <small><?= htmlspecialchars($data['faculty']) ?></small>
                            <?php endif; ?>
                            </div>

                        </td>
                    <?php else: ?>
                        <td></td>
                    <?php endif; ?>

                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
