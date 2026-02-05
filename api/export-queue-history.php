<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    header('Location: /STI_Queuing_System/staff-login');
    exit;
}

$staffId = $_SESSION['staff_id'];
$departmentId = $_SESSION['department_id'];
$departmentName = $_SESSION['department_name'] ?? '';

// Get export format and filters
$format = $_GET['format'] ?? 'csv'; // csv or pdf
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$status = $_GET['status'] ?? null;
$searchQuery = $_GET['search'] ?? null;

// Build the query
$query = "
    SELECT 
        qt.ticket_number,
        qt.student_name,
        qt.student_number,
        qt.status,
        qt.created_at,
        qt.called_at,
        qt.completed_at,
        d.name as department,
        s.full_name as staff_name,
        td.name as transferred_to_department
    FROM queue_tickets qt
    LEFT JOIN departments d ON qt.department_id = d.id
    LEFT JOIN staff s ON qt.staff_id = s.id
    LEFT JOIN departments td ON qt.transferred_to_department_id = td.id
    WHERE qt.department_id = ?
";

$params = [$departmentId];
$types = "i";

// Apply filters (same as get-queue-history.php)
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

if ($status && $status !== 'all') {
    $query .= " AND qt.status = ?";
    $params[] = $status;
    $types .= "s";
}

if ($searchQuery) {
    $query .= " AND (qt.ticket_number LIKE ? OR qt.student_name LIKE ?)";
    $searchParam = "%{$searchQuery}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "ss";
}

$query .= " ORDER BY qt.created_at DESC";

// Execute query
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Export as CSV
if ($format === 'csv') {
    $filename = "queue_history_{$departmentName}_" . date('Y-m-d_His') . ".csv";
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Write header
    fputcsv($output, [
        'Queue Number',
        'Student Name',
        'Student Number',
        'Department',
        'Date Issued',
        'Time Called',
        'Time Completed',
        'Status',
        'Staff Handled',
        'Transferred To'
    ]);
    
    // Write data
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['ticket_number'],
            $row['student_name'],
            $row['student_number'],
            $row['department'],
            $row['created_at'],
            $row['called_at'] ?? 'N/A',
            $row['completed_at'] ?? 'N/A',
            strtoupper($row['status']),
            $row['staff_name'] ?? 'N/A',
            $row['transferred_to_department'] ?? 'N/A'
        ]);
    }
    
    fclose($output);
    exit;
}

// Export as PDF (using basic HTML to PDF approach)
if ($format === 'pdf') {
    $filename = "queue_history_{$departmentName}_" . date('Y-m-d_His') . ".pdf";
    
    // Note: For production, consider using a library like TCPDF or mPDF
    // This is a simple HTML approach that browsers can save as PDF
    
    header('Content-Type: text/html');
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Queue History Report</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
            }
            h1 {
                color: #333;
                border-bottom: 2px solid #007bff;
                padding-bottom: 10px;
            }
            .info {
                margin-bottom: 20px;
                color: #666;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
                font-size: 12px;
            }
            th {
                background-color: #007bff;
                color: white;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            .status {
                padding: 4px 8px;
                border-radius: 4px;
                font-weight: bold;
                text-transform: uppercase;
            }
            .status-completed { background: #d4edda; color: #155724; }
            .status-cancelled { background: #f8d7da; color: #721c24; }
            .status-no-show { background: #fff3cd; color: #856404; }
            .status-serving { background: #cfe2ff; color: #084298; }
            @media print {
                body { margin: 0; }
                button { display: none; }
            }
        </style>
    </head>
    <body>
        <h1>Queue History Report - <?php echo htmlspecialchars($departmentName); ?> Department</h1>
        <div class="info">
            <p><strong>Generated:</strong> <?php echo date('F d, Y h:i A'); ?></p>
            <?php if ($startDate || $endDate): ?>
                <p><strong>Date Range:</strong> 
                    <?php echo $startDate ? date('M d, Y', strtotime($startDate)) : 'Beginning'; ?> - 
                    <?php echo $endDate ? date('M d, Y', strtotime($endDate)) : 'Present'; ?>
                </p>
            <?php endif; ?>
        </div>
        
        <button onclick="window.print()">Print / Save as PDF</button>
        
        <table>
            <thead>
                <tr>
                    <th>Queue #</th>
                    <th>Student Name</th>
                    <th>Student #</th>
                    <th>Date Issued</th>
                    <th>Time Called</th>
                    <th>Time Completed</th>
                    <th>Status</th>
                    <th>Staff</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['ticket_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['student_number'] ?? 'N/A'); ?></td>
                    <td><?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></td>
                    <td><?php echo $row['called_at'] ? date('h:i A', strtotime($row['called_at'])) : 'N/A'; ?></td>
                    <td><?php echo $row['completed_at'] ? date('h:i A', strtotime($row['completed_at'])) : 'N/A'; ?></td>
                    <td>
                        <span class="status status-<?php echo $row['status']; ?>">
                            <?php echo strtoupper($row['status']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($row['staff_name'] ?? 'N/A'); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit;
}
?>
