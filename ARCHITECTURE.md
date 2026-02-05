# SYSTEM FLOW DIAGRAM

## Overall Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                     STI QUEUING SYSTEM                          │
└─────────────────────────────────────────────────────────────────┘

┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│   STUDENT    │      │    STAFF     │      │   MONITOR    │
│    KIOSK     │      │  DASHBOARD   │      │   DISPLAY    │
└──────┬───────┘      └──────┬───────┘      └──────┬───────┘
       │                     │                      │
       │                     │                      │
       ▼                     ▼                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                          API LAYER                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐           │
│  │ create-ticket│  │  call-next   │  │  get-queue   │           │
│  └──────────────┘  └──────────────┘  └──────────────┘           │
└─────────────────────────────────────────────────────────────────┘
       │                     │                      │
       └─────────────────────┴──────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                      MySQL DATABASE                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐           │
│  │ departments  │  │    staff     │  │queue_tickets │           │
│  └──────────────┘  └──────────────┘  └──────────────┘           │
└─────────────────────────────────────────────────────────────────┘
```

## Student Journey Flow

```
START → Kiosk Page → Select Department → Enter Details → Get Ticket → END
  │         │              │                  │              │
  │         │              │                  │              │
  ▼         ▼              ▼                  ▼              ▼
┌───┐   ┌──────┐      ┌──────┐         ┌──────┐       ┌────────┐
│ 1 │   │  2   │      │  3   │         │  4   │       │   5    │
└───┘   └──────┘      └──────┘         └──────┘       └────────┘
```

### Step-by-Step:

1. **Student arrives at Kiosk**
   - URL: `/kiosk`
   - File: `pages/kiosk.php`
   - JS: `js/kiosk.js`

2. **Selects Department**
   - Options: Cashier, Admission, Registrar
   - Function: `openInput(department)`

3. **Enters Information**
   - Student Name (required)
   - Student Number (optional)
   - Priority checkbox (PWD/Senior/Pregnant)

4. **Generates Ticket**
   - Calls API: `POST /api/create-ticket.php`
   - Receives ticket number (e.g., C-001, P-042)
   - Stores in database

5. **Receives Ticket**
   - Displays ticket number
   - Shows department
   - Shows student name
   - Priority badge (if applicable)

## Staff Workflow

```
┌──────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐
│  LOGIN   │ →   │ DASHBOARD│ →   │CALL NEXT │ →   │ COMPLETE │
└──────────┘     └──────────┘     └──────────┘     └──────────┘
     │                │                  │                │
     │                │                  │                │
     ▼                ▼                  ▼                ▼
┌────────┐      ┌─────────┐       ┌─────────┐      ┌─────────┐
│api/    │      │ View    │       │api/     │      │api/     │
│login   │      │ Queue   │       │call-next│      │complete │
└────────┘      └─────────┘       └─────────┘      └─────────┘
```

### Process:

1. **Login** (`/staff-login`)
   - Enter username/password
   - Select department
   - API: `POST /api/login.php`
   - Creates session

2. **Dashboard** (`/staff-dashboard`)
   - View queue count
   - See current ticket
   - Session check

3. **Call Next**
   - Click "Call Next" button
   - API: `POST /api/call-next.php`
   - Gets highest priority ticket
   - Updates status to "serving"

4. **Complete Transaction**
   - Options:
     - ✓ Done (completed)
     - 🔄 Recall (notify again)
     - ✗ No Show (cancelled)
   - API: `POST /api/complete-ticket.php`

## Monitor Display Flow

```
┌──────────────────────────────────────────────────────┐
│              QUEUE MONITOR DISPLAY                    │
│                                                       │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐          │
│  │ CASHIER  │  │ADMISSION │  │REGISTRAR │          │
│  ├──────────┤  ├──────────┤  ├──────────┤          │
│  │ Serving: │  │ Serving: │  │ Serving: │          │
│  │  C-042   │  │  A-028   │  │  R-015   │          │
│  ├──────────┤  ├──────────┤  ├──────────┤          │
│  │ Next:    │  │ Next:    │  │ Next:    │          │
│  │  C-043   │  │  A-029   │  │  R-016   │          │
│  │  C-044   │  │  A-030   │  │  R-017   │          │
│  │  C-045   │  │  A-031   │  │  R-018   │          │
│  └──────────┘  └──────────┘  └──────────┘          │
└──────────────────────────────────────────────────────┘
           │
           │ Auto-refresh every 3 seconds
           ▼
      GET /api/get-queue.php
           │
           ▼
      MySQL Database
