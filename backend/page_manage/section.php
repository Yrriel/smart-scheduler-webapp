<?php

// =======================
// Pagination settings for sections
// =======================
$section_limit = 5;
$section_page = isset($_GET['section_page']) ? (int)$_GET['section_page'] : 1;
$section_start = ($section_page - 1) * $section_limit;

// Search filter for sections
$section_search = isset($_GET['section_search']) ? trim($_GET['section_search']) : '';
$section_where = "";

if ($section_search !== '') {
    $section_where = "WHERE section_name LIKE ?";
    $section_searchParam = "%$section_search%";
}

// Get total rows for sections
if ($section_search !== '') {
    $stmtTotalSection = $conn->prepare("SELECT COUNT(*) as total FROM manage_sections $section_where");
    $stmtTotalSection->bind_param("s", $section_searchParam);
} else {
    $stmtTotalSection = $conn->prepare("SELECT COUNT(*) as total FROM manage_sections");
}

$stmtTotalSection->execute();
$resultTotalSection = $stmtTotalSection->get_result();
$section_totalRows = $resultTotalSection->fetch_assoc()['total'];
$section_totalPages = ceil($section_totalRows / $section_limit);
$stmtTotalSection->close();

// Fetch rows for sections current page
if ($section_search !== '') {
    $stmtSection = $conn->prepare(
        "SELECT * FROM manage_sections $section_where LIMIT ?, ?"
    );
    $stmtSection->bind_param(
        "sii",
        $section_searchParam,
        $section_start,
        $section_limit
    );
}
 else {
    $stmtSection = $conn->prepare("SELECT * FROM manage_sections LIMIT ?, ?");
    $stmtSection->bind_param("ii", $section_start, $section_limit);
}

$stmtSection->execute();
$resultSection = $stmtSection->get_result();
$section_rows = $resultSection->fetch_all(MYSQLI_ASSOC);
$stmtSection->close();


?>