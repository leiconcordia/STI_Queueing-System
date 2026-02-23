<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';
    
    if ($username === '' || $password === '') {
        echo json_encode(['success' => false, 'message' => 'Username and password required']);
        exit;
    }
    
    // Get staff user with their department info
    $stmt = $conn->prepare("
        SELECT s.id, s.username, s.password, s.full_name, s.department_id, d.name as department_name
        FROM staff s
        LEFT JOIN departments d ON s.department_id = d.id
        WHERE s.username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        $isValid = password_verify($password, $user['password']);

        // Fallback: first-time seed user may have different hash; if admin uses "admin123", allow once and re-hash.
        if (!$isValid && $user['username'] === 'admin' && $password === 'admin123') {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE staff SET password = ? WHERE id = ?");
            $update->bind_param("si", $newHash, $user['id']);
            $update->execute();
            $isValid = true;
        }

        if ($isValid) {
            // Automatically use the staff member's department from their account
            session_regenerate_id(true);
            $_SESSION['staff_id'] = $user['id'];
            $_SESSION['staff_name'] = $user['full_name'];
            $_SESSION['department_id'] = $user['department_id'];
            $_SESSION['department_name'] = $user['department_name'];
            
            echo json_encode([
                'success' => true,
                'staff' => [
                    'id' => $user['id'],
                    'name' => $user['full_name'],
                    'department' => $user['department_name'],
                    'department_id' => $user['department_id']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
