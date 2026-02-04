# BITSI Dispatch

A real-time bus dispatch management system for **Bicol Isarog Transport System, Inc. (BITSI)**. Digitizes the daily bus status report into a modern web application with dispatch board, reports, GPS tracking, driver management, and SMS notifications.

---

## Tech Stack

| Layer            | Technology                                    |
| ---------------- | --------------------------------------------- |
| Backend          | Laravel 12 (PHP 8.2+)                         |
| Frontend         | Vue 3 (Composition API) + TypeScript           |
| SPA Router       | Inertia.js v2                                  |
| CSS              | Tailwind CSS + shadcn-vue (Radix-based)       |
| Database         | MySQL 8+ (recommended) or SQLite (zero config) |
| Queue            | Database driver (works with both MySQL & SQLite)|
| Icons            | Lucide Vue                                     |
| Maps             | Leaflet.js + OpenStreetMap                     |
| SMS              | Semaphore API (Philippine provider)            |
| Excel Export     | maatwebsite/excel                              |
| PDF Export       | barryvdh/laravel-dompdf                        |
| Build Tool       | Vite                                           |

---

## Features

- **Dispatch Board** — Excel-like editable table for daily bus dispatch management
- **Trip Code Auto-fill** — Select a trip code and all fields populate automatically
- **Driver Management** — Quick status toggles (Available, Dispatched, On Route, On Leave)
- **Vehicle Management** — Track bus status (OK, Under Repair, PMS, In Transit, Lutaw/Idle)
- **PMS Tracking** — Preventive Maintenance Schedule monitoring by kilometers or trips
- **Daily Reports** — Summary cards, destination breakdowns, daily tables with totals
- **Excel & PDF Export** — Export dispatch reports per day
- **GPS Tracking Map** — Real-time bus positions on Leaflet/OpenStreetMap
- **SMS Notifications** — Auto-notify drivers via Semaphore when assigned or status changes
- **Role-based Access** — Admin, Operations Manager, Dispatcher
- **Audit Logging** — Tracks all CRUD operations with old/new values
- **Historical Records** — Search and filter past dispatch entries

---

## Prerequisites

### All Platforms (Windows, macOS, Linux)

| Software    | Version  | Download                                                     |
| ----------- | -------- | ------------------------------------------------------------ |
| **PHP**     | >= 8.2   | https://www.php.net/downloads                                |
| **Composer**| >= 2.x   | https://getcomposer.org/download/                            |
| **Node.js** | >= 18.x  | https://nodejs.org/                                          |
| **npm**     | >= 9.x   | (comes with Node.js)                                         |
| **Git**     | >= 2.x   | https://git-scm.com/downloads                                |
| **MySQL**   | >= 8.0   | https://dev.mysql.com/downloads/installer/ (or use XAMPP)    |

### Required PHP Extensions

Make sure these extensions are enabled in your `php.ini`:

- `pdo_mysql` (for MySQL)
- `pdo_sqlite` and `sqlite3` (only if using SQLite)
- `mbstring`
- `openssl`
- `fileinfo`
- `gd` or `imagick`
- `xml`
- `zip`

### Windows-Specific Setup

1. **Install XAMPP (easiest — includes PHP + MySQL)**
   - Download XAMPP from https://www.apachefriends.org/
   - After install, add PHP to your PATH: `C:\xampp\php`
   - Open XAMPP Control Panel and **start MySQL**
   - Verify: `php -v` and `mysql --version`

2. **Or install PHP + MySQL standalone**
   - **PHP:** Download from https://windows.php.net/download/
     - Extract to `C:\php`
     - Copy `php.ini-development` to `php.ini`
     - Uncomment these lines in `php.ini`:
       ```ini
       extension=pdo_mysql
       extension=pdo_sqlite
       extension=sqlite3
       extension=mbstring
       extension=openssl
       extension=fileinfo
       extension=gd
       extension=zip
       ```
     - Add `C:\php` to your System PATH
   - **MySQL:** Download from https://dev.mysql.com/downloads/installer/
     - Use the default settings (root user, port 3306)

