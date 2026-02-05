# SETUP INSTRUCTIONS - STI Queuing System

## Quick Start Guide

### Step 1: Database Setup

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "New" to create a new database
3. Database name: `queue_db`
4. Click "Create"
5. Select the `queue_db` database
6. Click "SQL" tab
7. Open the file: `config/database.sql`
8. Copy all contents and paste into the SQL query box
9. Click "Go" to execute

### Step 2: Enable URL Rewriting

**Option A: Edit httpd.conf (Recommended)**

1. Open: `C:\xampp\apache\conf\httpd.conf`
2. Find this line (around line 177):
   ```
   #LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. Remove the `#` to make it:
   ```
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
4. Find (around line 232):
   ```
   AllowOverride None
   ```
5. Change to:
   ```
   AllowOverride All
   ```
6. Save the file
7. Restart Apache in XAMPP

**Option B: Check if Already Enabled**

1. Create a test file: `C:\xampp\htdocs\test.php`
   ```php
   <?php phpinfo(); ?>
   ```
2. Open: http://localhost/test.php
3. Search for "mod_rewrite"
4. If you see it, it's enabled!

### Step 3: Verify Installation

1. Open your browser
2. Go to: http://localhost/STI_Queuing_System/kiosk
3. You should see the kiosk interface

### Step 4: Test the System

**Test Kiosk:**
1. Go to: http://localhost/STI_Queuing_System/kiosk
2. Click on any department
3. Enter a name
4. Click "Get Ticket"
5. You should receive a ticket number

**Test Monitor:**
1. Open a new tab
2. Go to: http://localhost/STI_Queuing_System/monitor
3. You should see the queue display

**Test Staff Login:**
1. Go to: http://localhost/STI_Queuing_System/staff-login
2. Username: `admin`
3. Password: `admin123`
4. Department: Select any
5. Click "Login"
6. You should see the staff dashboard

### Step 5: Create Additional Staff Users (Optional)

Run this in phpMyAdmin SQL tab to create more staff users:

```sql
-- Cashier Staff
INSERT INTO staff (username, password, full_name, department_id) VALUES
('cashier1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria Santos', 1);

-- Admission Staff
INSERT INTO staff (username, password, full_name, department_id) VALUES
('admission1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan Dela Cruz', 2);

-- Registrar Staff
INSERT INTO staff (username, password, full_name, department_id) VALUES
('registrar1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana Reyes', 3);
```

All passwords are: `admin123`

## Troubleshooting

### Problem: 404 Not Found when accessing /kiosk

**Solution:**
- mod_rewrite is not enabled
- Follow Step 2 above
- Make sure to restart Apache

### Problem: Access forbidden

**Solution:**
- Check `.htaccess` file exists in root directory
- Verify `AllowOverride All` in httpd.conf

### Problem: Database connection failed

**Solution:**
- Make sure MySQL is running in XAMPP
- Check database name is `queue_db`
- Verify credentials in `includes/db.php`

### Problem: Blank page or errors

**Solution:**
- Check Apache error log: `C:\xampp\apache\logs\error.log`
- Enable error display in PHP:
  - Open `C:\xampp\php\php.ini`
  - Find `display_errors = Off`
  - Change to `display_errors = On`
  - Restart Apache

### Problem: JavaScript not working

**Solution:**
- Open browser Developer Tools (F12)
- Check Console tab for errors
- Verify all JS files are loading
- Clear browser cache (Ctrl+F5)

## URL Structure

After setup, these URLs will work:

- http://localhost/STI_Queuing_System/ → Redirects to kiosk
- http://localhost/STI_Queuing_System/kiosk → Student kiosk
- http://localhost/STI_Queuing_System/monitor → Queue monitor
- http://localhost/STI_Queuing_System/staff-login → Staff login
- http://localhost/STI_Queuing_System/staff-dashboard → Staff dashboard (requires login)

## Default Login

```
Username: admin
Password: admin123
```

## Next Steps

1. Test creating tickets from kiosk
2. Test calling tickets from staff dashboard
3. Monitor updates on the monitor page
4. Customize CSS in `css/style.css` as needed
5. Add more staff users
6. Modify departments if needed

## Need Help?

1. Check the main README.md file
2. Review the database schema in `config/database.sql`
3. Check Apache/MySQL logs in XAMPP
4. Ensure all services are running in XAMPP Control Panel

---

**Important:** This is a development setup. For production use:
- Change all passwords
- Use HTTPS
- Add proper security measures
- Implement input validation
- Add CSRF protection
