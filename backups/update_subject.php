<?php
include __DIR__ . '/../connection/connection.php';

$id = $_POST['id'] ?? '';
$subject = trim($_POST['subject_code'] ?? '');
$subject_name = trim($_POST['subject_name'] ?? '');
$short_name = trim($_POST['short_name'] ?? '');
$type = trim($_POST['type'] ?? '');
if (!in_array($type, ['Lecture', 'Laboratory'])) {
    $type = 'Lecture'; // default fallback
}
$units = trim($_POST['units'] ?? '');
$hours = trim($_POST['hours_per_week'] ?? '');
$status = trim($_POST['status'] ?? '');

if (!$id || !$subject || !$subject_name) {
    echo "Missing fields!";
    exit;
}

$stmt = $conn->prepare("UPDATE manage_subjects 
    SET subject=?, subject_name=?, short_name=?, type=?, units=?, hours=?, status=? 
    WHERE id=?");
$stmt->bind_param("ssssissi", $subject, $subject_name, $short_name, $type, $units, $hours, $status, $id);


if ($stmt->execute()) {
    header("Location: ../../frontend/page_manage/manage.php?tab=subject");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
