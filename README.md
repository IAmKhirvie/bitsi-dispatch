# BITSI Dispatch

A real-time bus dispatch management system for **Bicol Isarog Transport System, Inc. (BITSI)**. Digitizes the daily bus status report into a modern web application with a live dispatch board, fleet management, driver operations, attendance tracking, SMS notifications, reporting, and a full audit trail.

---

## Tech Stack

| Layer            | Technology                                     |
| ---------------- | ---------------------------------------------- |
| Backend          | Laravel 12 (PHP 8.2+)                          |
| Frontend         | Blade templates + Livewire 3 + Alpine.js       |
| Auth             | Laravel Fortify + Jetstream                    |
| CSS              | Tailwind CSS 3.4 (dark mode support)           |
| Database         | MySQL 8+ (recommended) / SQLite (dev)          |
| Queue            | Database driver                                |
| Permissions      | Spatie laravel-permission                       |
| SMS              | Semaphore API (Philippine provider)             |
| Excel Export     | maatwebsite/excel                               |
| PDF Export       | barryvdh/laravel-dompdf                         |
| Build Tool       | Vite 6                                          |

---

## Features Overview

### Dispatch Operations
- **Live Dispatch Board** — Real-time Livewire-powered board with auto-refresh every 10 seconds (`wire:poll.10s`), so multiple dispatchers see each other's updates instantly
- **Trip Code Auto-fill** — Select a trip code and all fields (route, bus type, terminals, schedule, direction) populate automatically
- **Dual Driver Support** — Assign Driver 1 and Driver 2 per dispatch entry for co-driver trips
- **Dispatch Status Tracking** — Full lifecycle: Scheduled → Departed → On Route → Arrived (with Delayed and Cancelled states)
- **Daily Dispatch Days** — Initialize a dispatch day per date, add/edit/delete entries, reorder by sort order
- **Inline Add/Edit/Delete** — Modal dialogs powered by Livewire for adding and editing entries without page reloads

### Fleet & Vehicle Management
- **Vehicle Registry** — Full CRUD for fleet vehicles with bus number, brand, type, plate number, and status tracking
- **Vehicle Statuses** — OK (Available), Under Repair, For Maintenance (PMS), In Transit, Lutaw (Idle)
- **PMS Tracking** — Preventive Maintenance Schedule monitoring by kilometers or trip count
- **PMS Dashboard Alerts** — Vehicles at 80%+ PMS threshold appear as WARNING (orange) or OVERDUE (red) on the dashboard with progress bars
- **Idle Day Tracking** — Track how many days a vehicle has been unused

### Driver Management
- **Driver Registry** — Full CRUD with name, phone, license number, and active status
- **Quick Status Toggles** — Set drivers to Available, Dispatched, On Route, or On Leave with inline buttons
- **Active/Inactive Toggle** — Enable or disable drivers from being assigned
- **SMS to Drivers** — Send schedule notifications or custom messages via Semaphore API
- **Schedule Preview** — Preview the SMS schedule message before sending

### Driver Attendance
- **Daily Attendance Tracking** — Initialize attendance for each dispatch day, track check-in/check-out times
- **Status Management** — Mark drivers as On Time, Late (with minutes), Absent, or Excused
- **Auto-Absent Detection** — Scheduled command auto-marks absent drivers after configurable timeout
- **Configurable Thresholds** — Late threshold (default 15 min), pre-departure alerts (15 min), auto-absent timeout (30 min)
- **Attendance Alerts** — Automatic upcoming/late/absent alerts generated every 5 minutes
- **Mobile Check-in API** — REST API endpoints for future mobile app integration

### SMS Notifications
- **Semaphore Integration** — Philippine SMS provider for driver notifications
- **Queued Delivery** — SMS sent via database queue with 3 retries and 30-second backoff
- **Priority Messages** — Schedule notifications use the `high` queue priority
- **SMS Log Dashboard** — Admin view of all SMS history with sent/failed/pending stats
- **Retry Failed SMS** — One-click retry for failed messages from the SMS dashboard
- **Custom SMS** — Send custom messages (max 160 chars) to any driver from their management page

