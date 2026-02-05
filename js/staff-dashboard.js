// Staff Dashboard JavaScript

let currentTicket = null;
let departmentId = null;
let currentPage = 1;
let historyLimit = 50;
let historyTotal = 0;
let currentFilters = {};
let allStaffList = [];
let allDepartments = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', async function() {
    // Get department from session (passed via PHP)
    departmentId = parseInt(document.body.dataset.departmentId);
    
    if (!departmentId) {
        alert('Session expired. Please login again.');
        window.location.href = '/STI_Queuing_System/staff-login';
        return;
    }
    
    // Load initial data
    await updateQueueCount();
    
    // Refresh queue count every 5 seconds
    setInterval(updateQueueCount, 5000);
    
    // Show queue tab by default
    showTab('queue');
    
    // Load departments for staff management
    await loadDepartments();
});

// Update queue count
async function updateQueueCount() {
    try {
        const response = await fetch('/STI_Queuing_System/api/get-queue.php');
        const result = await response.json();
        
        if (result.success) {
            const tickets = result.tickets;
            
            // Count waiting tickets for current department
            const waitingCount = tickets.filter(t => 
                t.status === 'waiting' && 
                parseInt(t.department_id) === departmentId
            ).length;
            
            document.getElementById('queueCount').textContent = waitingCount;
        }
    } catch (error) {
        console.error('Error fetching queue:', error);
    }
}

// Call next student
async function callNext() {
    try {
        const response = await fetch('/STI_Queuing_System/api/call-next.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                department_id: departmentId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            currentTicket = result.ticket;
            
            // Update UI
            document.getElementById('ticketNumber').textContent = currentTicket.ticket_number;
            document.getElementById('studentName').textContent = currentTicket.student_name;
            
            // Update queue count
            await updateQueueCount();
            
            // Optional: Play notification sound
            playNotificationSound();
        } else {
            alert(result.message || 'No tickets in queue');
        }
    } catch (error) {
        console.error('Error calling next:', error);
        alert('Failed to call next student');
    }
}

// Mark transaction as done
async function markDone() {
    if (!currentTicket) {
        alert('No student currently being served');
        return;
    }
    
    if (!confirm('Mark this transaction as complete?')) {
        return;
    }
    
    try {
        const response = await fetch('/STI_Queuing_System/api/complete-ticket.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                ticket_id: currentTicket.id,
                status: 'completed'
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Clear current ticket
            currentTicket = null;
            document.getElementById('ticketNumber').textContent = '---';
            document.getElementById('studentName').textContent = '---';
            
            // Update queue count
            await updateQueueCount();
            
            alert('Transaction completed successfully!');
        } else {
            alert('Failed to complete transaction');
        }
    } catch (error) {
        console.error('Error completing ticket:', error);
        alert('Failed to complete transaction');
    }
}

// Recall current student
function recallStudent() {
    if (!currentTicket) {
        alert('No student currently being served');
        return;
    }
    
    alert(`Recalling ${currentTicket.ticket_number}...`);
    playNotificationSound();
}

// Mark student as no show
async function markNoShow() {
    if (!currentTicket) {
        alert('No student currently being served');
        return;
    }
    
    if (!confirm('Mark this student as No Show?')) {
        return;
    }
    
    try {
        const response = await fetch('/STI_Queuing_System/api/complete-ticket.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                ticket_id: currentTicket.id,
                status: 'cancelled'
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Student marked as No Show');
            
            // Automatically call next
            await callNext();
        } else {
            alert('Failed to mark as no show');
        }
    } catch (error) {
        console.error('Error marking no show:', error);
        alert('Failed to mark as no show');
    }
}

// Play notification sound (optional)
function playNotificationSound() {
    // You can add an audio element and play it here
    // const audio = new Audio('/sounds/notification.mp3');
    // audio.play();
}

// ========================================
// HISTORY TAB FUNCTIONS
// ========================================

// Show/Hide tabs
function showTab(tabName) {
    // Hide all tabs
    document.getElementById('queueTab').classList.remove('active');
    document.getElementById('historyTab').classList.remove('active');
    document.getElementById('staffTab').classList.remove('active');
    
    // Remove active class from buttons
    document.getElementById('btnQueueTab').classList.remove('active');
    document.getElementById('btnHistoryTab').classList.remove('active');
    document.getElementById('btnStaffTab').classList.remove('active');
    
    // Show selected tab
    if (tabName === 'queue') {
        document.getElementById('queueTab').classList.add('active');
        document.getElementById('btnQueueTab').classList.add('active');
    } else if (tabName === 'history') {
        document.getElementById('historyTab').classList.add('active');
        document.getElementById('btnHistoryTab').classList.add('active');
        
        // Load history when tab is opened
        loadQueueHistory();
    } else if (tabName === 'staff') {
        document.getElementById('staffTab').classList.add('active');
        document.getElementById('btnStaffTab').classList.add('active');
        
        // Load staff list when tab is opened
        loadStaffList();
    }
}

