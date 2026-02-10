<?php
session_start();
include __DIR__ . '/../connection/connection.php';

/* =========================
   INPUT
========================= */
$subject       = trim($_POST['subject_code'] ?? '');
$subject_name  = trim($_POST['subject_name'] ?? '');
$short_name    = trim($_POST['short_name'] ?? '');
$typeInput     = trim($_POST['type'] ?? 'Lecture');
$units         = (int) ($_POST['units'] ?? 0);
$hours         = trim($_POST['hours_per_week'] ?? '');
$status        = trim($_POST['status'] ?? '');

$hasLab = ($typeInput === 'Laboratory');

if (
    !$subject ||
    !$subject_name ||
    !$short_name ||
    !$units ||
    !$hours ||
    !$status
) {
    die("❌ All fields are required.");
}

/* =========================
   START TRANSACTION
========================= */
$conn->begin_transaction();

try {

    /* =========================
       INSERT LECTURE (ALWAYS)
    ========================= */
    $checkLecture = $conn->prepare("
        SELECT id FROM manage_subjects
        WHERE subject = ? AND type = 'Lecture'
    ");
    $checkLecture->bind_param("s", $subject);
    $checkLecture->execute();
    $lectureExists = $checkLecture->get_result()->num_rows > 0;
    $checkLecture->close();

    if (!$lectureExists) {
        $insertLecture = $conn->prepare("
            INSERT INTO manage_subjects
            (subject, subject_name, short_name, type, units, hours, status)
            VALUES (?, ?, ?, 'Lecture', ?, ?, ?)
        ");
        $insertLecture->bind_param(
            "sssiss",
            $subject,
            $subject_name,
            $short_name,
            $units,
            $hours,
            $status
        );
        if (!$insertLecture->execute()) {
            throw new Exception("Lecture insert failed.");
        }
        $insertLecture->close();
    }

    /* =========================
       INSERT LAB (OPTIONAL)
    ========================= */
    if ($hasLab) {

        $checkLab = $conn->prepare("
            SELECT id FROM manage_subjects
            WHERE subject = ? AND type = 'Laboratory'
        ");
        $checkLab->bind_param("s", $subject);
        $checkLab->execute();
        $labExists = $checkLab->get_result()->num_rows > 0;
        $checkLab->close();

        if (!$labExists) {
            $insertLab = $conn->prepare("
                INSERT INTO manage_subjects
                (subject, subject_name, short_name, type, units, hours, status)
                VALUES (?, ?, ?, 'Laboratory', ?, ?, ?)
            ");
            $insertLab->bind_param(
                "sssiss",
                $subject,
                $subject_name,
                $short_name,
                $units,
                $hours,
                $status
            );
            if (!$insertLab->execute()) {
                throw new Exception("Laboratory insert failed.");
            }
            $insertLab->close();
        }
    }

    /* =========================
       COMMIT
    ========================= */
    $conn->commit();

    header("Location: ../../frontend/page_manage/manage.php?tab=subject");
    exit;

} catch (Exception $e) {

    $conn->rollback();
    die("❌ Error adding subject: " . $e->getMessage());
}

$conn->close();