### Reporting
- **Daily Summary Reports** — Trip breakdown by direction (SB/NB) and destination (Naga, Legazpi, Sorsogon, Virac, Masbate, Tabaco, Visayas, Cargo)
- **Report Summary Table** — Paginated with date range filtering and aggregate totals
- **Excel Export** — Download dispatch data per day as `.xlsx`
- **PDF Export** — Download formatted reports as `.pdf`
- **Historical Records** — Search and filter past dispatch entries by bus, route, trip code, date range, direction, and status

### Administration & Security
- **Role-Based Access Control** — Three roles: Admin, Operations Manager, Dispatcher
- **Role-Based Dashboard** — Admin sees everything (audit logs, SMS stats, vehicle/driver stats, PMS alerts). Operations Manager sees vehicle stats + PMS alerts. Dispatchers see trip stats + quick dispatch actions
- **User Management** — Full CRUD with role assignment, phone number, and active/inactive toggle
- **Audit Log Viewer** — Searchable admin page tracking all CRUD operations with old/new value diffs, filterable by user, action, model type, and date range
- **Soft Deletes** — Drivers, Vehicles, Trip Codes, Dispatch Days, and Dispatch Entries use soft deletes with "Show Deleted" toggle and restore functionality in admin tables
- **Two-Factor Authentication** — Jetstream 2FA setup and management
- **Profile Photos** — Upload and manage profile photos (shown in sidebar)
- **Browser Session Management** — View and logout other browser sessions
- **Account Deletion** — Self-service account deletion with password confirmation

### User Interface
- **Dark Mode** — Full dark mode support with toggle
- **Responsive Sidebar** — Collapsible navigation with role-based menu items
- **Server-Side Pagination** — All tables use Livewire's `WithPagination` (15-20 items/page)
- **Server-Side Search** — Debounced LIKE queries with URL state persistence via `$queryString`
- **Badge System** — Color-coded status badges throughout the app driven by PHP enums

---

## CRUD Modules

### Admin-Only Modules

| Module | Route | Features |
| --- | --- | --- |
| **Users** | `/admin/users` | Create, Read, Update, Delete users. Assign roles (Admin/Ops Manager/Dispatcher). Toggle active status. Search by name/email, filter by role |
| **Vehicles** | `/admin/vehicles` | Create, Read, Update, Delete vehicles. Track bus number, brand, type, plate, PMS values. Show deleted + restore. Search by bus no/brand/plate, filter by status |
| **Drivers** | `/admin/drivers` | Create, Read, Update, Delete drivers. Toggle active, set status (Available/Dispatched/On Route/On Leave). Send SMS. Show deleted + restore. Search by name/phone/license, filter by status |
| **Trip Codes** | `/admin/trip-codes` | Create, Read, Update, Delete trip codes. Toggle active. Define operator, origin, destination, bus type, scheduled time, direction. Show deleted + restore. Search by code/operator/terminal, filter by direction |
| **Attendance** | `/admin/attendance` | Initialize daily attendance. Mark late/absent/excused. Override status. Configure thresholds. View alerts |
| **Audit Logs** | `/admin/audit-logs` | Read-only. View all CRUD audit trails. Expand rows to see old/new value diffs. Filter by user, action, model, date range |
| **SMS Logs** | `/admin/sms-logs` | Read-only + Retry. View SMS delivery history. Retry failed messages. Today's stats (sent/failed/pending). Filter by phone/message, status, date |

### All-Role Modules

| Module | Route | Features |
| --- | --- | --- |
| **Dashboard** | `/dashboard` | Role-based stats cards, PMS alerts, today's summary, quick actions, recent entries. Admin also sees audit log + SMS summary cards |
| **Dispatch Board** | `/dispatch` | Create/Read/Update/Delete dispatch entries per day. Auto-fill from trip codes and vehicles. Real-time auto-refresh (10s polling). Modal-based add/edit forms |
| **Reports** | `/reports` | View daily reports with summary breakdowns. Export to Excel and PDF. Paginated summary table with date range filtering |
| **History** | `/history` | Search past dispatch entries. Filter by date range, direction, status. Full-text search on bus number, route, trip code |

### Settings

| Module | Route | Features |
| --- | --- | --- |
| **Profile** | `/settings/profile` | Update name, email, profile photo. Change password. Set up 2FA. Manage browser sessions. Delete account |
| **Appearance** | `/settings/appearance` | Toggle dark/light mode |

