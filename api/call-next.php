<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $departmentId = $data['department_id'] ?? null;
    $staffId = $_SESSION['staff_id'] ?? null;
    
    if (!$departmentId) {
        echo json_encode(['success' => false, 'message' => 'Department ID required']);
        exit;
    }
    
    // ---------------------------------------------------------
    // STEP 1: COMPLETE THE PREVIOUS TICKET (The Fix)
    // ---------------------------------------------------------
    // Before calling a new person, we must mark the CURRENT serving person as 'completed'.
    $completeStmt = $conn->prepare("
        UPDATE queue_tickets 
        SET status = 'completed', completed_at = NOW() 
        WHERE department_id = ? AND status = 'serving'
    ");
    $completeStmt->bind_param("i", $departmentId);
    $completeStmt->execute();
    
    // ---------------------------------------------------------
    // STEP 2: GET THE NEXT TICKET
    // ---------------------------------------------------------
    // Now we are free to get the next person in line.
    $stmt = $conn->prepare("
        SELECT id, ticket_number, student_name, student_number, is_priority
        FROM queue_tickets
        WHERE department_id = ? AND status = 'waiting'
        ORDER BY is_priority DESC, created_at ASC
        LIMIT 1
    ");
    $stmt->bind_param("i", $departmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($ticket = $result->fetch_assoc()) {
        // ---------------------------------------------------------
        // STEP 3: UPDATE NEW TICKET TO 'SERVING' AND ASSIGN STAFF
        // ---------------------------------------------------------
        $updateStmt = $conn->prepare("
            UPDATE queue_tickets 
            SET status = 'serving', called_at = NOW(), staff_id = ?
            WHERE id = ?
        ");
        $updateStmt->bind_param("ii", $staffId, $ticket['id']);
        $updateStmt->execute();
        
        echo json_encode([
            'success' => true,
            'ticket' => $ticket
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No tickets in queue']);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>