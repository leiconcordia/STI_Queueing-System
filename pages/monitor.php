<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STI College Queue Monitor</title>
    <!-- <link rel="stylesheet" href="../css/style.css"> -->
      <link rel="stylesheet" href="http://localhost/STI_Queuing_System/css/style.css">
</head>
<body>

    <div class="monitor-container">
        <div class="monitor-header">
            <div class="monitor-title-group">
            
            <img src="http://localhost/STI_Queuing_System/images/STI_logo.png" alt="STI Logo" class="monitor-logo">
        
                <div>
                    <h1>STI College Queue Monitor</h1>
                    <p class="monitor-date" id="date-display">Loading...</p>
                </div>
            </div>
            <div class="monitor-time" id="time-display">--:--:--</div>
        </div>

        <div class="monitor-grid">

            <div class="monitor-column" id="col-cashier">
                <div class="column-header">
                    <h2>Cashier</h2>
                </div>
                <div class="now-serving" id="cashier-box">
                    <div class="serving-label">NOW SERVING</div>
                    <div class="serving-number" id="cashier-serving">---</div>
                    <div class="serving-window">Proceed to Window 1</div>
                </div>
                <div class="next-in-line">
                    <div class="next-label">NEXT IN LINE</div>
                    <div class="next-list" id="cashier-next">
                        <div class="next-item">---</div>
                    </div>
                </div>
            </div>

            <div class="monitor-column" id="col-admission">
                <div class="column-header">
                    <h2>Admission</h2>
                </div>
                <div class="now-serving" id="admission-box">
                    <div class="serving-label">NOW SERVING</div>
                    <div class="serving-number" id="admission-serving">---</div>
                    <div class="serving-window">Proceed to Window 2</div>
                </div>
                <div class="next-in-line">
                    <div class="next-label">NEXT IN LINE</div>
                    <div class="next-list" id="admission-next">
                        <div class="next-item">---</div>
                    </div>
                </div>
            </div>

            <div class="monitor-column" id="col-registrar">
                <div class="column-header">
                    <h2>Registrar</h2>
                </div>
                <div class="now-serving" id="registrar-box">
                    <div class="serving-label">NOW SERVING</div>
                    <div class="serving-number" id="registrar-serving">---</div>
                    <div class="serving-window">Proceed to Window 3</div>
                </div>
                <div class="next-in-line">
                    <div class="next-label">NEXT IN LINE</div>
                    <div class="next-list" id="registrar-next">
                        <div class="next-item">---</div>
                    </div>
                </div>
            </div>

        </div>

        <div class="monitor-footer">
            <p>Please listen for your number and proceed to the designated window when called</p>
        </div>
    </div>

    <!-- <script src="../js/monitor.js"></script> -->
<script src="http://localhost/STI_Queuing_System/js/monitor.js"></script>
</body>
</html>
