# BITSI Dispatch - Application Scope & Documentation

> Last Updated: 2026-02-10
> Repository: https://github.com/IAmKhirvie/bitsi-dispatch
> Version: 1.0

---

## Application Overview

**BITSI Dispatch** is a real-time bus dispatch management system for **Bicol Isarog Transport System, Inc. (BITSI)**. It digitizes the daily bus status report into a modern web application with dispatch board, reports, driver management, and SMS notifications.

### Purpose
- Replace manual Excel-based daily tracking
- Provide real-time dispatch management
- Track driver attendance and performance
- Send automated SMS notifications to drivers
- Generate reports and export data (Excel/PDF)

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12 (PHP 8.2+) |
| Frontend | Vue 3 (Composition API) + TypeScript |
| SPA Router | Inertia.js v2 |
| CSS | Tailwind CSS + shadcn-vue (Radix-based) |
| Database | MySQL 8+ (recommended) or SQLite (zero config) |
| Queue | Database driver (works with both MySQL & SQLite) |
| Icons | Lucide Vue |
| Maps | Leaflet.js + OpenStreetMap (NOTE: GPS tracking REMOVED) |
| SMS | Semaphore API (Philippine provider) |
| Excel Export | maatwebsite/excel |
| PDF Export | barryvdh/laravel-dompdf |
| Build Tool | Vite |

---

## All Modules & CRUD Operations

### 1. USERS MODULE (`/admin/users`)
**Access:** Admin only

| Operation | Method | Route | Description |
|-----------|--------|-------|-------------|
| Index | GET | `/admin/users` | List users with search & role filter |
| Create | GET | `/admin/users/create` | Show create form |
| Store | POST | `/admin/users` | Create new user |
| Show | GET | `/admin/users/{user}` | View user details |
| Edit | GET | `/admin/users/{user}/edit` | Show edit form |
| Update | PUT/PATCH | `/admin/users/{user}` | Update user |
| Destroy | DELETE | `/admin/users/{user}` | Delete user |
| Toggle Active | PATCH | `/admin/users/{user}/toggle-active` | Enable/disable account |

**Fields:** name, email, password, role (admin/operations_manager/dispatcher), phone, is_active

---

### 2. VEHICLES MODULE (`/admin/vehicles`)
**Access:** Admin only

| Operation | Method | Route | Description |
|-----------|--------|-------|-------------|
| Index | GET | `/admin/vehicles` | List vehicles with filters |
| Create | GET | `/admin/vehicles/create` | Show create form |
| Store | POST | `/admin/vehicles` | Create vehicle |
| Show | GET | `/admin/vehicles/{vehicle}` | View vehicle details |
| Edit | GET | `/admin/vehicles/{vehicle}/edit` | Show edit form |
| Update | PUT/PATCH | `/admin/vehicles/{vehicle}` | Update vehicle |
| Destroy | DELETE | `/admin/vehicles/{vehicle}` | Delete vehicle |

**Fields:** bus_number, brand, bus_type (regular/deluxe/super_deluxe/elite/sleeper/single_seater/skybus), plate_number, status (OK/UR/PMS/In Transit/Lutaw), pms_unit (kilometers/trips), pms_threshold, current_pms_value, last_pms_date

**PMS Feature:** Preventive Maintenance Schedule tracking with warning indicators

---

### 3. DRIVERS MODULE (`/admin/drivers`)
**Access:** Admin only

| Operation | Method | Route | Description |
|-----------|--------|-------|-------------|
| Index | GET | `/admin/drivers` | List drivers with search & status filter |
| Create | GET | `/admin/drivers/create` | Show create form |
| Store | POST | `/admin/drivers` | Create driver |
| Show | GET | `/admin/drivers/{driver}` | View driver details |
| Edit | GET | `/admin/drivers/{driver}/edit` | Show edit form |
| Update | PUT/PATCH | `/admin/drivers/{driver}` | Update driver |
| Destroy | DELETE | `/admin/drivers/{driver}` | Delete driver |
| Toggle Active | PATCH | `/admin/drivers/{driver}/toggle-active` | Enable/disable driver |
| Update Status | PATCH | `/admin/drivers/{driver}/update-status` | Quick status change |
| Send Schedule SMS | POST | `/admin/drivers/{driver}/send-schedule-sms` | **NEW** Send today's schedule via SMS |
| Send Custom SMS | POST | `/admin/drivers/{driver}/send-custom-sms` | **NEW** Send custom message |
| Schedule Preview | GET | `/admin/drivers/{driver}/schedule-preview` | **NEW** Preview schedule SMS |

