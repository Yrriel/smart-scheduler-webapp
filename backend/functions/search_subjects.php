<?php
include __DIR__ . '/../connection/connection.php';

$q = trim($_GET['q'] ?? '');

if ($q === '') {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT subject, subject_name FROM manage_subjects WHERE subject LIKE ? OR subject_name LIKE ? LIMIT 10");
$search = "%$q%";
$stmt->bind_param("ss", $search, $search);
$stmt->execute();

$result = $stmt->get_result();
$subjects = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($subjects);
?>
