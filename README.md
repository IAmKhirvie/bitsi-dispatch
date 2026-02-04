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
| Database         | SQLite (zero config)                           |
| Queue            | SQLite (database driver)                       |
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

### Required PHP Extensions

Make sure these extensions are enabled in your `php.ini`:

- `pdo_sqlite`
- `sqlite3`
- `mbstring`
- `openssl`
- `fileinfo`
- `gd` or `imagick`
- `xml`
- `zip`

### Windows-Specific Setup

1. **Install PHP via XAMPP (easiest)**
   - Download XAMPP from https://www.apachefriends.org/
   - After install, add PHP to your PATH: `C:\xampp\php`
   - Verify: `php -v`

2. **Or install PHP standalone**
   - Download from https://windows.php.net/download/
   - Extract to `C:\php`
   - Copy `php.ini-development` to `php.ini`
   - Uncomment these lines in `php.ini`:
     ```ini
     extension=pdo_sqlite
     extension=sqlite3
     extension=mbstring
     extension=openssl
     extension=fileinfo
     extension=gd
     extension=zip
     ```
   - Add `C:\php` to your System PATH

3. **Install Composer**
   - Download and run the installer from https://getcomposer.org/Composer-Setup.exe

4. **Install Node.js**
   - Download the LTS installer from https://nodejs.org/
   - The installer automatically adds Node.js and npm to PATH

### macOS Setup

```bash
# Using Homebrew
brew install php composer node
```

### Linux (Ubuntu/Debian) Setup

```bash
sudo apt update
sudo apt install php php-sqlite3 php-mbstring php-xml php-zip php-gd composer nodejs npm
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

### 6. Create database and seed sample data

```bash
# Create the SQLite database file
# macOS / Linux:
touch database/database.sqlite

# Windows CMD:
type nul > database\database.sqlite

# Windows PowerShell:
New-Item database\database.sqlite -ItemType File

# Run migrations and seed
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

### SQLite "database does not exist"

Create the file manually:

```bash
# macOS / Linux
touch database/database.sqlite

# Windows
type nul > database\database.sqlite
```

Then run `php artisan migrate:fresh --seed`.

### PHP extension missing

Check your extensions with `php -m`. If `pdo_sqlite` or `sqlite3` is missing, enable them in `php.ini`:

```ini
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
