<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

// Get all waiting tickets by department
$query = "
    SELECT 
        qt.id,
        qt.ticket_number,
        qt.student_name,
        qt.is_priority,
        qt.status,
        qt.created_at,
        qt.department_id,
        d.name as department_name,
        d.prefix,
        d.window_number
    FROM queue_tickets qt
    JOIN departments d ON qt.department_id = d.id
    WHERE qt.status IN ('waiting', 'serving')
    ORDER BY 
        d.id ASC,
        qt.status = 'serving' DESC,  -- Ensure 'serving' tickets always come first
        qt.is_priority DESC,         -- Then Priority tickets
        qt.created_at ASC            -- Then first come, first served
";

$result = $conn->query($query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$tickets = [];
while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}

echo json_encode(['success' => true, 'tickets' => $tickets, 'count' => count($tickets)]);
?>