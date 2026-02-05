<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $ticketId = $data['ticket_id'] ?? null;
    $status = $data['status'] ?? 'completed'; // 'completed' or 'cancelled'
    
    if (!$ticketId) {
        echo json_encode(['success' => false, 'message' => 'Ticket ID required']);
        exit;
    }
    
    $stmt = $conn->prepare("
        UPDATE queue_tickets 
        SET status = ?, completed_at = NOW() 
        WHERE id = ?
    ");
    $stmt->bind_param("si", $status, $ticketId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Ticket updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update ticket']);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