---

## Database Schema

### Core Tables

| Table | Purpose | Key Fields |
| --- | --- | --- |
| `users` | System users | name, email, password, role, phone, is_active, profile_photo_path, 2FA columns |
| `drivers` | Bus drivers | name, phone, license_number, is_active, status, deleted_at |
| `vehicles` | Fleet vehicles | bus_number, brand, bus_type, plate_number, status, pms_unit, pms_threshold, current_pms_value, idle_days, deleted_at |
| `trip_codes` | Route templates | code, operator, origin_terminal, destination_terminal, bus_type, scheduled_departure_time, direction, is_active, deleted_at |
| `dispatch_days` | Daily dispatch container | service_date (unique), created_by, notes, deleted_at |
| `dispatch_entries` | Individual trip assignments | dispatch_day_id, vehicle_id, trip_code_id, driver_id, driver2_id, brand, bus_number, route, bus_type, terminals, times, direction, status, remarks, sort_order, deleted_at |

### Summary & Reporting

| Table | Purpose | Key Fields |
| --- | --- | --- |
| `daily_summaries` | Per-day aggregate | dispatch_day_id, total_trips |
| `daily_summary_items` | Normalized category counts | daily_summary_id, category (sb/nb/naga/legazpi/...), trip_count |

### Attendance

| Table | Purpose | Key Fields |
| --- | --- | --- |
| `driver_attendances` | Daily check-in/out records | driver_id, dispatch_entry_id, attendance_date, check_in_time, check_out_time, status, minutes_late, notes, marked_by |
| `attendance_settings` | Key-value config | key, value (late_threshold_minutes, pre_departure_alert_minutes, etc.) |
| `attendance_alerts` | System-generated alerts | driver_id, dispatch_entry_id, alert_date, alert_type (upcoming/late/absent), is_read |

### Logging & Notifications

| Table | Purpose | Key Fields |
| --- | --- | --- |
| `audit_logs` | CRUD audit trail | user_id, action, auditable_type, auditable_id, old_values (JSON), new_values (JSON), ip_address |
| `sms_logs` | SMS delivery tracking | dispatch_entry_id, recipient_phone, message, status, provider_message_id, sent_at |

### System

| Table | Purpose |
| --- | --- |
| `pms_settings` | Named PMS threshold presets (Standard 15k km, Heavy Duty 20k km, Trip-based 500 trips) |
| `permissions` / `roles` / pivot tables | Spatie role-based access control |
| `personal_access_tokens` | Laravel Sanctum API tokens |
| `sessions` | Database session driver |
| `cache` / `cache_locks` | Database cache driver |
| `jobs` / `job_batches` / `failed_jobs` | Database queue driver |

---

## Enums

| Enum | Values |
| --- | --- |
| `UserRole` | `admin`, `operations_manager`, `dispatcher` |
| `BusType` | `regular`, `deluxe`, `super_deluxe`, `elite`, `sleeper`, `single_seater`, `skybus` |
| `Direction` | `SB` (Southbound), `NB` (Northbound) |
| `DispatchStatus` | `scheduled`, `departed`, `on_route`, `delayed`, `cancelled`, `arrived` |
| `DriverStatus` | `available`, `dispatched`, `on_route`, `on_leave` |
| `VehicleStatus` | `OK`, `UR` (Under Repair), `PMS`, `In Transit`, `Lutaw` (Idle) |
| `PmsUnit` | `kilometers`, `trips` |
| `SmsStatus` | `pending`, `sent`, `failed` |

---

## User Roles & Permissions

| Feature | Admin | Ops Manager | Dispatcher |
| --- | --- | --- | --- |
| Dashboard (trip stats) | Yes | Yes | Yes |
| Dashboard (vehicle/driver stats) | Yes | Yes | No |
| Dashboard (PMS alerts) | Yes | Yes | No |
| Dashboard (audit logs, SMS stats) | Yes | No | No |
| Dispatch Board | Yes | Yes | Yes |
| Reports & History | Yes | Yes | Yes |
| User Management | Yes | No | No |
| Vehicle Management | Yes | No | No |
| Driver Management | Yes | No | No |
| Trip Code Management | Yes | No | No |
| Attendance Management | Yes | No | No |
| Audit Log Viewer | Yes | No | No |
| SMS Log Viewer | Yes | No | No |
| Profile / Settings | Yes | Yes | Yes |