// Load queue history with filters
async function loadQueueHistory(page = 1) {
    currentPage = page;
    const offset = (page - 1) * historyLimit;
    
    // Build query parameters
    const params = new URLSearchParams({
        limit: historyLimit,
        offset: offset,
        ...currentFilters
    });
    
    try {
        const response = await fetch(`/STI_Queuing_System/api/get-queue-history.php?${params}`);
        const result = await response.json();
        
        if (result.success) {
            displayHistory(result.history);
            historyTotal = result.total;
            updatePagination();
        } else {
            showError('Failed to load history');
        }
    } catch (error) {
        console.error('Error loading history:', error);
        showError('Error loading history');
    }
}

// Display history in table
function displayHistory(history) {
    const tbody = document.getElementById('historyTableBody');
    
    if (history.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="no-data">No records found</td></tr>';
        return;
    }
    
    tbody.innerHTML = history.map(record => {
        const statusClass = `status-${record.status}`;
        const dateIssued = new Date(record.date_issued);
        const timeCalled = record.time_called ? new Date(record.time_called) : null;
        const timeCompleted = record.time_completed ? new Date(record.time_completed) : null;
        
        return `
            <tr>
                <td><strong>${record.queue_number}</strong></td>
                <td>${escapeHtml(record.student_name)}</td>
                <td>${escapeHtml(record.student_number || 'N/A')}</td>
                <td>${formatDateTime(dateIssued)}</td>
                <td>${timeCalled ? formatTime(timeCalled) : 'N/A'}</td>
                <td>${timeCompleted ? formatTime(timeCompleted) : 'N/A'}</td>
                <td><span class="status-badge ${statusClass}">${record.status.toUpperCase()}</span></td>
                <td>${escapeHtml(record.staff_name)}</td>
            </tr>
        `;
    }).join('');
}

// Apply filters
function applyFilters() {
    currentFilters = {};
    
    const startDate = document.getElementById('filterStartDate').value;
    const endDate = document.getElementById('filterEndDate').value;
    const status = document.getElementById('filterStatus').value;
    const search = document.getElementById('filterSearch').value.trim();
    
    if (startDate) currentFilters.start_date = startDate;
    if (endDate) currentFilters.end_date = endDate;
    if (status && status !== 'all') currentFilters.status = status;
    if (search) currentFilters.search = search;
    
    loadQueueHistory(1);
}

// Reset filters
function resetFilters() {
    document.getElementById('filterStartDate').value = '';
    document.getElementById('filterEndDate').value = '';
    document.getElementById('filterStatus').value = 'all';
    document.getElementById('filterSearch').value = '';
    currentFilters = {};
    loadQueueHistory(1);
}

// Export history
function exportHistory(format) {
    // Build query parameters with current filters
    const params = new URLSearchParams({
        format: format,
        ...currentFilters
    });
    
    // Open export URL in new window
    window.open(`/STI_Queuing_System/api/export-queue-history.php?${params}`, '_blank');
}

