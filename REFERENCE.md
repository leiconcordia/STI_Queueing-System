# DEVELOPER QUICK REFERENCE

## 🚀 Quick Start

```bash
1. Import: config/database.sql to phpMyAdmin
2. Enable: mod_rewrite in Apache
3. Access: http://localhost/STI_Queuing_System/kiosk
4. Login: admin / admin123
```

## 📁 File Locations

| What | Where |
|------|-------|
| Pages | `pages/*.php` |
| JavaScript | `js/*.js` |
| API | `api/*.php` |
| Styles | `css/style.css` |
| Database | `includes/db.php` |

## 🔗 URLs

```
Kiosk:     /kiosk
Monitor:   /monitor
Login:     /staff-login
Dashboard: /staff-dashboard
```

## 🔌 API Endpoints

```php
POST   /api/create-ticket.php      // Create new ticket
GET    /api/get-queue.php          // Get all queues
POST   /api/call-next.php          // Call next student
POST   /api/complete-ticket.php    // Mark done/cancelled
POST   /api/login.php              // Staff login
GET    /api/logout.php             // Staff logout
```

## 📊 Database Tables

```sql
departments      // Cashier, Admission, Registrar
staff            // Staff user accounts
queue_tickets    // All queue tickets
```

## 🎯 Key Functions

### Kiosk (js/kiosk.js)
```javascript
openInput(department)       // Open student form
generateTicket()            // Create ticket via API
goHome()                    // Return to welcome screen
```

### Monitor (js/monitor.js)
```javascript
updateQueue()               // Fetch and display queue
updateDepartment(dept)      // Update specific dept
```

### Dashboard (js/staff-dashboard.js)
```javascript
callNext()                  // Call next student
markDone()                  // Complete transaction
markNoShow()                // Cancel ticket
```

## 🔐 Session Variables

```php
$_SESSION['staff_id']           // Staff user ID
$_SESSION['staff_name']         // Staff full name
$_SESSION['department_id']      // Department ID
$_SESSION['department_name']    // Department name
```

## 🎨 CSS Classes

```css
.kiosk-container               // Kiosk wrapper
.monitor-container             // Monitor wrapper
.dashboard-container           // Dashboard wrapper
.dept-card                     // Department button
.ticket-card                   // Ticket display
.is-priority                   // Priority styling
```

## 🔧 Common Tasks

### Add New Department
```sql
INSERT INTO departments (name, prefix, window_number) 
VALUES ('Finance', 'F', 4);
```

### Create Staff User
```sql
-- Password: admin123
INSERT INTO staff (username, password, full_name, department_id) 
VALUES ('cashier1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 1);
```

### Change Ticket Format
Edit: `api/create-ticket.php` → `generateTicketNumber()`

### Modify Refresh Rate
```javascript
// Monitor (js/monitor.js)
setInterval(updateQueue, 3000);  // Change 3000 to desired ms

// Dashboard (js/staff-dashboard.js)
setInterval(updateQueueCount, 5000);  // Change 5000 to desired ms
```

## 🐛 Debug Tips

### Enable PHP Errors
```php
// Add to top of any PHP file
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Check Database Connection
```php
// In includes/db.php, add:
echo "Connected: " . $conn->host_info;
```

### View API Response
```javascript
// In browser console (F12)
fetch('/STI_Queuing_System/api/get-queue.php')
  .then(r => r.json())
  .then(console.log);
```

### Check Session
```php
// Add to any page
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
```

## 📝 Ticket Status Values

```
'waiting'    // In queue
'serving'    // Currently being served
'completed'  // Transaction done
'cancelled'  // No show / cancelled
```

## 🔄 Queue Priority Logic

```sql
ORDER BY 
  is_priority DESC,    // Priority tickets first
  created_at ASC       // Then by creation time
```

## 💾 Backup Database

```bash
# In XAMPP shell or command prompt
cd C:\xampp\mysql\bin
mysqldump -u root queue_db > backup.sql
```

## 🎬 Testing Workflow

```
1. Generate ticket at /kiosk
2. Check appears on /monitor
3. Login at /staff-login
4. Call ticket at /staff-dashboard
5. Verify status on /monitor
6. Mark as done
```

## 📱 Mobile Responsive

All pages are responsive. Key breakpoints:
- Kiosk: Optimized for tablet (portrait)
- Monitor: Optimized for large display
- Dashboard: Optimized for desktop/tablet

## 🚨 Error Messages

```javascript
// API always returns:
{ success: true/false, message: "..." }

// Check for:
if (result.success) {
  // Handle success
} else {
  alert(result.message);
}
```

## 🎯 Department IDs

```
1 = Cashier
2 = Admission
3 = Registrar
```

## 🔑 Password Hashing

```php
// To create new password hash
$hash = password_hash('yourpassword', PASSWORD_DEFAULT);
echo $hash;  // Use this in SQL INSERT
```

## 📦 Required XAMPP Modules

```
✓ Apache (with mod_rewrite)
✓ MySQL
✓ PHP 7.4+
```

## 🎨 Customization Points

| What | File | Line |
|------|------|------|
| Colors | `css/style.css` | Varies |
| Department names | `config/database.sql` | Line 40 |
| Ticket format | `api/create-ticket.php` | Line 16 |
| Refresh rate | `js/monitor.js` | Line 72 |
| Window numbers | `config/database.sql` | Line 40 |

## 🏗️ Build Process

No build process required! Pure vanilla:
- ✓ No npm
- ✓ No webpack
- ✓ No dependencies
- ✓ Just PHP, MySQL, JavaScript

## 🌐 Browser Support

```
✓ Chrome 90+
✓ Firefox 88+
✓ Safari 14+
✓ Edge 90+
```

## 📚 Documentation Files

```
README.md         // Full project docs
SETUP.md          // Installation guide
ARCHITECTURE.md   // System design
SUMMARY.md        // What was changed
REFERENCE.md      // This file
```

## ⚡ Performance Tips

1. **Database indexes** already on foreign keys
2. **API caching** - Consider adding for monitor
3. **Date filtering** - Only show today's tickets
4. **Limit results** - Already in SQL queries

## 🔐 Security Checklist

```
✓ Prepared statements (SQL injection protected)
✓ Password hashing (bcrypt)
✓ Session management
✓ Input validation (client + server)
□ HTTPS (enable in production)
□ CSRF tokens (add for production)
□ Rate limiting (add for production)
```

## 🎯 Next Features to Add

- [ ] Print ticket functionality
- [ ] SMS notifications
- [ ] Sound alerts
- [ ] Daily reports
- [ ] Analytics dashboard
- [ ] Multi-language support

## 💡 Pro Tips

1. **Keep pages folder restricted** - .htaccess already blocks direct access
2. **Use browser dev tools** - F12 for debugging
3. **Check Apache logs** - C:\xampp\apache\logs\error.log
4. **Database migrations** - Keep SQL files for changes
5. **Test on different screens** - Kiosk on tablet, monitor on TV

---

**Remember:** All files are well-commented. Read the code for more details!