**Fields:** name, phone, license_number, is_active, status (available/dispatched/on_route/on_leave)

**NEW SMS Features:**
- "Schedule" button - Sends driver's trips for today with times, routes, bus numbers
- "Send" button - Opens dialog with schedule preview + custom message option
- SMS includes trip details: scheduled departure, route, bus number, status
- Messages sent via queued job (high priority)

---

### 4. TRIP CODES MODULE (`/admin/trip-codes`)
**Access:** Admin only

| Operation | Method | Route | Description |
|-----------|--------|-------|-------------|
| Index | GET | `/admin/trip-codes` | List trip codes with filters |
| Create | GET | `/admin/trip-codes/create` | Show create form |
| Store | POST | `/admin/trip-codes` | Create trip code |
| Show | GET | `/admin/trip-codes/{trip_code}` | View trip code |
| Edit | GET | `/admin/trip-codes/{trip_code}/edit` | Show edit form |
| Update | PUT/PATCH | `/admin/trip-codes/{trip_code}` | Update trip code |
| Destroy | DELETE | `/admin/trip-codes/{trip_code}` | Delete trip code |
| Toggle Active | PATCH | `/admin/trip-codes/{tripCode}/toggle-active` | Enable/disable |

**Fields:** code, operator, origin_terminal, destination_terminal, bus_type, scheduled_departure_time, direction (SB/NB), is_active

---

### 5. DISPATCH MODULE (`/dispatch`)
**Access:** All authenticated users

| Operation | Method | Route | Description |
|-----------|--------|-------|-------------|
| Index | GET | `/dispatch?date={date}` | View dispatch board |
| Store Day | POST | `/dispatch` | Create dispatch day |
| Store Entry | POST | `/dispatch/{dispatchDay}/entries` | Add dispatch entry |
| Update Entry | PUT | `/dispatch/{dispatchDay}/entries/{entry}` | Update dispatch entry |
| Destroy Entry | DELETE | `/dispatch/{dispatchDay}/entries/{entry}` | Delete entry |
| Update Status | PATCH | `/dispatch/{dispatchDay}/entries/{entry}/status` | Update status |
| Autofill API | GET | `/api/trip-codes/{tripCode}/autofill` | Auto-fill from trip code |

**Dispatch Day Fields:** service_date, notes, created_by

**Dispatch Entry Fields:** vehicle_id, trip_code_id, driver_id, driver2_id, brand, bus_number, route, bus_type, departure_terminal, arrival_terminal, scheduled_departure, actual_departure, direction, status (scheduled/departed/on_route/delayed/cancelled/arrived), remarks

**Status Workflow:** scheduled → departed → on_route → arrived (can also go to delayed/cancelled)

---

### 6. HISTORY MODULE (`/history`)
**Access:** All authenticated users (Read-only)

| Operation | Method | Route | Description |
|-----------|--------|-------|-------------|
| Index | GET | `/history` | Search historical dispatch entries |

**Filters:** date_from, date_to, bus_number, trip_code, direction, status, route

---

### 7. REPORTS MODULE (`/reports`)
**Access:** All authenticated users (Read-only)

| Operation | Method | Route | Description |
|-----------|--------|-------|-------------|
| Index | GET | `/reports` | Summary reports with date range |
| Daily | GET | `/reports/daily/{date}` | Detailed daily report |
| Export Excel | GET | `/reports/export/excel/{date}` | Download as Excel |
| Export PDF | GET | `/reports/export/pdf/{date}` | Download as PDF |

**Report Metrics:** total_trips, sb_trips, nb_trips, destination breakdowns

---

### 8. ATTENDANCE MODULE (`/admin/attendance`) **NEW**
**Access:** Admin only

| Operation | Method | Route | Description |
|-----------|--------|-------|-------------|
| Index | GET | `/admin/attendance` | Attendance records with statistics |
| Settings | GET | `/admin/attendance/settings` | Configure thresholds |
| Update Settings | PUT | `/admin/attendance/settings` | Save settings |
| Mark Late | POST | `/admin/attendance/mark-late` | Mark driver as late |
| Mark Absent | POST | `/admin/attendance/mark-absent` | Mark driver as absent |
| Override | POST | `/admin/attendance/override` | Override attendance record |
| Initialize Today | POST | `/admin/attendance/initialize-today` | Create attendance records for today |
| Alerts API | GET | `/api/attendance/alerts` | Get unread alerts |
| Pending API | GET | `/api/attendance/pending` | Get pending check-ins |