// Pagination functions
function updatePagination() {
    const totalPages = Math.ceil(historyTotal / historyLimit);
    document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages} (${historyTotal} records)`;
    
    // Disable/enable buttons
    document.getElementById('btnPrevPage').disabled = currentPage <= 1;
    document.getElementById('btnNextPage').disabled = currentPage >= totalPages;
}

function loadPreviousPage() {
    if (currentPage > 1) {
        loadQueueHistory(currentPage - 1);
    }
}

function loadNextPage() {
    const totalPages = Math.ceil(historyTotal / historyLimit);
    if (currentPage < totalPages) {
        loadQueueHistory(currentPage + 1);
    }
}

// Utility functions
function formatDateTime(date) {
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatTime(date) {
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showError(message) {
    const tbody = document.getElementById('historyTableBody');
    tbody.innerHTML = `<tr><td colspan="8" class="error">${escapeHtml(message)}</td></tr>`;
}

// ========================================
// STAFF MANAGEMENT FUNCTIONS
// ========================================

// Load departments for dropdown
async function loadDepartments() {
    try {
        const response = await fetch('/STI_Queuing_System/api/get-departments.php');
        const result = await response.json();
        
        if (result.success) {
            allDepartments = result.departments;
            
            // Populate staff department dropdown
            const staffDeptSelect = document.getElementById('staffDepartment');
            staffDeptSelect.innerHTML = '<option value="">-- Select Department --</option>';
            
            result.departments.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.id;
                option.textContent = dept.name;
                staffDeptSelect.appendChild(option);
            });
            
            // Populate filter dropdown
            const filterSelect = document.getElementById('staffDeptFilter');
            filterSelect.innerHTML = '<option value="">All Departments</option>';
            
            result.departments.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.id;
                option.textContent = dept.name;
                filterSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading departments:', error);
    }
}

// Handle add staff form submission
async function handleAddStaff(event) {
    event.preventDefault();
    
    const username = document.getElementById('staffUsername').value.trim();
    const password = document.getElementById('staffPassword').value;
    const fullName = document.getElementById('staffFullName').value.trim();
    const deptId = document.getElementById('staffDepartment').value;
    
    // Basic validation
    if (!username || !password || !fullName || !deptId) {
        showFormMessage('Please fill in all required fields', 'error');
        return;
    }
    
    if (password.length < 6) {
        showFormMessage('Password must be at least 6 characters', 'error');
        return;
    }
    
    // Check for spaces in username
    if (username.includes(' ')) {
        showFormMessage('Username cannot contain spaces', 'error');
        return;
    }
    
    try {
        const response = await fetch('/STI_Queuing_System/api/add-staff.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                password: password,
                full_name: fullName,
                department_id: parseInt(deptId)
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showFormMessage('✓ Staff member added successfully!', 'success');
            clearStaffForm();
            loadStaffList();
        } else {
            showFormMessage('✗ ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error adding staff:', error);
        showFormMessage('✗ Failed to add staff member', 'error');
    }
}

// Show form message
function showFormMessage(message, type) {
    const messageEl = document.getElementById('staffFormMessage');
    messageEl.textContent = message;
    messageEl.className = 'form-message ' + type;
    messageEl.style.display = 'block';
    
    // Auto-hide success messages after 5 seconds
    if (type === 'success') {
        setTimeout(() => {
            messageEl.style.display = 'none';
        }, 5000);
    }
}

// Clear staff form
function clearStaffForm() {
    document.getElementById('addStaffForm').reset();
    document.getElementById('staffFormMessage').style.display = 'none';
}

// Load staff list
async function loadStaffList() {
    const deptFilter = document.getElementById('staffDeptFilter').value;
    const searchQuery = document.getElementById('staffSearchFilter').value.trim();
    
    const params = new URLSearchParams();
    if (deptFilter) params.append('department_id', deptFilter);
    if (searchQuery) params.append('search', searchQuery);
    
    try {
        const response = await fetch(`/STI_Queuing_System/api/get-staff-list.php?${params}`);
        const result = await response.json();
        
        if (result.success) {
            allStaffList = result.staff;
            displayStaffList(result.staff);
            document.getElementById('staffCount').textContent = `Total: ${result.total} staff member${result.total !== 1 ? 's' : ''}`;
        } else {
            showStaffError('Failed to load staff list');
        }
    } catch (error) {
        console.error('Error loading staff list:', error);
        showStaffError('Error loading staff list');
    }
}

// Display staff list in table
function displayStaffList(staffList) {
    const tbody = document.getElementById('staffTableBody');
    
    if (staffList.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="no-data">No staff members found</td></tr>';
        return;
    }
    
    tbody.innerHTML = staffList.map(staff => {
        const createdDate = new Date(staff.created_at);
        
        return `
            <tr>
                <td><strong>${escapeHtml(staff.username)}</strong></td>
                <td>${escapeHtml(staff.full_name)}</td>
                <td><span class="dept-badge">${escapeHtml(staff.department_name)}</span></td>
                <td>${createdDate.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                })}</td>
            </tr>
        `;
    }).join('');
}

// Filter staff list (client-side for search)
function filterStaffList() {
    loadStaffList();
}

// Show staff error
function showStaffError(message) {
    const tbody = document.getElementById('staffTableBody');
    tbody.innerHTML = `<tr><td colspan="4" class="error">${escapeHtml(message)}</td></tr>`;
}
