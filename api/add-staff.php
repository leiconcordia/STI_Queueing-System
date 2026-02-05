<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';
    $fullName = trim($data['full_name'] ?? '');
    $departmentId = $data['department_id'] ?? null;
    
    // Validation
    if (empty($username)) {
        echo json_encode(['success' => false, 'message' => 'Username is required']);
        exit;
    }
    
    if (empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Password is required']);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }
    
    if (empty($fullName)) {
        echo json_encode(['success' => false, 'message' => 'Full name is required']);
        exit;
    }
    
    if (empty($departmentId)) {
        echo json_encode(['success' => false, 'message' => 'Department is required']);
        exit;
    }
    
    // Check if username already exists
    $checkStmt = $conn->prepare("SELECT id FROM staff WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new staff
    $stmt = $conn->prepare("
        INSERT INTO staff (username, password, full_name, department_id, created_at) 
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("sssi", $username, $hashedPassword, $fullName, $departmentId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Staff member added successfully',
            'staff_id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add staff member']);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