**Attendance Fields:** driver_id, dispatch_entry_id, attendance_date, check_in_time, check_out_time, status (pending/on_time/late/absent/excused), minutes_late, notes, marked_by

**Alert Types:** upcoming, late, absent

**Configurable Settings:**
- Late Threshold (default: 15 min)
- Pre-departure Alert (default: 15 min)
- Auto-absent Timeout (default: 30 min)
- Require Check-in (toggle)

**Scheduled Task:** Runs every 5 minutes to check attendance issues and create alerts

---

### 9. API ROUTES (Mobile App - Future Expansion) **NEW**
**Base Path:** `/api/v1`

| Endpoint | Method | Description |
|---------|--------|-------------|
| `/driver/check-in` | POST | Driver checks in (params: dispatch_entry_id, time, phone) |
| `/driver/check-out` | POST | Driver checks out (params: dispatch_entry_id, time, phone) |
| `/driver/my-schedule` | GET | Get driver's upcoming trips |
| `/driver/my-attendance` | GET | Get driver's attendance history |

**Note:** Currently uses phone number for identification. In production, should use API tokens/JWT.

---

### 10. USER PROFILE & SETTINGS (`/settings`)
**Access:** All authenticated users (Own account only)

| Operation | Method | Route | Description |
|-----------|--------|-------|-------------|
| Profile Edit | GET | `/settings/profile` | View profile settings |
| Profile Update | PATCH | `/settings/profile` | Update profile |
| Delete Profile | DELETE | `/settings/profile` | Delete account |
| Password Edit | GET | `/settings/password` | View password settings |
| Password Update | PUT | `/settings/password` | Change password |
| Appearance | GET | `/settings/appearance` | Theme settings |

---

### 11. AUTHENTICATION

| Operation | Method | Route | Description |
|-----------|--------|-------|-------------|
| Login | GET/POST | `/login` | Login form & submit |
| Logout | POST | `/logout` | Logout |
| Register | GET/POST | `/register` | Registration |
| Forgot Password | GET/POST | `/forgot-password` | Request password reset |
| Reset Password | GET/POST | `/reset-password/{token}` | Reset password |
| Email Verification | GET | `/verify-email` | Verification prompt |
| Verify | GET | `/verify-email/{id}/{hash}` | Verify email link |
| Resend Verification | POST | `/email/verification-notification` | Resend verification |
| Confirm Password | GET/POST | `/confirm-password` | Confirm password |

---

## Role Permissions Summary

| Module | Admin | Operations Manager | Dispatcher |
|--------|-------|-------------------|------------|
| Users | Full CRUD | - | - |
| Vehicles | Full CRUD | View only | View only |
| Drivers | Full CRUD + SMS | View only | View only |
| Trip Codes | Full CRUD | View only | View only |
| Dispatch | Full | Full | Full |
| History | View | View | View |
| Reports | View | View | View |
| Attendance | Full CRUD | View only | View only |
| Profile | Own | Own | Own |

---

## Known Limitations & Constraints

### Technical Limitations

1. **GPS Tracking Removed**
   - GPS/Real-time tracking module has been completely removed
   - No map visualization of vehicle positions
   - No `gps_device_id` field in vehicles table

2. **SMS Service Dependency**
   - SMS functionality requires valid Semaphore API credentials
   - SMS sending is queued via database queue - requires `php artisan queue:listen` running
   - No guarantee of SMS delivery (depends on Semaphore service)
   - SMS sending can fail silently - check `sms_logs` table for delivery status

3. **Database-Specific Behavior**
   - SQLite has limited date/time handling compared to MySQL
   - Some complex queries may behave differently between SQLite and MySQL

4. **Queue System**
   - Background jobs (SMS) require queue worker to be running
   - If queue worker is down, SMS jobs will pile up in `jobs` table
   - No automatic retry mechanism for failed SMS (manual re-queue required)

5. **Mobile App API Not Secured**
   - API endpoints for mobile app use phone number for identification
   - No authentication tokens/JWT implemented yet
   - Anyone with phone number can theoretically check in/out