3. **Install Composer**
   - Download and run the installer from https://getcomposer.org/Composer-Setup.exe

4. **Install Node.js**
   - Download the LTS installer from https://nodejs.org/
   - The installer automatically adds Node.js and npm to PATH

### macOS Setup

```bash
# Using Homebrew
brew install php mysql composer node
brew services start mysql
```

### Linux (Ubuntu/Debian) Setup

```bash
sudo apt update
sudo apt install php php-mysql php-sqlite3 php-mbstring php-xml php-zip php-gd composer nodejs npm mysql-server
sudo systemctl start mysql
```

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/bitsi-dispatch.git
cd bitsi-dispatch
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node.js dependencies

```bash
npm install
```

### 4. Environment setup

```bash
# Copy the example environment file
cp .env.example .env        # macOS / Linux
copy .env.example .env       # Windows CMD
```

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Set up the database

#### Option A: MySQL (Recommended)

```bash
# 1. Start MySQL (if not already running)
# XAMPP: Open XAMPP Control Panel and start MySQL
# Homebrew (macOS): brew services start mysql
# Linux: sudo systemctl start mysql

# 2. Create the database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS bitsi_dispatch;"

# If your MySQL has a password:
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS bitsi_dispatch;"

# 3. Make sure .env has these settings (already set by default):
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=bitsi_dispatch
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Run migrations and seed
php artisan migrate:fresh --seed
```

#### Option B: SQLite (Zero Config)

If you don't have MySQL, you can use SQLite:

```bash
# 1. Edit .env — comment out MySQL and uncomment SQLite:
# DB_CONNECTION=sqlite

# 2. Create the SQLite database file
# macOS / Linux:
touch database/database.sqlite

# Windows CMD:
type nul > database\database.sqlite

# Windows PowerShell:
New-Item database\database.sqlite -ItemType File

# 3. Run migrations and seed
php artisan migrate:fresh --seed
```

### 7. Build frontend assets

```bash
npm run build
```

### 8. Start the development server

```bash
composer run dev
```

This starts 4 services concurrently:
- **Laravel server** at http://127.0.0.1:8000
- **Vite dev server** (HMR for frontend)
- **Queue worker** (processes SMS jobs)
- **Log watcher** (real-time log output)

Open http://127.0.0.1:8000 in your browser.

---

## Test Accounts

| Role                | Email                   | Password   |
| ------------------- | ----------------------- | ---------- |
| Admin               | admin@bitsi.com         | password   |
| Operations Manager  | opsmanager@bitsi.com    | password   |
| Dispatcher          | dispatcher@bitsi.com    | password   |

> **Note:** Admin users can access all management pages (Users, Vehicles, Drivers, Trip Codes) via the sidebar.

---

## SMS Setup (Optional)

