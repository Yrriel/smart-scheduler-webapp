<?php
include __DIR__ . '/../connection/connection.php';

$id = $_POST['id'] ?? '';
$subject = trim($_POST['subject_code'] ?? '');
$subject_name = trim($_POST['subject_name'] ?? '');
$short_name = trim($_POST['short_name'] ?? '');
$type = trim($_POST['type'] ?? '');


$units = trim($_POST['units'] ?? '');
$hours = trim($_POST['hours_per_week'] ?? '');
$status = trim($_POST['status'] ?? '');

$hasLab = false;

if (!in_array($type, ['Lecture', 'Lecture with Laboratory'])) {
    $type = 'Lecture'; // default fallback
}
if($type == "Lecture with Laboratory"){
    $hasLab = true;
}

if (!$id || !$subject || !$subject_name) {
    echo "Missing fields!";
    exit;
}

if($hasLab){
    $type = "Laboratory";

    $stmt = $conn->prepare("SELECT * FROM manage_subjects_laboratory
        WHERE
            subject = ? AND
            subject_name = ? AND
            short_name = ? AND
            units = ? AND
            hours = ? AND
            status = ? ;
");
    $stmt->bind_param(
    "sssiss",
    $subject,
    $subject_name,
    $short_name,
    $units,
    $hours,
    $status
);

    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0 ){
        $stmt = $conn->prepare("UPDATE manage_subjects_laboratory
        SET
            subject = ?,
            subject_name = ?,
            short_name = ?,
            type = ?,
            units = ?,
            hours = ?,
            status = ?
        WHERE
            subject = ? AND
            subject_name = ? AND
            short_name = ? AND
            units = ? AND
            hours = ? AND
            status = ? ;
");
    }else{
        $stmt = $conn->prepare("INSERT INTO manage_subjects_laboratory (subject, subject_name, short_name, type, units, hours, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiss", $subject, $subject_name, $short_name, $type, $units, $hours, $status);
        $hasLab = false;
    }


        

    
}

$stmt = $conn->prepare("UPDATE manage_subjects 
    SET subject=?, subject_name=?, short_name=?, type=?, units=?, hours=?, status=? 
    WHERE id=?");
$stmt->bind_param("ssssissi", $subject, $subject_name, $short_name, $type, $units, $hours, $status, $id);

$stmt->execute();



if ($stmt->execute()) {
    header("Location: ../../frontend/page_manage/manage.php?tab=subject");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