6. **Scheduler Limitations**
   - Attendance alerts run every 5 minutes via Laravel scheduler
   - Requires cron job or `php artisan schedule:work` to be running
   - No alerts if server is not running

7. **No Real-time Updates**
   - No WebSockets or real-time data push
   - Frontend polls server for updates (standard Inertia.js behavior)

8. **File Uploads**
   - No file upload functionality beyond avatar images
   - Excel/PDF exports generated on-demand (not stored)

---

## Configuration

### Environment Variables (.env)

```bash
# Application
APP_NAME="BITSI Dispatch"
APP_ENV=local
APP_KEY=generated_by_php
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (SQLite by default for zero-config)
DB_CONNECTION=sqlite
# DB_CONNECTION=mysql (for MySQL)
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=bitsi_dispatch
# DB_USERNAME=root
# DB_PASSWORD=

# SMS (Semaphore)
SEMAPHORE_API_KEY=your_api_key_here
SEMAPHORE_SENDER_NAME=BITSI
```

### SMS Configuration
To enable SMS notifications, add to `.env`:
```env
SEMAPHORE_API_KEY=your_api_key_here
SEMAPHORE_SENDER_NAME=BITSI
```

---

## File Structure

```
bitsi-dispatch/
├── app/
│   ├── Console/Commands/
│   │   └── CheckAttendanceAlerts.php          # Attendance checker scheduler
│   ├── Enums/                                     # PHP enums
│   ├── Exports/                                   # Excel exports
│   ├── Http/Controllers/
│   │   ├── Admin/                             # Admin CRUD controllers
│   │   ├── Api/                               # API endpoints (mobile app)
│   │   └── Auth/                              # Authentication
│   ├── Jobs/                                     # Queued jobs (SendSmsJob)
│   ├── Models/                                    # Eloquent models
│   ├── Observers/                                # Model observers (auto SMS)
│   ├── Services/                                 # Business logic (SemaphoreService)
│   └── Traits/                                   # Reusable traits
├── database/
│   ├── migrations/                              # Database migrations
│   └── database.sqlite                          # SQLite database (if using SQLite)
├── resources/
│   ├── js/
│   │   ├── components/                           # Vue components
│   │   ├── pages/                               # Vue pages
│   │   └── types/                               # TypeScript definitions
│   └── views/                                  # Blade templates (PDF exports)
├── routes/
│   ├── api.php                                 # API routes for mobile app
│   ├── console.php                              # Console routes & scheduler
│   ├── web.php                                 # Web routes
│   └── auth.php                                # Auth routes
└── tests/                                      # PHPUnit tests
```

---

## Running the Application

### Start Development Server
```bash
# Using composer (recommended - runs 4 services concurrently)
composer run dev

# Or individually:
php artisan serve           # Laravel server at http://127.0.0.1:8000
npm run dev               # Vite dev server (HMR)
php artisan queue:listen    # Queue worker (for SMS)
php artisan pail            # Log watcher
```

### Build for Production
```bash
npm run build
php artisan optimize:clear
```

### Run Scheduled Tasks
```bash
# Run attendance checker manually
php artisan attendance:check-alerts

# Run schedule worker (for background tasks)
php artisan schedule:work
```

---

## Test Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@bitsi.com | password |
| Operations Manager | opsmanager@bitsi.com | password |
| Dispatcher | dispatcher@bitsi.com | password |

---

## Common Commands

```bash
# Run migrations
php artisan migrate:fresh --seed

# Clear cache
php artisan optimize:clear

# Generate app key
php artisan key:generate

# Clear config cache
php artisan config:clear
```

---

## Notes for Future Expansion

1. **Mobile App Integration**
   - API endpoints are ready but need authentication
   - Consider implementing Laravel Sanctum or Passport for API tokens

2. **Real-time Features**
   - Consider Laravel Echo + WebSockets/Pusher for real-time updates
   - Broadcasting for live attendance status changes

3. **Advanced Reporting**
   - Add charts and analytics dashboard
   - Export to more formats (CSV, etc.)

4. **Performance**
   - Add database indexing for large datasets
   - Consider caching for frequently accessed data

5. **SMS Improvements**
   - Add delivery status tracking
   - Implement retry mechanism for failed SMS
   - Add bulk SMS send capability

---

## License

Proprietary software for Bicol Isarog Transport System, Inc.

---

*This documentation is generated for development reference. Always check the codebase for the most up-to-date information.*
