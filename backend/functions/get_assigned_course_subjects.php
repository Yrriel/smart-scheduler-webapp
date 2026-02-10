<?php
header('Content-Type: application/json');

include __DIR__ . '/../connection/connection.php';

$course = isset($_GET['section_course']) ? trim($_GET['section_course']) : '';
$year   = isset($_GET['year_level']) ? trim($_GET['year_level']) : '';

if ($course === '' || $year === '') {
    echo json_encode(['assigned_subjects' => []]);
    exit;
}

$assigned = [];
$stmt = $conn->prepare("SELECT assign_subject FROM assign_section_subjects WHERE section_course = ? AND section_year = ?");
$stmt->bind_param('ss', $course, $year);
if ($stmt->execute()) {
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $assigned[] = $row['assign_subject'];
    }
}
$stmt->close();

echo json_encode(['assigned_subjects' => $assigned]);