<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get filter parameters
$departmentFilter = $_GET['department_id'] ?? null;
$searchQuery = $_GET['search'] ?? null;

// Build query
$query = "
    SELECT 
        s.id,
        s.username,
        s.full_name,
        s.created_at,
        d.name as department_name,
        d.id as department_id
    FROM staff s
    LEFT JOIN departments d ON s.department_id = d.id
    WHERE 1=1
";

$params = [];
$types = "";

// Apply filters
if ($departmentFilter) {
    $query .= " AND s.department_id = ?";
    $params[] = $departmentFilter;
    $types .= "i";
}

if ($searchQuery) {
    $query .= " AND (s.username LIKE ? OR s.full_name LIKE ?)";
    $searchParam = "%{$searchQuery}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "ss";
}

$query .= " ORDER BY s.created_at DESC";

// Execute query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $staffList = [];
    
    while ($row = $result->fetch_assoc()) {
        $staffList[] = [
            'id' => $row['id'],
            'username' => $row['username'],
            'full_name' => $row['full_name'],
            'department_name' => $row['department_name'],
            'department_id' => $row['department_id'],
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'staff' => $staffList,
        'total' => count($staffList)
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch staff list']);
}
?>