```

## Database Relationships

```
┌─────────────────┐
│  departments    │
│─────────────────│
│ id (PK)         │◄────┐
│ name            │     │
│ prefix          │     │
│ window_number   │     │
└─────────────────┘     │
                        │
        ┌───────────────┴────────────────┐
        │                                │
        │                                │
┌───────┴─────────┐          ┌──────────┴────────┐
│     staff       │          │  queue_tickets    │
│─────────────────│          │───────────────────│
│ id (PK)         │          │ id (PK)           │
│ username        │          │ ticket_number     │
│ password        │          │ student_name      │
│ full_name       │          │ student_number    │
│ department_id(FK)│         │ department_id (FK)│
└─────────────────┘          │ is_priority       │
                             │ status            │
                             │ called_at         │
                             │ completed_at      │
                             └───────────────────┘
```

## API Request/Response Examples

### Create Ticket

**Request:**
```javascript
POST /api/create-ticket.php
{
  "student_name": "Juan Dela Cruz",
  "student_number": "2024-12345",
  "department": "Cashier",
  "is_priority": false
}
```

**Response:**
```javascript
{
  "success": true,
  "ticket_number": "C-001",
  "department": "Cashier",
  "student_name": "Juan Dela Cruz",
  "student_number": "2024-12345",
  "is_priority": false
}
```

### Get Queue

**Request:**
```javascript
GET /api/get-queue.php
```

**Response:**
```javascript
{
  "success": true,
  "tickets": [
    {
      "id": 1,
      "ticket_number": "C-001",
      "student_name": "Juan Dela Cruz",
      "department_name": "Cashier",
      "status": "serving",
      "is_priority": false
    },
    {
      "id": 2,
      "ticket_number": "C-002",
      "student_name": "Maria Santos",
      "department_name": "Cashier",
      "status": "waiting",
      "is_priority": false
    }
  ]
}
```

### Call Next

**Request:**
```javascript
POST /api/call-next.php
{
  "department_id": 1
}
```

**Response:**
```javascript
{
  "success": true,
  "ticket": {
    "id": 2,
    "ticket_number": "C-002",
    "student_name": "Maria Santos",
    "is_priority": false
  }
}
```

## File Responsibilities

| File | Responsibility |
|------|----------------|
| `pages/kiosk.php` | Student interface HTML |
| `js/kiosk.js` | Kiosk logic + API calls |
| `api/create-ticket.php` | Generate tickets |
| `pages/monitor.php` | Display HTML |
| `js/monitor.js` | Auto-refresh logic |
| `api/get-queue.php` | Fetch queue data |
| `pages/staff-login.php` | Login form HTML |
| `js/staff-login.js` | Login submission |
| `api/login.php` | Authentication |
| `pages/staff-dashboard.php` | Dashboard HTML |
| `js/staff-dashboard.js` | Queue management |
| `api/call-next.php` | Get next ticket |
| `api/complete-ticket.php` | Mark tickets done |

## Ticket Number Format

```
Format: [PREFIX]-[NUMBER]

Regular Tickets:
- C-001  (Cashier)
- A-001  (Admission)
- R-001  (Registrar)

Priority Tickets:
- P-001  (Priority - any department)
- P-042  (Priority)
```

Priority tickets always appear first in queue regardless of creation time.

## Session Flow

```
1. User logs in → api/login.php
2. Session created:
   - staff_id
   - staff_name
   - department_id
   - department_name
3. Redirect to dashboard
4. Dashboard checks session
5. If no session → redirect to login
6. User logs out → api/logout.php → session destroyed
```

---

This system provides a complete, modern queuing solution with clean architecture and real-time updates!
