<?php
include __DIR__ . '/../connection/connection.php';

// =======================
// Pagination settings for courses
// =======================
$courses_limit = 5;
$courses_page = isset($_GET['courses_page']) ? (int)$_GET['courses_page'] : 1;
$courses_start = ($courses_page - 1) * $courses_limit;

// Search filter for courses
$courses_search = isset($_GET['courses_search']) ? trim($_GET['courses_search']) : '';
$courses_where = "";

if ($courses_search !== '') {
    $courses_where = "WHERE course LIKE ? OR course_name LIKE ? OR short_name LIKE ?";
    $courses_searchParam = "%$courses_search%";
}

// Get total rows for courses
if ($courses_search !== '') {
    $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM manage_courses $courses_where");
    $stmtTotal->bind_param("sss", $courses_searchParam, $courses_searchParam, $courses_searchParam);
} else {
    $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM manage_courses");
}

$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$courses_totalRows = $resultTotal->fetch_assoc()['total'];
$courses_totalPages = ceil($courses_totalRows / $courses_limit);
$stmtTotal->close();

// Fetch rows for courses current page
if ($courses_search !== '') {
    $stmt = $conn->prepare("SELECT * FROM manage_courses $courses_where LIMIT ?, ?");
    $stmt->bind_param("sssii", $courses_searchParam, $courses_searchParam, $courses_searchParam, $courses_start, $courses_limit);
} else {
    $stmt = $conn->prepare("SELECT * FROM manage_courses LIMIT ?, ?");
    $stmt->bind_param("ii", $courses_start, $courses_limit);
}

$stmt->execute();
$result = $stmt->get_result();
$courses_rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
