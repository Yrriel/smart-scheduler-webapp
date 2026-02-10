<?php
include __DIR__ . '/../../backend/connection/connection.php';

/* -------------------------
   BUILD TIME SLOTS
------------------------- */
$slots = [];
$start = strtotime('07:00');
$end   = strtotime('17:30');
while ($start < $end) {
    $next = $start + 30*60;
    $slots[] = date('g:i A', $start).' - '.date('g:i A', $next);
    $start = $next;
}

/* -------------------------
   FETCH FILTERED SCHEDULE
------------------------- */
$sql = "SELECT * FROM generated_schedule $filterSql ORDER BY time_start";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("s", count($params)), ...$params);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}
?>
