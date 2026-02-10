<?php
include __DIR__ . '/../connection/connection.php';

$faculty_name = trim($_GET['faculty_name'] ?? '');

if ($faculty_name === '') {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT subject FROM manage_faculty_subject WHERE faculty_name = ?");
$stmt->bind_param("s", $faculty_name);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row['subject'];
}

echo json_encode($subjects);
?>
