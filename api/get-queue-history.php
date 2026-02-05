<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$staffId = $_SESSION['staff_id'];
$departmentId = $_SESSION['department_id'];
$departmentName = $_SESSION['department_name'] ?? '';

// Get filter parameters
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$status = $_GET['status'] ?? null;
$searchQuery = $_GET['search'] ?? null;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

// Build the query with filters
$query = "
    SELECT 
        qt.id,
        qt.ticket_number,
        qt.student_name,
        qt.student_number,
        qt.status,
        qt.created_at as date_issued,
        qt.called_at as time_called,
        qt.completed_at as time_completed,
        qt.notes,
        d.name as department,
        s.full_name as staff_name,
        td.name as transferred_to_department
    FROM queue_tickets qt
    LEFT JOIN departments d ON qt.department_id = d.id
    LEFT JOIN staff s ON qt.staff_id = s.id
    LEFT JOIN departments td ON qt.transferred_to_department_id = td.id
    WHERE 1=1
";

$params = [];
$types = "";

// Filter by department - staff can only see their department's history
// (Cashier only sees cashier history)
$query .= " AND qt.department_id = ?";
$params[] = $departmentId;
$types .= "i";

// Date range filter
if ($startDate) {
    $query .= " AND DATE(qt.created_at) >= ?";
    $params[] = $startDate;
    $types .= "s";
}

if ($endDate) {
    $query .= " AND DATE(qt.created_at) <= ?";
    $params[] = $endDate;
    $types .= "s";
}

// Status filter
if ($status && $status !== 'all') {
    $query .= " AND qt.status = ?";
    $params[] = $status;
    $types .= "s";
}

// Search by queue number or student name
if ($searchQuery) {
    $query .= " AND (qt.ticket_number LIKE ? OR qt.student_name LIKE ? OR qt.student_number LIKE ?)";
    $searchParam = "%{$searchQuery}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

// Order by most recent first
$query .= " ORDER BY qt.created_at DESC";

// Get total count for pagination
$countQuery = "SELECT COUNT(*) as total FROM (" . $query . ") as filtered";
$countStmt = $conn->prepare($countQuery);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalResult = $countStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$total = $totalRow['total'];

// Add pagination
$query .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

// Execute main query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $history = [];
    
    while ($row = $result->fetch_assoc()) {
        $history[] = [
            'id' => $row['id'],
            'queue_number' => $row['ticket_number'],
            'student_name' => $row['student_name'],
            'student_number' => $row['student_number'],
            'department' => $row['department'],
            'date_issued' => $row['date_issued'],
            'time_called' => $row['time_called'],
            'time_completed' => $row['time_completed'],
            'status' => $row['status'],
            'staff_name' => $row['staff_name'] ?? 'N/A',
            'transferred_to' => $row['transferred_to_department'],
            'notes' => $row['notes']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'history' => $history,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset,
        'department' => $departmentName
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch history']);
}
?>
