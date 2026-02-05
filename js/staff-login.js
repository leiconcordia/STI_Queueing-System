// Staff Login JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

         
            const departmentSelect = document.getElementById('department');
            const departmentValue = departmentSelect.value;

            // Basic guard to ensure a department was chosen
            if (!departmentValue) {
                alert('Please select a department.');
                return;
            }
            
            // Get department ID from value
            const departmentMap = {
                'cashier': 1,
                'admission': 2,
                'registrar': 3
            };
            
            const loginData = {
                username: username,
                password: password,
                department_id: departmentMap[departmentValue]
            };
            
            try {
                const response = await fetch('/STI_Queuing_System/api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(loginData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Redirect to dashboard
                    window.location.href = '/STI_Queuing_System/staff-dashboard';
                } else {
                    alert('Login failed: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Login failed. Please try again.');
            }
        });
    }
});
