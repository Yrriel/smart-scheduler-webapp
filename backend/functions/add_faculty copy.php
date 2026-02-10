<?php
session_start();
include "connection.php";

// Get POST values
$faculty_name = trim($_POST['faculty_name'] ?? '');
$employment_type = trim($_POST['employment_type'] ?? '');
$current_status = trim($_POST['current_status'] ?? '');
$total_hours_per_week = trim($_POST['total_hours_per_week'] ?? '');

// These now come automatically
$preferred_days = $_POST['preferred_day'] ?? [];
$subjects = isset($_POST['subjects']) ? explode(",", $_POST['subjects']) : [];

// Validate required fields
if (!$faculty_name || !$employment_type || !$current_status || !$total_hours_per_week) {
    echo "All fields are required.";
    exit;
}

// ----------------------------------------------------
// AUTO-CALCULATE total_units FROM manage_subjects
// ----------------------------------------------------
$total_units = 0;

if (!empty($subjects)) {

    // Create "?, ?, ?, ..." for SQL IN()
    $placeholders = implode(",", array_fill(0, count($subjects), "?"));

    $sql = "SELECT units FROM manage_subjects WHERE subject IN ($placeholders)";
    $stmtUnits = $conn->prepare($sql);

    // Binding parameters dynamically
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
// INSERT into manage_faculty ( NOW using auto total_units )
// ----------------------------------------------------
$stmt = $conn->prepare("
    INSERT INTO manage_faculty 
    (faculty_name, employment_type, current_status, total_units, total_hours_per_week) 
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param("sssss", 
    $faculty_name, 
    $employment_type, 
    $current_status, 
    $total_units,                  // AUTO CALCULATED VALUE
    $total_hours_per_week
);

// Check execution
if ($stmt->execute()) {

    // Insert preferred days
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

    // Insert subjects
    if (!empty($subjects)) {
        $insertSubject = $conn->prepare("
            INSERT INTO manage_faculty_days (faculty_name, subject)
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