SMS notifications are powered by [Semaphore](https://semaphore.co/), a Philippine SMS API provider.

1. Sign up at https://semaphore.co
2. Get your API key from the dashboard
3. Add to your `.env`:

```env
SEMAPHORE_API_KEY=your_api_key_here
SEMAPHORE_SENDER_NAME=BITSI
```

When configured, the system will automatically send SMS to drivers when:
- They are assigned to a dispatch entry
- A dispatch entry status is updated

---

## Project Structure

```
bitsi-dispatch/
├── app/
│   ├── Enums/              # PHP enums (UserRole, BusType, DriverStatus, etc.)
│   ├── Exports/            # Excel export classes
│   ├── Http/Controllers/
│   │   ├── Admin/          # User, Vehicle, Driver, TripCode CRUD
│   │   ├── Api/            # GPS position API
│   │   ├── Dispatch/       # Dispatch day & entry management
│   │   └── Report/         # Reports & exports
│   ├── Jobs/               # SendSmsJob (queued)
│   ├── Models/             # Eloquent models
│   ├── Observers/          # DispatchEntryObserver (auto SMS + summary)
│   ├── Services/           # SemaphoreService, SummaryService, DispatchService
│   └── Traits/             # Auditable trait
├── database/
│   ├── migrations/         # 12 migration files
│   └── seeders/            # Sample data seeder
├── resources/
│   ├── js/
│   │   ├── components/     # Vue components (sidebar, nav, UI)
│   │   ├── layouts/        # App layout
│   │   ├── pages/          # All Vue pages
│   │   │   ├── admin/      # Admin CRUD pages
│   │   │   ├── dispatch/   # Dispatch board
│   │   │   ├── history/    # Historical records
│   │   │   ├── reports/    # Reports & analytics
│   │   │   └── tracking/   # GPS tracking map
│   │   └── types/          # TypeScript interfaces
│   └── views/
│       └── exports/        # PDF Blade templates
└── routes/
    └── web.php             # All application routes
```

---

## Available Routes

| Section       | URL                    | Description                        |
| ------------- | ---------------------- | ---------------------------------- |
| Landing Page  | `/`                    | Public BITSI landing page          |
| Dashboard     | `/dashboard`           | Overview stats and quick actions   |
| Dispatch      | `/dispatch`            | Daily dispatch board               |
| Tracking      | `/tracking`            | Real-time GPS map                  |
| Reports       | `/reports`             | Trip analytics and exports         |
| History       | `/history`             | Search past dispatch entries       |
| Users         | `/admin/users`         | User management (Admin only)       |
| Trip Codes    | `/admin/trip-codes`    | Trip code management (Admin only)  |
| Vehicles      | `/admin/vehicles`      | Vehicle/bus management (Admin only)|
| Drivers       | `/admin/drivers`       | Driver management (Admin only)     |

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

### "Address already in use" on port 8000

Another process is using port 8000. Kill it:

```bash
# macOS / Linux
lsof -ti:8000 | xargs kill -9

# Windows CMD
netstat -ano | findstr :8000
taskkill /PID <PID_NUMBER> /F

# Windows PowerShell
Stop-Process -Id (Get-NetTCPConnection -LocalPort 8000).OwningProcess -Force
```

### MySQL "Access denied" or "Connection refused"

Make sure MySQL is running:

```bash
# XAMPP: Open Control Panel and start MySQL
# macOS: brew services start mysql
# Linux: sudo systemctl start mysql
```

Check your `.env` credentials match your MySQL setup. Default is `root` with no password.

If you set a password during MySQL installation, update `.env`:

```env
DB_PASSWORD=your_mysql_password
```

### MySQL "Unknown database 'bitsi_dispatch'"

Create the database first:

```bash
mysql -u root -e "CREATE DATABASE bitsi_dispatch;"
# Or with password:
mysql -u root -p -e "CREATE DATABASE bitsi_dispatch;"
```

### Switching from SQLite to MySQL

1. Update `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bitsi_dispatch
   DB_USERNAME=root
   DB_PASSWORD=
   ```
2. Create the MySQL database: `mysql -u root -e "CREATE DATABASE bitsi_dispatch;"`
3. Run `php artisan migrate:fresh --seed`

### Switching from MySQL to SQLite

1. Update `.env`:
   ```env
   DB_CONNECTION=sqlite
   # Comment out or remove DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
   ```
2. Create the file: `touch database/database.sqlite` (or `type nul > database\database.sqlite` on Windows)
3. Run `php artisan migrate:fresh --seed`

### PHP extension missing

Check your extensions with `php -m`. Enable missing ones in `php.ini`:

```ini
extension=pdo_mysql
extension=pdo_sqlite
extension=sqlite3
```

On Windows with XAMPP, the `php.ini` is at `C:\xampp\php\php.ini`.

### Node.js / npm errors on Windows

Make sure Node.js is in your PATH. Open a new terminal after installation. Verify with:

```bash
node -v
npm -v
```

---

## License

This project is proprietary software for Bicol Isarog Transport System, Inc.
