# STI Queuing System

A modern web-based queue management system for STI College, built with PHP, MySQL, and vanilla JavaScript.

## Features

- **Student Kiosk**: Self-service ticket generation with priority lane support
- **Queue Monitor**: Real-time display of current serving numbers and next in line
- **Staff Dashboard**: Queue management interface for staff members
- **Priority Queue**: Separate handling for senior citizens, PWD, and pregnant students
- **Real-time Updates**: Automatic queue updates without page refresh

## Project Structure

```
STI_Queuing_System/
├── api/                          # API endpoints
│   ├── call-next.php            # Call next ticket
│   ├── complete-ticket.php      # Mark ticket as done/cancelled
│   ├── create-ticket.php        # Create new ticket
│   ├── get-queue.php            # Get current queue status
│   ├── login.php                # Staff authentication
│   └── logout.php               # Staff logout
├── config/                       # Configuration files
│   └── database.sql             # Database schema and seed data
├── css/                         # Stylesheets
│   └── style.css
├── includes/                    # Shared PHP files
│   └── db.php                   # Database connection
├── js/                          # JavaScript files
│   ├── kiosk.js                # Student kiosk functionality
│   ├── monitor.js              # Queue monitor display
│   ├── staff-dashboard.js      # Staff dashboard controls
│   └── staff-login.js          # Staff login handling
├── pages/                       # Page templates
│   ├── kiosk.php               # Student kiosk page
│   ├── monitor.php             # Queue monitor page
│   ├── staff-dashboard.php     # Staff dashboard page
│   └── staff-login.php         # Staff login page
├── .htaccess                    # URL routing configuration
└── index.php                    # Entry point

```

## Installation

### Prerequisites
- XAMPP (Apache, MySQL, PHP)
- Modern web browser

### Setup Steps

1. **Copy Project to XAMPP**
   ```
   Copy the STI_Queuing_System folder to C:\xampp\htdocs\
   ```

2. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start Apache
   - Start MySQL

3. **Create Database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create a new database named `queue_db`
   - Import the SQL file: `config/database.sql`
   
   OR run these commands in phpMyAdmin SQL tab:
   ```sql
   CREATE DATABASE IF NOT EXISTS queue_db;
   USE queue_db;
   ```
   Then paste the contents of `config/database.sql`

4. **Configure Database Connection**
   - Open `includes/db.php`
   - Verify the database credentials:
     ```php
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "queue_db";
     ```

5. **Enable mod_rewrite in Apache**
   - Open `C:\xampp\apache\conf\httpd.conf`
   - Find and uncomment this line (remove the #):
     ```
     LoadModule rewrite_module modules/mod_rewrite.so
     ```
   - Find `AllowOverride None` and change to `AllowOverride All`
   - Restart Apache in XAMPP

## Usage

### Access the System

- **Student Kiosk**: http://localhost/STI_Queuing_System/kiosk
- **Queue Monitor**: http://localhost/STI_Queuing_System/monitor
- **Staff Login**: http://localhost/STI_Queuing_System/staff-login

### Default Login Credentials

```
Username: admin
Password: admin123
Department: Any (Cashier, Admission, or Registrar)
```

### Student Flow

1. Student goes to kiosk
2. Selects department (Cashier, Admission, or Registrar)
3. Enters name and student number
4. Optionally selects priority lane
5. Receives ticket number
6. Waits for number to appear on monitor

### Staff Flow

1. Staff logs in with credentials
2. Selects their department
3. Clicks "Call Next" to serve the next student
4. Marks transaction as "Done", "Recall", or "No Show"
5. System automatically updates queue

## API Endpoints

### POST /api/create-ticket.php
Create a new queue ticket
```json
{
  "student_name": "Juan Dela Cruz",
  "student_number": "2024-12345",
  "department": "Cashier",
  "is_priority": false
}
```

### GET /api/get-queue.php
Get current queue status for all departments

### POST /api/call-next.php
Call next ticket in queue
```json
{
  "department_id": 1
}
```

### POST /api/complete-ticket.php
Complete or cancel a ticket
```json
{
  "ticket_id": 123,
  "status": "completed"
}
```

### POST /api/login.php
Staff authentication
```json
{
  "username": "admin",
  "password": "admin123",
  "department_id": 1
}
```

## Database Schema

### departments
- `id`: Primary key
- `name`: Department name (Cashier, Admission, Registrar)
- `prefix`: Ticket prefix (C, A, R)
- `window_number`: Window assignment

### staff
- `id`: Primary key
- `username`: Staff username
- `password`: Hashed password
- `full_name`: Staff full name
- `department_id`: Foreign key to departments

### queue_tickets
- `id`: Primary key
- `ticket_number`: Unique ticket number (e.g., C-001, P-042)
- `student_name`: Student's full name
- `student_number`: Student ID (optional)
- `department_id`: Foreign key to departments
- `is_priority`: Priority lane flag
- `status`: waiting, serving, completed, cancelled
- `called_at`: Timestamp when ticket was called
- `completed_at`: Timestamp when transaction completed
- `created_at`: Ticket creation timestamp

## Customization

### Add New Department

1. Insert into departments table:
   ```sql
   INSERT INTO departments (name, prefix, window_number) 
   VALUES ('Finance', 'F', 4);
   ```

2. Update forms to include the new department

### Change Ticket Number Format

Edit `api/create-ticket.php` in the `generateTicketNumber()` function

### Modify Priority Rules

Edit `api/call-next.php` to change queue ordering logic

## Troubleshooting

### URLs not working (404 errors)
- Make sure mod_rewrite is enabled in Apache
- Check that `.htaccess` file exists in the root directory
- Verify `AllowOverride All` in httpd.conf

### Database connection failed
- Check MySQL is running in XAMPP
- Verify credentials in `includes/db.php`
- Ensure `queue_db` database exists

### JavaScript not loading
- Check browser console for errors
- Verify file paths in HTML files
- Clear browser cache

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Server**: Apache (XAMPP)

## License

This project is created for educational purposes.

## Support

For issues or questions, please contact your system administrator.
