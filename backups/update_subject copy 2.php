<?php
include __DIR__ . '/../connection/connection.php';

/* =========================
   FETCH INPUT
========================= */

$id           = (int) ($_POST['id'] ?? 0);
$subject      = trim($_POST['subject_code'] ?? '');
$subject_name = trim($_POST['subject_name'] ?? '');
$short_name   = trim($_POST['short_name'] ?? '');
$typeInput    = trim($_POST['type'] ?? 'Lecture');
$units        = (int) ($_POST['units'] ?? 0);
$hours        = trim($_POST['hours_per_week'] ?? '');
$status       = trim($_POST['status'] ?? '');

/* =========================
   NORMALIZE TYPE
========================= */

$hasLab = ($typeInput === 'Laboratory');
$lectureType = 'Lecture';

/* =========================
   VALIDATION
========================= */

if (!$id || !$subject || !$subject_name || !$short_name || !$units || !$hours || !$status) {
    die("❌ Missing required fields.");
}

/* =========================
   START TRANSACTION
========================= */

$conn->begin_transaction();

try {

    /* =========================
       UPDATE LECTURE (ALWAYS)
    ========================= */

    $stmt = $conn->prepare("
        UPDATE manage_subjects
        SET
            subject_name = ?,
            short_name   = ?,
            type         = ?,
            units        = ?,
            hours        = ?,
            status       = ?
        WHERE id = ? AND subject = ?
    ");

    $stmt->bind_param(
        "sssissis",
        $subject_name,
        $short_name,
        $lectureType,
        $units,
        $hours,
        $status,
        $id,
        $subject
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to update lecture subject.");
    }

    $stmt->close();

    /* =========================
       CHECK IF LAB EXISTS
    ========================= */

    $checkLab = $conn->prepare("
        SELECT id FROM manage_subjects_laboratory
        WHERE subject = ?
    ");
    $checkLab->bind_param("s", $subject);
    $checkLab->execute();
    $labExists = $checkLab->get_result()->num_rows > 0;
    $checkLab->close();

    /* =========================
       HANDLE LAB LOGIC
    ========================= */

    if ($hasLab) {

        if ($labExists) {
            // UPDATE LAB
            $updateLab = $conn->prepare("
                UPDATE manage_subjects_laboratory
                SET
                    subject_name = ?,
                    short_name   = ?,
                    type         = 'Laboratory',
                    units        = ?,
                    hours        = ?,
                    status       = ?
                WHERE subject = ?
            ");

            $updateLab->bind_param(
                "ssisss",
                $subject_name,
                $short_name,
                $units,
                $hours,
                $status,
                $subject
            );

            if (!$updateLab->execute()) {
                throw new Exception("Failed to update laboratory subject.");
            }

            $updateLab->close();

        } else {
            // INSERT LAB
            $insertLab = $conn->prepare("
                INSERT INTO manage_subjects_laboratory
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
                throw new Exception("Failed to insert laboratory subject.");
            }

            $insertLab->close();
        }

    } else {
        // Lecture only → delete lab if exists
        if ($labExists) {
            $deleteLab = $conn->prepare("
                DELETE FROM manage_subjects_laboratory
                WHERE subject = ?
            ");
            $deleteLab->bind_param("s", $subject);
            $deleteLab->execute();
            $deleteLab->close();
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
    die("❌ Update failed: " . $e->getMessage());

} finally {
    $conn->close();
}
