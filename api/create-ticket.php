<?php
// 1. Headers and DB Connection
header('Content-Type: application/json');
require_once '../includes/db.php';

// 2. Function to get Department ID
function getDepartmentId($conn, $deptName) {
    $stmt = $conn->prepare("SELECT id FROM departments WHERE name = ?");
    $stmt->bind_param("s", $deptName);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['id'] : null;
}

// 3. FIXED Function to Generate Ticket Number (Prevents Duplicates)
function generateTicketNumber($conn, $prefix) {
    // Find the LAST ticket ever created with this prefix
    // We order by 'id DESC' because ID is always unique and increasing.
    $stmt = $conn->prepare("
        SELECT ticket_number 
        FROM queue_tickets 
        WHERE ticket_number LIKE ? 
        ORDER BY id DESC 
        LIMIT 1
    ");
    
    // Search for anything starting with the prefix (e.g., "C-%")
    $pattern = $prefix . '-%';
    $stmt->bind_param("s", $pattern);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Extract the number part (e.g., from "C-005" take "005")
        $lastNumberString = substr($row['ticket_number'], strlen($prefix) + 1);
        
        // Convert to integer and add 1
        $nextNum = intval($lastNumberString) + 1;
    } else {
        // If no tickets exist at all for this prefix, start at 1
        $nextNum = 1;
    }
    
    // Format back to "C-001"
    return $prefix . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
}

// 4. Main Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON Input
    $data = json_decode(file_get_contents('php://input'), true);
    
    $studentName = $data['student_name'] ?? '';
    $studentNumber = $data['student_number'] ?? null;
    $department = $data['department'] ?? '';
    $isPriority = $data['is_priority'] ?? false;
    
    // Validate Input
    if (empty($studentName) || empty($department)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    // Get department ID
    $deptId = getDepartmentId($conn, $department);
    if (!$deptId) {
        echo json_encode(['success' => false, 'message' => 'Invalid department']);
        exit;
    }
    
    // Determine prefix
    if ($isPriority) {
        $prefix = 'P'; // Priority Prefix
    } else {
        $stmt = $conn->prepare("SELECT prefix FROM departments WHERE id = ?");
        $stmt->bind_param("i", $deptId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $prefix = $row['prefix'];
    }
    
    // Generate Unique Ticket Number
    $ticketNumber = generateTicketNumber($conn, $prefix);
    
    // Insert into Database
    $stmt = $conn->prepare("
        INSERT INTO queue_tickets (ticket_number, student_name, student_number, department_id, is_priority, status) 
        VALUES (?, ?, ?, ?, ?, 'waiting')
    ");
    
    // Use 'i' for boolean priority (0 or 1)
    $priorityInt = $isPriority ? 1 : 0;
    $stmt->bind_param("sssii", $ticketNumber, $studentName, $studentNumber, $deptId, $priorityInt);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'ticket_number' => $ticketNumber,
            'department' => $department,
            'student_name' => $studentName,
            'student_number' => $studentNumber,
            'is_priority' => $isPriority
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create ticket',
            'error' => $stmt->error
        ]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>