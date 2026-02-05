<?php
// Test file to debug API and database
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Database Connection and API</h2>";

// Test 1: Database Connection
echo "<h3>1. Testing Database Connection</h3>";
require_once 'includes/db.php';

if ($conn->connect_error) {
    echo "❌ Connection failed: " . $conn->connect_error . "<br>";
} else {
    echo "✅ Connected to database successfully<br>";
    echo "Database: queue_db<br>";
}

// Test 2: Check Tables
echo "<h3>2. Checking Tables</h3>";
$tables = ['departments', 'staff', 'queue_tickets'];
foreach ($tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "✅ Table '$table' exists with {$row['count']} rows<br>";
    } else {
        echo "❌ Table '$table' error: " . $conn->error . "<br>";
    }
}

// Test 3: Check for tickets
echo "<h3>3. Current Tickets in Queue</h3>";
$query = "
    SELECT 
        qt.id,
        qt.ticket_number,
        qt.student_name,
        qt.status,
        d.name as department_name
    FROM queue_tickets qt
    JOIN departments d ON qt.department_id = d.id
    ORDER BY qt.created_at DESC
    LIMIT 10
";

$result = $conn->query($query);
if ($result) {
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Ticket</th><th>Name</th><th>Department</th><th>Status</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['ticket_number']}</td>";
            echo "<td>{$row['student_name']}</td>";
            echo "<td>{$row['department_name']}</td>";
            echo "<td>{$row['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "⚠️ No tickets found in database<br>";
        echo "<p><strong>Try creating a ticket at: <a href='/STI_Queuing_System/kiosk'>Kiosk</a></strong></p>";
    }
} else {
    echo "❌ Query error: " . $conn->error . "<br>";
}

// Test 4: Test API Endpoint
echo "<h3>4. Testing API Endpoint</h3>";
$apiUrl = "http://localhost/STI_Queuing_System/api/get-queue.php";
echo "Fetching from: $apiUrl<br>";

$apiResponse = file_get_contents($apiUrl);
if ($apiResponse) {
    echo "✅ API Response:<br>";
    echo "<pre>" . htmlspecialchars($apiResponse) . "</pre>";
    
    $data = json_decode($apiResponse, true);
    if ($data && isset($data['success'])) {
        if ($data['success']) {
            echo "✅ API Success: " . ($data['count'] ?? 0) . " tickets returned<br>";
        } else {
            echo "❌ API Error: " . ($data['message'] ?? 'Unknown error') . "<br>";
        }
    }
} else {
    echo "❌ Could not fetch API response<br>";
}

echo "<hr>";
echo "<h3>Quick Actions</h3>";
echo "<a href='/STI_Queuing_System/kiosk'>→ Go to Kiosk</a> | ";
echo "<a href='/STI_Queuing_System/monitor'>→ Go to Monitor</a> | ";
echo "<a href='/STI_Queuing_System/staff-login'>→ Go to Staff Login</a>";
?>
