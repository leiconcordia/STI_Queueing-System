<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    header('Location: /STI_Queuing_System/staff-login');
    exit;
}

$staffName = $_SESSION['staff_name'] ?? 'Staff';
$departmentName = $_SESSION['department_name'] ?? 'Department';
$departmentId = $_SESSION['department_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - STI College Queuing System</title>
    <!-- <link rel="stylesheet" href="../css/style.css"> -->
     <link rel="stylesheet" href="http://localhost/STI_Queuing_System/css/style.css">
</head>
<body data-department-id="<?php echo $departmentId; ?>">
    <div class="dashboard-container">
        <!-- Navigation Bar -->
        <nav class="navbar">
            <div class="navbar-left">
                <h2>Welcome, <span><?php echo htmlspecialchars($staffName); ?></span> | <?php echo htmlspecialchars($departmentName); ?> Department</h2>
            </div>
            <div class="navbar-right">
                <button class="btn-nav" onclick="showTab('queue')" id="btnQueueTab">
                    📋 Queue
                </button>
                <button class="btn-nav" onclick="showTab('history')" id="btnHistoryTab">
                    📊 History
                </button>
                <button class="btn-nav" onclick="showTab('staff')" id="btnStaffTab">
                    👥 Staff
                </button>
                <button class="btn-logout" onclick="logout()">Logout</button>
            </div>
        </nav>

        <!-- Main Dashboard Content -->
        <main class="dashboard-main">
            <!-- Queue Tab -->
            <div id="queueTab" class="tab-content active">
                <div class="dashboard-grid">
                    <!-- Panel A: Action Zone (Call Next & Queue Status) -->
                    <div class="panel action-zone">
                        <h3>Queue Control</h3>
                        
                        <!-- Call Next Button - The Centerpiece -->
                        <button class="btn-call-next" onclick="callNext()">
                            📢 Call Next
                        </button>

                        <!-- Queue Status Display -->
                        <div class="queue-status">
                            <div class="queue-status-number" id="queueCount">0</div>
                            <div class="queue-status-label">Students Waiting</div>
                        </div>
                    </div>

                    <!-- Panel B: Current Transaction -->
                    <div class="panel">
                        <h3>Current Transaction</h3>
                        
                        <!-- Ticket Card -->
                        <div class="ticket-card">
                            <div class="ticket-number" id="ticketNumber">---</div>
                            <div class="student-name" id="studentName">---</div>
                            <div class="ticket-status">Now Serving</div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button class="btn-action btn-done" onclick="markDone()">
                                ✓ Done
                            </button>
                            <button class="btn-action btn-recall" onclick="recallStudent()">
                                🔄 Recall
                            </button>
                            <button class="btn-action btn-noshow" onclick="markNoShow()">
                                ✗ No Show
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History Tab -->
            <div id="historyTab" class="tab-content">
                <div class="history-container">
                    <div class="history-header">
                        <h3>📊 Queue History & Logs</h3>
                        <p class="dept-info">Viewing: <strong><?php echo htmlspecialchars($departmentName); ?> Department</strong></p>
                    </div>

                    <!-- Filters Section -->
                    <div class="history-filters">
                        <div class="filter-group">
                            <label>Date Range:</label>
                            <input type="date" id="filterStartDate" class="filter-input">
                            <span>to</span>
                            <input type="date" id="filterEndDate" class="filter-input">
                        </div>

                        <div class="filter-group">
                            <label>Status:</label>
                            <select id="filterStatus" class="filter-input">
                                <option value="all">All Status</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="no-show">No Show</option>
                                <option value="skipped">Skipped</option>
                                <option value="transferred">Transferred</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Search:</label>
                            <input type="text" id="filterSearch" class="filter-input" placeholder="Queue # or Student Name">
                        </div>

                        <div class="filter-actions">
                            <button class="btn-filter" onclick="applyFilters()">🔍 Apply Filters</button>
                            <button class="btn-filter-reset" onclick="resetFilters()">↺ Reset</button>
                        </div>
                    </div>

                    <!-- Export Actions -->
                    <div class="export-actions">
                        <button class="btn-export" onclick="exportHistory('csv')">
                            📥 Export CSV
                        </button>
                        <button class="btn-export" onclick="exportHistory('pdf')">
                            📄 Export PDF
                        </button>
                    </div>

                    <!-- History Table -->
                    <div class="history-table-container">
                        <table class="history-table">
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
                            <tbody id="historyTableBody">
                                <tr>
                                    <td colspan="8" class="loading">Loading history...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination">
                        <button class="btn-page" onclick="loadPreviousPage()" id="btnPrevPage">← Previous</button>
                        <span id="pageInfo">Page 1</span>
                        <button class="btn-page" onclick="loadNextPage()" id="btnNextPage">Next →</button>
                    </div>
                </div>
            </div>

            <!-- Staff Management Tab -->
            <div id="staffTab" class="tab-content">
                <div class="staff-container">
                    <div class="staff-header">
                        <h3>👥 Staff Management</h3>
                        <p class="staff-info">Add and manage staff members for all departments</p>
                    </div>

                    <div class="staff-grid">
                        <!-- Add Staff Form -->
                        <div class="panel staff-form-panel">
                            <h4>Add New Staff Member</h4>
                            
                            <form id="addStaffForm" onsubmit="handleAddStaff(event)">
                                <div class="form-group">
                                    <label for="staffUsername">Username <span class="required">*</span></label>
                                    <input type="text" id="staffUsername" class="form-input" required 
                                           placeholder="Enter username (no spaces)">
                                    <small class="form-hint">Use lowercase, no spaces</small>
                                </div>

                                <div class="form-group">
                                    <label for="staffPassword">Password <span class="required">*</span></label>
                                    <input type="password" id="staffPassword" class="form-input" required 
                                           placeholder="Enter password (min. 6 characters)">
                                    <small class="form-hint">Minimum 6 characters</small>
                                </div>

                                <div class="form-group">
                                    <label for="staffFullName">Full Name <span class="required">*</span></label>
                                    <input type="text" id="staffFullName" class="form-input" required 
                                           placeholder="Enter full name">
                                </div>

                                <div class="form-group">
                                    <label for="staffDepartment">Department <span class="required">*</span></label>
                                    <select id="staffDepartment" class="form-input" required>
                                        <option value="">-- Select Department --</option>
                                    </select>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-submit">
                                        ✓ Add Staff Member
                                    </button>
                                    <button type="button" class="btn-clear" onclick="clearStaffForm()">
                                        ↺ Clear Form
                                    </button>
                                </div>

                                <div id="staffFormMessage" class="form-message"></div>
                            </form>
                        </div>

                        <!-- Staff List -->
                        <div class="panel staff-list-panel">
                            <h4>Current Staff Members</h4>
                            
                            <!-- Staff List Filters -->
                            <div class="staff-filters">
                                <select id="staffDeptFilter" class="filter-input" onchange="loadStaffList()">
                                    <option value="">All Departments</option>
                                </select>
                                <input type="text" id="staffSearchFilter" class="filter-input" 
                                       placeholder="Search staff..." onkeyup="filterStaffList()">
                            </div>

                            <!-- Staff Table -->
                            <div class="staff-table-container">
                                <table class="staff-table">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Full Name</th>
                                            <th>Department</th>
                                            <th>Added</th>
                                        </tr>
                                    </thead>
                                    <tbody id="staffTableBody">
                                        <tr>
                                            <td colspan="4" class="loading">Loading staff list...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="staff-count">
                                <span id="staffCount">Total: 0 staff members</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- <script src="../js/staff-dashboard.js"></script> -->
          <script src="http://localhost/STI_Queuing_System/js/staff-dashboard.js"></script>
    <script>
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '/STI_Queuing_System/api/logout.php';
            }
        }
    </script>
</body>
</html>
