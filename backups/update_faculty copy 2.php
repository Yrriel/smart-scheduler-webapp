<?php
include "connection.php";

// POST values
$id = $_POST['id'] ?? '';
$faculty_name = trim($_POST['faculty_name'] ?? '');
$employment_type = trim($_POST['employment_type'] ?? '');
$current_status = trim($_POST['current_status'] ?? '');
$total_hours_per_week = trim($_POST['total_hours_per_week'] ?? '');

$preferred_days = $_POST['preferred_day'] ?? [];
$subjects = isset($_POST['subjects']) ? explode(",", $_POST['subjects']) : [];

// Validate
if (!$id || !$faculty_name) {
    echo "Missing fields!";
    exit;
}

// ----------------------------------------------------
// AUTO-CALCULATE TOTAL UNITS FROM SELECTED SUBJECTS
// ----------------------------------------------------
$total_units = 0;

if (!empty($subjects)) {

    $placeholders = implode(",", array_fill(0, count($subjects), "?"));
    $sql = "SELECT units FROM manage_subject WHERE subject IN ($placeholders)";
    $stmtUnits = $conn->prepare($sql);

    $types = str_repeat("s", count($subjects));
    $stmtUnits->bind_param($types, ...$subjects);

    $stmtUnits->execute();
    $resultUnits = $stmtUnits->get_result();

    while ($row = $resultUnits->fetch_assoc()) {
        $total_units += (int)$row['units'];
    }

    $stmtUnits->close();
}

// ----------------------------------------------------
// UPDATE MAIN manage_faculty RECORD
// ----------------------------------------------------
$stmt = $conn->prepare("
    UPDATE manage_faculty 
    SET faculty_name=?, employment_type=?, current_status=?, total_units=?, total_hours_per_week=?
    WHERE id=?
");

$stmt->bind_param("sssssi", 
    $faculty_name, 
    $employment_type, 
    $current_status, 
    $total_units, 
    $total_hours_per_week, 
    $id
);

// If update successful → update related tables
if ($stmt->execute()) {

    // ----------------------------------------------------
    // UPDATE DAYS (delete old → insert new)
    // ----------------------------------------------------
    $conn->query("DELETE FROM manage_faculty_days WHERE faculty_name = '$faculty_name' AND day IS NOT NULL");

    if (!empty($preferred_days)) {
        $insertDay = $conn->prepare("
            INSERT INTO manage_faculty_days (faculty_name, day)
            VALUES (?, ?)
        ");

        foreach ($preferred_days as $day) {
            $insertDay->bind_param("ss", $faculty_name, $day);
            $insertDay->execute();
        }

        $insertDay->close();
    }

    // ----------------------------------------------------
    // UPDATE SUBJECTS (delete old → insert new)
    // ----------------------------------------------------
    $conn->query("DELETE FROM manage_faculty_subject WHERE faculty_name = '$faculty_name'");

    if (!empty($subjects)) {
        $insertSubject = $conn->prepare("
            INSERT INTO manage_faculty_subject (faculty_name, subject)
            VALUES (?, ?)
        ");

        foreach ($subjects as $subj) {
            if (trim($subj) !== "") {
                $insertSubject->bind_param("ss", $faculty_name, $subj);
                $insertSubject->execute();
            }
        }

        $insertSubject->close();
    }

    header("Location: manage.php");
    exit;

} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
