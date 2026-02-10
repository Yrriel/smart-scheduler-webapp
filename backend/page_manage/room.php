<?php

// =======================
// Pagination settings for rooms
// =======================
$room_limit = 5;
$room_page = isset($_GET['room_page']) ? (int)$_GET['room_page'] : 1;
$room_start = ($room_page - 1) * $room_limit;

// Search filter for rooms
$room_search = isset($_GET['room_search']) ? trim($_GET['room_search']) : '';
$room_where = "";

if ($room_search !== '') {
    $room_where = "WHERE room_name LIKE ?";
    $room_searchParam = "%$room_search%";
}

// Get total rows for rooms
if ($room_search !== '') {
    $stmtTotalRoom = $conn->prepare("SELECT COUNT(*) as total FROM manage_rooms $room_where");
    $stmtTotalRoom->bind_param("s", $room_searchParam);
} else {
    $stmtTotalRoom = $conn->prepare("SELECT COUNT(*) as total FROM manage_rooms");
}

$stmtTotalRoom->execute();
$resultTotalRoom = $stmtTotalRoom->get_result();
$room_totalRows = $resultTotalRoom->fetch_assoc()['total'];
$room_totalPages = ceil($room_totalRows / $room_limit);
$stmtTotalRoom->close();

// Fetch rows for rooms current page
if ($room_search !== '') {
    $stmtRoom = $conn->prepare("SELECT * FROM manage_rooms $room_where LIMIT ?, ?");
    $stmtRoom->bind_param("sii", $room_searchParam, $room_start, $room_limit);
} else {
    $stmtRoom = $conn->prepare("SELECT * FROM manage_rooms LIMIT ?, ?");
    $stmtRoom->bind_param("ii", $room_start, $room_limit);
}

$stmtRoom->execute();
$resultRoom = $stmtRoom->get_result();
$room_rows = $resultRoom->fetch_all(MYSQLI_ASSOC);
$stmtRoom->close();


?>