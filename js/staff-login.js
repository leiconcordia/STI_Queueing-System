// Staff Login JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            if (!username || !password) {
                alert('Please enter both username and password.');
                return;
            }
            
            const loginData = {
                username: username,
                password: password
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
                    // Redirect to dashboard (department is automatically identified)
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
