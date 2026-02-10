# BITSI Dispatch v1.2

A real-time bus dispatch management system for **Bicol Isarog Transport System, Inc. (BITSI)**. Digitizes the daily bus status report into a modern web application with a live dispatch board, driver and vehicle management, attendance tracking, SMS notifications, and daily reports with Excel/PDF export.

---

## Tech Stack

| Layer            | Technology                                     |
| ---------------- | ---------------------------------------------- |
| Backend          | Laravel 12 (PHP 8.2+)                          |
| Frontend         | Blade templates + Livewire 3                   |
| Auth             | Laravel Fortify + Jetstream (2FA support)      |
| CSS              | Tailwind CSS 3.4                               |
| Database         | MySQL 8+ (recommended) or SQLite (zero config) |
| Queue            | Database driver                                |
| Permissions      | Spatie laravel-permission                       |
| SMS              | Semaphore API (Philippine provider)             |
| Excel Export     | maatwebsite/excel                               |
| PDF Export       | barryvdh/laravel-dompdf                         |
| Build Tool       | Vite 6                                          |

---

## Features

### Dispatch Operations
- **Live Dispatch Board** — Editable table powered by Livewire for real-time dispatch coordination
- **Trip Code Auto-fill** — Select a trip code and all fields (route, bus type, schedule) populate automatically
- **Dual Driver Support** — Assign Driver 1 and Driver 2 per dispatch entry
- **Dispatch Status Tracking** — Scheduled, Departed, On Route, Delayed, Cancelled, Arrived

### Fleet Management
- **Driver Management** — Quick status toggles (Available, Dispatched, On Route, On Leave)
- **Vehicle Management** — Track bus status (OK, Under Repair, PMS, In Transit, Lutaw/Idle)
- **PMS Tracking** — Preventive Maintenance Schedule monitoring by kilometers or trips

### Attendance & Notifications
- **Driver Attendance** — Mark late/absent drivers with configurable thresholds and alerts
- **SMS Notifications** — Auto-notify drivers via Semaphore when assigned or status changes

### Reporting
- **Daily Reports** — Summary cards, destination breakdowns, daily tables with totals
- **Excel & PDF Export** — Export dispatch reports per day
- **Historical Records** — Search and filter past dispatch entries

### Administration
- **Role-based Access** — Admin, Operations Manager, Dispatcher
- **User Management** — Full CRUD with role assignment and active/inactive toggle
- **Audit Logging** — Tracks all CRUD operations with old/new values
- **Two-Factor Authentication** — Jetstream 2FA support

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

> Admin users can access all management pages (Users, Vehicles, Drivers, Trip Codes, Attendance) via the sidebar.

---

## SMS Setup (Optional)

SMS notifications are powered by [Semaphore](https://semaphore.co/).

```env
SEMAPHORE_API_KEY=your_api_key_here
SEMAPHORE_SENDER_NAME=BITSI
```

When configured, the system automatically sends SMS to drivers when assigned to a dispatch or when status changes. Admins can also send custom SMS from the driver management page.

---

## Project Structure

```
bitsi-dispatch/
├── app/
│   ├── Enums/                 # 8 PHP enums (UserRole, BusType, DriverStatus, etc.)
│   ├── Exports/               # Excel export classes
│   ├── Http/Controllers/
│   │   ├── Admin/             # User, Vehicle, Driver, TripCode, Attendance CRUD
│   │   ├── Api/               # Driver attendance API
│   │   ├── Dispatch/          # Dispatch day & entry management
│   │   ├── Report/            # Reports & exports
│   │   └── Settings/          # Profile & password
│   ├── Jobs/                  # SendSmsJob (queued)
│   ├── Livewire/              # 9 Livewire components
│   │   ├── Admin/             # UserTable, DriverTable, VehicleTable, etc.
│   │   ├── Report/            # ReportSummaryTable
│   │   ├── DispatchBoard.php  # Live dispatch board
│   │   └── HistoryTable.php   # Historical records search
│   ├── Models/                # 13 Eloquent models
│   ├── Observers/             # DispatchEntryObserver (auto SMS + summary)
│   ├── Services/              # SemaphoreService, SummaryService, DispatchService
│   └── Traits/                # Auditable trait
├── database/
│   ├── migrations/            # 21 migration files
│   └── seeders/               # Sample data seeder
├── resources/
│   ├── views/
│   │   ├── admin/             # Admin CRUD views with shared _form partials
│   │   ├── dispatch/          # Dispatch board
│   │   ├── reports/           # Report views
│   │   ├── history/           # Historical records
│   │   ├── livewire/          # Livewire component templates
│   │   ├── layouts/           # App & guest layouts
│   │   └── exports/           # PDF Blade templates
│   ├── js/                    # app.js (dark mode)
│   └── css/                   # Tailwind CSS
└── routes/
    └── web.php                # All application routes
```

---

## Routes

| Section       | URL                      | Description                        |
| ------------- | ------------------------ | ---------------------------------- |
| Landing Page  | `/`                      | Public BITSI landing page          |
| Dashboard     | `/dashboard`             | Overview stats and quick actions   |
| Dispatch      | `/dispatch`              | Daily dispatch board (Livewire)    |
| Reports       | `/reports`               | Trip analytics and exports         |
| History       | `/history`               | Search past dispatch entries       |
| Users         | `/admin/users`           | User management (Admin)            |
| Trip Codes    | `/admin/trip-codes`      | Trip code management (Admin)       |
| Vehicles      | `/admin/vehicles`        | Vehicle/bus management (Admin)     |
| Drivers       | `/admin/drivers`         | Driver management (Admin)          |
| Attendance    | `/admin/attendance`      | Driver attendance (Admin)          |

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

# Clear all caches
php artisan optimize:clear
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

---

## Changelog

### v1.2 (2026-02-10)
- Migrated frontend from Vue 3 / Inertia.js to Blade / Livewire 3
- Added Laravel Jetstream authentication with 2FA support
- Added Spatie laravel-permission for role-based access
- Added driver attendance management with alerts and configurable thresholds
- Added Force SMS and custom SMS features for drivers
- Added dual driver support (Driver 1 & Driver 2) per dispatch entry
- Removed GPS tracking feature
- Removed code duplication across controllers, views, and enums
- Extracted shared form partials and validation rules
- Added enum-driven badge classes and filter dropdowns

### v1.1
- Initial Vue 3 / Inertia.js implementation
- Dispatch board, reports, GPS tracking, SMS notifications

### v1.0
- Project scaffolding and database design

---

## License

This project is proprietary software for Bicol Isarog Transport System, Inc.
