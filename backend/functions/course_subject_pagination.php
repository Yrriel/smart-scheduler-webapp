<?php
include __DIR__ . '/../connection/connection.php';

$course_id = intval($_GET['course_id'] ?? 0);
$search = trim($_GET['search'] ?? '');
$page = intval($_GET['page'] ?? 1);
$showAssignedOnly = intval($_GET['assigned_only'] ?? 0);

$limit = 5;
$start = ($page - 1) * $limit;

// Get subjects already assigned to this course
$assignedQuery = $conn->prepare("SELECT subject FROM manage_course_subject WHERE course_id = ?");
$assignedQuery->bind_param("i", $course_id);
$assignedQuery->execute();
$resAssigned = $assignedQuery->get_result();

$assignedSubjects = [];
while ($r = $resAssigned->fetch_assoc()) {
    $assignedSubjects[] = $r['subject'];
}

// Build WHERE condition
$where = "";
$params = [];
$types = "";

if ($search !== "") {
    $where .= "WHERE subject LIKE ? OR subject_name LIKE ?";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam];
    $types = "ss";
}

if ($showAssignedOnly == 1) {
    if ($where == "") {
        $where = "WHERE subject IN ('" . implode("','", $assignedSubjects) . "')";
    } else {
        $where .= " AND subject IN ('" . implode("','", $assignedSubjects) . "')";
    }
}

// Count total
$sqlCount = "SELECT COUNT(*) AS total FROM manage_subjects $where";
$stmtCount = $conn->prepare($sqlCount);

if (!empty($params)) $stmtCount->bind_param($types, ...$params);

$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$totalRows = $resultCount->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch subjects
$sql = "SELECT * FROM manage_subjects $where LIMIT ?, ?";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $types .= "ii";
    $params[] = $start;
    $params[] = $limit;
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("ii", $start, $limit);
}

$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $row['assigned'] = in_array($row['subject'], $assignedSubjects);
    $subjects[] = $row;
}

echo json_encode([
    "subjects" => $subjects,
    "totalPages" => $totalPages
]);
?>
