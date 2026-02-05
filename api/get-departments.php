<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get all departments
$stmt = $conn->prepare("SELECT id, name, prefix, window_number FROM departments ORDER BY name ASC");
$stmt->execute();
$result = $stmt->get_result();

$departments = [];
while ($row = $result->fetch_assoc()) {
    $departments[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'prefix' => $row['prefix'],
        'window_number' => $row['window_number']
    ];
}

echo json_encode([
    'success' => true,
    'departments' => $departments
]);
?>
