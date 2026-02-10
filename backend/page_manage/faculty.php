<?php

// =======================
// Pagination settings for faculty
// =======================
$faculty_limit = 5;
$faculty_page = isset($_GET['faculty_page']) ? (int)$_GET['faculty_page'] : 1;
$faculty_start = ($faculty_page - 1) * $faculty_limit;

// Search filter for faculty
$faculty_search = isset($_GET['faculty_search']) ? trim($_GET['faculty_search']) : '';
$faculty_where = "";

if ($faculty_search !== '') {
    $faculty_where = "WHERE faculty_name LIKE ? OR employment_type LIKE ? OR current_status LIKE ?";
    $faculty_searchParam = "%$faculty_search%";
}

// Get total rows for faculty
if ($faculty_search !== '') {
    $stmtTotalFaculty = $conn->prepare("SELECT COUNT(*) as total FROM manage_faculty $faculty_where");
    $stmtTotalFaculty->bind_param("sss", $faculty_searchParam, $faculty_searchParam, $faculty_searchParam);
} else {
    $stmtTotalFaculty = $conn->prepare("SELECT COUNT(*) as total FROM manage_faculty");
}

$stmtTotalFaculty->execute();
$resultTotalFaculty = $stmtTotalFaculty->get_result();
$faculty_totalRows = $resultTotalFaculty->fetch_assoc()['total'];
$faculty_totalPages = ceil($faculty_totalRows / $faculty_limit);
$stmtTotalFaculty->close();

// Fetch rows for faculty current page
if ($faculty_search !== '') {
    $stmtFaculty = $conn->prepare("SELECT * FROM manage_faculty $faculty_where LIMIT ?, ?");
    $stmtFaculty->bind_param("sssii", $faculty_searchParam, $faculty_searchParam, $faculty_searchParam, $faculty_start, $faculty_limit);
} else {
    $stmtFaculty = $conn->prepare("SELECT * FROM manage_faculty LIMIT ?, ?");
    $stmtFaculty->bind_param("ii", $faculty_start, $faculty_limit);
}

$stmtFaculty->execute();
$resultFaculty = $stmtFaculty->get_result();
$faculty_rows = $resultFaculty->fetch_all(MYSQLI_ASSOC);
$stmtFaculty->close();


?>