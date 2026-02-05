<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal - STI College Queuing System</title>
    <!-- <link rel="stylesheet" href="../css/style.css"> -->
      <link rel="stylesheet" href="http://localhost/STI_Queuing_System/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Logo Placeholder -->
           <div class="logo-placeholder">
            <img src="http://localhost/STI_Queuing_System/images/STI_logo.png" alt="STI Logo" class="logo">
        </div>


            
            <!-- Login Header -->
            <div class="login-header">
                <h1>Staff Portal</h1>
                <p>Queue Management System</p>
            </div>

            <!-- Login Form -->
            <form class="login-form">
                <!-- Username Input -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Enter your username" 
                        required
                    >
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password" 
                        required
                    >
                </div>

                Department Selection
                <div class="form-group">
                    <label for="department">Select Department/Counter</label>
                    <select id="department" name="department" required>
                        <option value="" disabled selected>Choose a department...</option>
                        <option value="cashier">Cashier</option>
                        <option value="admission">Admission</option>
                        <option value="registrar">Registrar</option>
                    </select>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-primary">Login</button>
            </form>
        </div>
    </div>

    <!-- <script src="../js/staff-login.js"></script> -->
          <script src="http://localhost/STI_Queuing_System/js/staff-login.js"></script>
</body>
</html>
