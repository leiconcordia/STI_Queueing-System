// Kiosk JavaScript - Student Queue System
// State Variables
let selectedDepartment = null;

// Elements
const screenWelcome = document.getElementById('screen-welcome');
const screenInput = document.getElementById('screen-input');
const screenTicket = document.getElementById('screen-ticket');
const deptDisplay = document.getElementById('selected-dept-display');
const inputName = document.getElementById('studentName');
const inputNumber = document.getElementById('studentNumber');
const submitBtn = document.getElementById('btn-submit');
const priorityCheck = document.getElementById('priorityCheck');

// Ticket Elements
const ticketNumDisplay = document.getElementById('ticket-number-display');
const ticketDeptDisplay = document.getElementById('ticket-dept-display');
const ticketNameDisplay = document.getElementById('ticket-name-display');
const ticketIdDisplay = document.getElementById('ticket-id-display');
const ticketCardVisual = document.getElementById('ticket-card-visual');
const priorityBadge = document.getElementById('priority-badge-display');

// --- Functions ---

function showScreen(screenId) {
    document.querySelectorAll('.kiosk-screen').forEach(screen => {
        screen.classList.remove('active');
        screen.classList.add('hidden');
    });
    document.getElementById(screenId).classList.remove('hidden');
    document.getElementById(screenId).classList.add('active');
}

function openInput(department) {
    selectedDepartment = department;
    deptDisplay.textContent = department;
    inputName.value = '';
    inputNumber.value = '';
    priorityCheck.checked = false;
    checkForm();
    showScreen('screen-input');
}

// Allow clicking the div to toggle checkbox
function toggleCheckbox() {
    priorityCheck.checked = !priorityCheck.checked;
}

function checkForm() {
    if (inputName.value.trim().length > 0) {
        submitBtn.removeAttribute('disabled');
        submitBtn.style.backgroundColor = '#0054A6';
    } else {
        submitBtn.setAttribute('disabled', 'true');
        submitBtn.style.backgroundColor = '#ccc';
    }
}

async function generateTicket() {
    if (!selectedDepartment || inputName.value.trim() === '') return;

    const isPriority = priorityCheck.checked;
    
    // Prepare data
    const ticketData = {
        student_name: inputName.value.trim(),
        student_number: inputNumber.value.trim() || null,
        department: selectedDepartment,
        is_priority: isPriority
    };

    try {
        // Call API to create ticket
        const response = await fetch('/STI_Queuing_System/api/create-ticket.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(ticketData)
        });

        const result = await response.json();

        if (result.success) {
            // Update visuals with the returned ticket data
            ticketNumDisplay.textContent = result.ticket_number;
            ticketDeptDisplay.textContent = result.department;
            ticketNameDisplay.textContent = result.student_name;

            // Handle Student ID
            if (result.student_number) {
                ticketIdDisplay.textContent = `ID: ${result.student_number}`;
                ticketIdDisplay.style.display = 'block';
            } else {
                ticketIdDisplay.style.display = 'none';
            }

            // Apply Priority Styling
            if (result.is_priority) {
                ticketCardVisual.classList.add('is-priority');
                priorityBadge.style.display = 'block';
            } else {
                ticketCardVisual.classList.remove('is-priority');
                priorityBadge.style.display = 'none';
            }

            showScreen('screen-ticket');
        } else {
            alert('Error creating ticket: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to create ticket. Please try again.');
    }
}

function goHome() {
    selectedDepartment = null;
    showScreen('screen-welcome');
}