---

## Prerequisites

| Software     | Version  |
| ------------ | -------- |
| **PHP**      | >= 8.2   |
| **Composer** | >= 2.x   |
| **Node.js**  | >= 18.x  |
| **npm**      | >= 9.x   |
| **Git**      | >= 2.x   |
| **MySQL**    | >= 8.0   |

### Required PHP Extensions

- `pdo_mysql` (for MySQL) or `pdo_sqlite` + `sqlite3` (for SQLite)
- `mbstring`, `openssl`, `fileinfo`, `gd` or `imagick`, `xml`, `zip`

### Platform Setup

<details>
<summary><strong>Windows (XAMPP recommended)</strong></summary>

1. Download XAMPP from https://www.apachefriends.org/
2. Add PHP to PATH: `C:\xampp\php`
3. Start MySQL from XAMPP Control Panel
4. Install Composer from https://getcomposer.org/Composer-Setup.exe
5. Install Node.js LTS from https://nodejs.org/
</details>

<details>
<summary><strong>macOS</strong></summary>

```bash
brew install php mysql composer node
brew services start mysql
```
</details>

<details>
<summary><strong>Linux (Ubuntu/Debian)</strong></summary>

```bash
sudo apt update
sudo apt install php php-mysql php-sqlite3 php-mbstring php-xml php-zip php-gd composer nodejs npm mysql-server
sudo systemctl start mysql
```
</details>

---

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/IAmKhirvie/bitsi-dispatch.git
cd bitsi-dispatch

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env          # macOS / Linux
copy .env.example .env         # Windows CMD

# 4. Generate application key
php artisan key:generate

# 5. Create symlink for profile photo uploads
php artisan storage:link
```

### Database Setup

**Option A: MySQL (Recommended)**

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS bitsi_dispatch;"
# Update .env: DB_CONNECTION=mysql, DB_DATABASE=bitsi_dispatch
php artisan migrate:fresh --seed
```

**Option B: SQLite (Zero Config)**

```bash
touch database/database.sqlite                    # macOS / Linux
type nul > database\database.sqlite               # Windows CMD
# Update .env: DB_CONNECTION=sqlite
php artisan migrate:fresh --seed
```

### Build & Run

```bash
# Build frontend assets
npm run build

# Start development server (runs Laravel + Queue + Vite + Logs concurrently)
composer run dev
```

Open http://127.0.0.1:8000 in your browser.

---

## Test Accounts

| Role                | Email                   | Password   |
| ------------------- | ----------------------- | ---------- |
| Admin               | admin@bitsi.com         | password   |
| Operations Manager  | opsmanager@bitsi.com    | password   |
| Dispatcher          | dispatcher@bitsi.com    | password   |

> Admin users can access all management pages (Users, Vehicles, Drivers, Trip Codes, Attendance, Audit Logs, SMS Logs) via the sidebar.

---

## SMS Setup (Optional)

