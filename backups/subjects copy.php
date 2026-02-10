<?php
include 'connection.php'; 

// =======================
// BACKEND LOGIC
// =======================

// Pagination settings
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = "";
$params = [];

if ($search !== '') {
    $where = "WHERE subject LIKE ? OR subject_name LIKE ? OR short_name LIKE ?";
    $searchParam = "%$search%";
}

// Get total rows for pagination
if ($search !== '') {
    $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM manage_subjects $where");
    $stmtTotal->bind_param("sss", $searchParam, $searchParam, $searchParam);
} else {
    $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM manage_subjects");
}

$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalRows = $resultTotal->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
$stmtTotal->close();

// Fetch rows for current page
if ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM manage_subjects $where LIMIT ?, ?");
    $stmt->bind_param("sssii", $searchParam, $searchParam, $searchParam, $start, $limit);
} else {
    $stmt = $conn->prepare("SELECT * FROM manage_subjects LIMIT ?, ?");
    $stmt->bind_param("ii", $start, $limit);
}

$stmt->execute();
$result = $stmt->get_result();
$subjects = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
