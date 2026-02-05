<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STI Student Kiosk</title>
    <!-- <link rel="stylesheet" href="../css/style.css"> -->
     <link rel="stylesheet" href="http://localhost/STI_Queuing_System/css/style.css">
</head>
<body>

    <div class="kiosk-container">

        <div id="screen-welcome" class="kiosk-screen active">
            <div class="kiosk-header">
                 <div class="logo-placeholder">
            <img src="http://localhost/STI_Queuing_System/images/STI_logo.png" alt="STI Logo" class="logo">
        </div>
                <h1>Welcome to the Student Queue</h1>
                <p>Please select your department to get a queue number</p>
            </div>

            <div class="department-grid">
                <button class="dept-card" onclick="openInput('Cashier')">
                    <div class="dept-icon">💰</div>
                    <h2>Cashier</h2>
                    <p>Payment & Billing</p>
                </button>
                <button class="dept-card" onclick="openInput('Admission')">
                    <div class="dept-icon">📝</div>
                    <h2>Admission</h2>
                    <p>New Students</p>
                </button>
                <button class="dept-card" onclick="openInput('Registrar')">
                    <div class="dept-icon">📋</div>
                    <h2>Registrar</h2>
                    <p>Records & Documents</p>
                </button>
            </div>
        </div>

        <div id="screen-input" class="kiosk-screen hidden">
            <div class="kiosk-modal-overlay">
                <div class="kiosk-modal">
                    <h2>Student Details</h2>
                    <p class="modal-subtitle">Department: <strong id="selected-dept-display">...</strong></p>
                    <br>

                    <div class="kiosk-form">
                        <div class="kiosk-form-group">
                            <label for="studentName">Student Name *</label>
                            <input type="text" id="studentName" placeholder="Enter your full name" oninput="checkForm()">
                        </div>

                        <div class="kiosk-form-group">
                            <label for="studentNumber">Student Number (Optional)</label>
                            <input type="text" id="studentNumber" placeholder="e.g., 2024-12345">
                        </div>

                        <div class="priority-wrapper" onclick="toggleCheckbox()">
                            <input type="checkbox" id="priorityCheck">
                            <div class="priority-label">
                                <h3>Priority Lane</h3>
                                <p>Senior Citizen, PWD, or Pregnant</p>
                            </div>
                        </div>

                        <div class="kiosk-actions">
                            <button class="btn-kiosk-cancel" onclick="goHome()">Cancel</button>
                            <button id="btn-submit" class="btn-kiosk-submit" onclick="generateTicket()" disabled>Get Ticket</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="screen-ticket" class="kiosk-screen hidden">
            <div class="kiosk-modal-overlay">
                <div class="kiosk-ticket">
                    <div class="ticket-header">
                        <h2>✓ Ticket Generated</h2>
                        <p>Please wait for your number to be called</p>
                    </div>

                    <div class="ticket-body" id="ticket-card-visual">
                        <div class="priority-badge" id="priority-badge-display">PRIORITY LANE</div>
                        <div class="ticket-number" id="ticket-number-display">---</div>
                        <div class="ticket-dept" id="ticket-dept-display">---</div>
                        <div class="ticket-name" id="ticket-name-display">---</div>
                        <div class="ticket-student-no" id="ticket-id-display"></div>
                    </div>

                    <div class="ticket-footer">
                        <p class="ticket-instruction">
                            Please keep this number and wait for it to be displayed on the monitor
                        </p>
                        <button class="btn-ticket-done" onclick="goHome()">Done</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- <script src="../js/kiosk.js"></script> -->
     <script src="http://localhost/STI_Queuing_System/js/kiosk.js"></script>
</body>
</html>
