// Monitor JavaScript - Queue Display System

// Update time and date
function updateDateTime() {
    const now = new Date();
    
    // Format time
    const timeStr = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    
    // Format date
    const dateStr = now.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    document.getElementById('time-display').textContent = timeStr;
    document.getElementById('date-display').textContent = dateStr;
}

// Fetch and update queue data
// Fetch and update queue data
async function updateQueue() {
    try {
        const response = await fetch('/STI_Queuing_System/api/get-queue.php');
        const result = await response.json();
        
        // DEBUG: Print exactly what the database sent
        console.log("API Result:", result);

        if (result.success) {
            const tickets = result.tickets || [];
            
            // DEBUG: Check the spelling of department names
            if (tickets.length > 0) {
                console.log("First Ticket Dept Name:", tickets[0].department_name);
            }

            // FILTERING (This is likely where it breaks)
            // We use .toLowerCase() and .trim() to fix spelling/casing issues automatically
            
            const cashierTickets = tickets.filter(t => 
                t.department_name.trim().toLowerCase() === 'cashier'
            );

            // NOTE: Check if your DB says "Admission" or "Admissions" (Plural)
            const admissionTickets = tickets.filter(t => 
                t.department_name.trim().toLowerCase().includes('admission')
            );
            
            const registrarTickets = tickets.filter(t => 
                t.department_name.trim().toLowerCase() === 'registrar'
            );
            
            // Update the display
            updateDepartment('cashier', cashierTickets);
            updateDepartment('admission', admissionTickets);
            updateDepartment('registrar', registrarTickets);
            
        } else {
            console.error('API error:', result.message || 'Unknown error');
        }
    } catch (error) {
        console.error('Error fetching queue:', error);
    }
}

function updateDepartment(deptName, tickets) {
    if (!tickets || tickets.length === 0) {
        // No tickets, show all as empty
        const servingElement = document.getElementById(`${deptName}-serving`);
        if (servingElement) servingElement.textContent = '---';
        
        const nextList = document.getElementById(`${deptName}-next`);
        if (nextList) {
            nextList.innerHTML = '<div class="next-item">---</div>';
        }
        return;
    }
    
    // Find currently serving ticket
    const serving = tickets.find(t => t.status === 'serving');
    const waiting = tickets.filter(t => t.status === 'waiting');
    
    // Update "Now Serving" section
    const servingElement = document.getElementById(`${deptName}-serving`);
    if (servingElement) {
        servingElement.textContent = serving ? serving.ticket_number : '---';
    }
    
    // Update "Next in Line" section
    const nextList = document.getElementById(`${deptName}-next`);
    if (nextList) {
        nextList.innerHTML = '';
        
        const nextThree = waiting.slice(0, 3);
        if (nextThree.length > 0) {
            nextThree.forEach(ticket => {
                const item = document.createElement('div');
                item.className = 'next-item';
                if (ticket.is_priority) {
                    item.classList.add('priority');
                }
                item.textContent = ticket.ticket_number;
                nextList.appendChild(item);
            });
        } else {
            const item = document.createElement('div');
            item.className = 'next-item';
            item.textContent = '---';
            nextList.appendChild(item);
        }
    }
}

// Initialize
updateDateTime();
updateQueue();

// Update every second for time, every 3 seconds for queue
setInterval(updateDateTime, 1000);
setInterval(updateQueue, 3000);