SMS notifications are powered by [Semaphore](https://semaphore.co/).

```env
SEMAPHORE_API_KEY=your_api_key_here
SEMAPHORE_SENDER_NAME=BITSI
```

When configured, the system:
- Sends SMS to drivers when assigned to a dispatch or when status changes
- Queues messages with 3 retries and 30-second backoff
- Logs all SMS attempts in the SMS dashboard
- Allows retry of failed messages from the admin SMS Log page

---

## Routes

| Section        | URL                        | Description                              |
| -------------- | -------------------------- | ---------------------------------------- |
| Landing Page   | `/`                        | Public BITSI landing page                |
| Dashboard      | `/dashboard`               | Role-based stats and quick actions       |
| Dispatch Board | `/dispatch`                | Live dispatch board (auto-refresh 10s)   |
| Reports        | `/reports`                 | Trip analytics, summaries, and exports   |
| History        | `/history`                 | Search past dispatch entries             |
| Users          | `/admin/users`             | User management (Admin)                  |
| Trip Codes     | `/admin/trip-codes`        | Trip code management (Admin)             |
| Vehicles       | `/admin/vehicles`          | Vehicle/bus management (Admin)           |
| Drivers        | `/admin/drivers`           | Driver management + SMS (Admin)          |
| Attendance     | `/admin/attendance`        | Driver attendance tracking (Admin)       |
| Audit Logs     | `/admin/audit-logs`        | CRUD audit trail viewer (Admin)          |
| SMS Logs       | `/admin/sms-logs`          | SMS delivery log + retry (Admin)         |
| Profile        | `/settings/profile`        | Profile, password, 2FA, sessions         |
| Appearance     | `/settings/appearance`     | Dark/light mode toggle                   |

---

## Project Structure

```
bitsi-dispatch/
├── app/
│   ├── Console/Commands/        # CheckAttendanceAlerts (runs every 5 min)
│   ├── Enums/                   # 8 PHP-backed enums (UserRole, BusType, etc.)
│   ├── Exports/                 # DispatchExport, DailySummaryExport
│   ├── Http/Controllers/
│   │   ├── Admin/               # User, Vehicle, Driver, TripCode, Attendance,
│   │   │                        #   DriverNotification controllers
│   │   ├── Api/                 # DriverAttendance API (mobile check-in)
│   │   ├── Dispatch/            # DispatchDay + DispatchEntry controllers
│   │   ├── Report/              # Reports + Excel/PDF export
│   │   └── Settings/            # Profile + Password controllers
│   ├── Jobs/                    # SendSmsJob (queued, 3 retries)
│   ├── Livewire/
│   │   ├── Admin/               # UserTable, DriverTable, VehicleTable,
│   │   │                        #   TripCodeTable, AttendanceTable,
│   │   │                        #   AttendanceSettings, AuditLogTable, SmsLogTable
│   │   ├── Report/              # ReportSummaryTable
│   │   ├── DispatchBoard.php    # Live dispatch board with auto-refresh
│   │   └── HistoryTable.php     # Historical records search
│   ├── Models/                  # 14 Eloquent models
│   ├── Observers/               # DispatchEntryObserver (auto SMS + summary)
│   ├── Services/                # SemaphoreService, SummaryService, DispatchService
│   └── Traits/                  # Auditable (auto audit logging)
├── config/
│   └── semaphore.php            # SMS provider configuration
├── database/
│   ├── migrations/              # 26 migration files
│   └── seeders/                 # Sample data (users, drivers, vehicles, trip codes, dispatch)
├── resources/views/
│   ├── admin/                   # Admin CRUD views + shared _form partials
│   ├── dispatch/                # Dispatch board wrapper
│   ├── reports/                 # Report views
│   ├── history/                 # Historical records
│   ├── livewire/                # Livewire component templates
│   ├── layouts/                 # App & guest layouts
│   ├── exports/                 # PDF Blade templates
│   ├── partials/                # Sidebar, header
│   └── settings/                # Profile, appearance
└── routes/
    ├── web.php                  # All web routes
    ├── settings.php             # Settings routes
    ├── api.php                  # Mobile API routes
    └── console.php              # Scheduled commands
```

---

## Architecture Notes

### Normalized Database (1NF-4NF)
The `daily_summaries` table uses a normalized `daily_summary_items` child table instead of hardcoded destination columns. Adding a new destination category requires zero schema changes.

### Audit Trail
The `Auditable` trait (applied to 6 models) automatically logs all create, update, delete, and restore operations with full old/new value diffs, user ID, and IP address.

### Soft Deletes
Drivers, Vehicles, Trip Codes, Dispatch Days, and Dispatch Entries support soft deletion. Admin tables include a "Show Deleted" toggle with one-click restore. Deleted records are preserved for audit integrity.

### Real-Time Updates
The dispatch board uses Livewire's `wire:poll.10s` for auto-refresh, allowing multiple dispatchers to see updates without manual page reloads.

### PMS (Preventive Maintenance Service)
Vehicles track current PMS value against a configurable threshold (by km or trips). Dashboard alerts surface vehicles at 80%+ threshold. Three preset PMS settings ship with the seeder.

### SMS Architecture
`SemaphoreService` → `SendSmsJob` (queued, 3 retries, 30s backoff) → `SmsLog`. All attempts are logged. Failed messages can be retried from the admin dashboard.

### Scheduled Commands
`attendance:check-alerts` runs every 5 minutes to detect upcoming, late, and absent drivers against configurable thresholds.

---

## Common Commands

```bash
# Start development server
composer run dev

# Build for production
npm run build

# Run migrations
php artisan migrate

# Fresh database with sample data
php artisan migrate:fresh --seed

# Run the queue worker (for SMS)
php artisan queue:work --queue=high,default

# Run attendance alert check manually
php artisan attendance:check-alerts

# Clear all caches
php artisan optimize:clear

# Run tests
php artisan test
```

---

## Troubleshooting

<details>
<summary><strong>"Address already in use" on port 8000</strong></summary>

```bash
# macOS / Linux
lsof -ti:8000 | xargs kill -9

# Windows PowerShell
Stop-Process -Id (Get-NetTCPConnection -LocalPort 8000).OwningProcess -Force
```
</details>

<details>
<summary><strong>MySQL "Access denied" or "Connection refused"</strong></summary>

Make sure MySQL is running. Check `.env` credentials match your MySQL setup.

```bash
# XAMPP: Open Control Panel → Start MySQL
# macOS: brew services start mysql
# Linux: sudo systemctl start mysql
```
</details>

<details>
<summary><strong>Switching between MySQL and SQLite</strong></summary>

Update `DB_CONNECTION` in `.env` to `mysql` or `sqlite`, create the database if needed, then run `php artisan migrate:fresh --seed`.
</details>

<details>
<summary><strong>Profile photos not showing</strong></summary>

Run `php artisan storage:link` to create the `public/storage` symlink.
</details>

<details>
<summary><strong>SMS not sending</strong></summary>

1. Check `SEMAPHORE_API_KEY` is set in `.env`
2. Run the queue worker: `php artisan queue:work`
3. Check SMS Logs at `/admin/sms-logs` for error details
</details>

---

## Changelog

### v1.3 (2026-02-19)
- Added Audit Log Viewer with search, filters, and expandable change diffs
- Added SMS Dashboard with delivery stats and retry for failed messages
- Added Vehicle PMS maintenance alerts on dashboard
- Added real-time dispatch board with 10-second auto-refresh
- Added role-based dashboard (Admin / Ops Manager / Dispatcher)
- Added soft deletes to Drivers, Vehicles, Trip Codes, Dispatch Days, Dispatch Entries
- Added "Show Deleted" toggle and restore in admin tables
- Normalized daily_summaries into daily_summary_items (NF 1-4 compliant)
- Switched default database to MySQL
- Enabled Jetstream profile photos, 2FA, browser session management, account deletion
- Added sidebar items for Audit Logs and SMS Logs

### v1.2 (2026-02-10)
- Migrated frontend from Vue 3 / Inertia.js to Blade / Livewire 3
- Added Laravel Jetstream authentication with 2FA support
- Added Spatie laravel-permission for role-based access
- Added driver attendance management with alerts and configurable thresholds
- Added Force SMS and custom SMS features for drivers
- Added dual driver support (Driver 1 & Driver 2) per dispatch entry
- Removed GPS tracking feature
- Extracted shared form partials and validation rules
- Added enum-driven badge classes and filter dropdowns

### v1.1
- Initial Vue 3 / Inertia.js implementation
- Dispatch board, reports, GPS tracking, SMS notifications

### v1.0
- Project scaffolding and database design

---

## Tools Used

| Tool | Purpose |
| --- | --- |
| **VS Code** | Primary code editor and IDE |
| **Claude Code** | AI-assisted development — code generation, debugging, refactoring |
| **ChatGPT** | Research, documentation drafting, architectural planning |
| **DeepSeek** | Code analysis and problem-solving |
| **Laravel Herd / XAMPP** | Local PHP + MySQL development environment |
| **Git + GitHub** | Version control and repository hosting |
| **Composer** | PHP dependency management |
| **npm + Vite** | Frontend asset bundling and build tooling |
| **Postman** | API endpoint testing (attendance mobile API) |
| **MySQL Workbench** | Database design and query testing |

---

## License

This project is proprietary software for Bicol Isarog Transport System, Inc.
