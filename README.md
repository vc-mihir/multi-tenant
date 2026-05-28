# Multi-Tenant Laravel Architecture

## 🚀 Overview

This is a robust, custom-built multi-tenant web application developed on Laravel 13. It implements a strict **Database-per-Tenant** architecture with domain-based isolation. The project cleanly separates the central administrative platform from isolated tenant environments, ensuring data security, performance scalability, and clean code separation.

---

## 🧰 Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | Laravel 13, PHP 8.3 |
| **Frontend** | TailwindCSS v3, Alpine.js v3, Vite v8 |
| **Database** | MySQL |
| **Queue** | Laravel Jobs (database driver) |
| **Auth** | Laravel Guards (`web`, `company`, `tenant_user`) |
| **Packages** | Spatie Activity Log v5, Spatie Permission v7, Yajra DataTables v13 |

---

## 🏗 Architecture & Flow

### Central Domain vs Tenant Subdomain

- **Central Domain**: (e.g., `multi-tenant.test`) Handles the registration of new tenants (companies), and central system administration / Super Admin.
- **Tenant Subdomain**: (e.g., `company1.multi-tenant.test`) Each tenant receives their own dedicated subdomain. When a request hits a subdomain, the application dynamically resolves the tenant and switches to their dedicated database connection.

### Multi-Tenant Implementation

The application uses a **Database-per-Tenant** approach:

1. **Central Database**: Stores global users (Central Admins), a master list of all registered `companies` (tenants), and a `company_databases` lookup table. This lookup table safely stores encrypted MySQL credentials and database names for each specific tenant.
2. **Tenant Databases**: When a new company registers, the system dynamically provisions a completely new MySQL database for them. It automatically runs tenant-specific migrations and seeds their initial profile data.

---

## 🔐 Authentication & Guards

To prevent cross-domain session hijacking and ensure strict access control, the project uses multiple authentication guards:

- **`web` Guard (Central)**: Authenticates Central Admins using the `User` model strictly on the central domain. Login requires the `SuperAdmin` role (enforced via **Spatie Laravel Permission**).
- **`company` Guard (Tenant Admin)**: Authenticates the Company Owner using the `Company` model on their specific tenant subdomain.
- **`tenant_user` Guard (Tenant User)**: Authenticates regular users/employees using the `TenantUser` model within a specific tenant's environment.

### Role & Permission System

The central layer uses **Spatie Laravel Permission (v7)** for role-based access control. The `User` model carries the `HasRoles` trait. The seeder assigns the `SuperAdmin` role to the initial admin user — login is rejected for any central user who does not hold this role.

Permission tables (`roles`, `permissions`, `model_has_roles`, etc.) live only in the central database; tenant databases do not use the permission system.

---

## ⚙️ Middleware & Tenancy Handling

- **`IdentifyTenant`**: This is the core middleware for tenancy. It intercepts incoming requests to subdomains, looks up the corresponding tenant in the central database, decrypts their specific database credentials, purges the default connection, and dynamically connects Laravel to the tenant's database.
- **`CentralDomainOnly`**: Ensures that central administrative routes are completely inaccessible from tenant subdomains.

---

## 🔄 Queues, Jobs, and Background Processing

Tenant provisioning is heavy (creating databases, running migrations). To keep the user experience fast, this is handled via background queues.

- **`CreateCompanyDatabase` Job**: Dispatched immediately upon tenant registration. It handles executing `CREATE DATABASE`, running `php artisan migrate --database=tenant --path=database/migrations/tenant`, and seeding the initial company data into the new database without blocking the user's HTTP request.

---

## 📋 Activity Logging

Key operations (login, logout, registration, delete) are tracked using **Spatie Laravel Activity Log (v5)**. Each entry records the causer, the affected model (`subject_type` / `subject_id`), and the event name. Logs are written to the `activity_log` table in whichever database is active — central or tenant.

---

## 🛠 Project Structure Overview

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Central/        ← central admin controllers
│   │   ├── Tenant/         ← tenant controllers
│   │   └── Shared/         ← shared controllers (e.g. CSRF refresh)
│   ├── Middleware/         ← IdentifyTenant, CentralDomainOnly
│   └── Requests/           ← form request validation classes
├── Jobs/                   ← background tenant provisioning
├── Models/
│   ├── Central/            ← central DB models
│   └── Tenant/             ← tenant DB models
├── Notifications/          ← email verification notifications
└── Services/               ← business logic services
routes/
├── central/                ← central domain routes
└── tenant/                 ← tenant subdomain routes
database/
├── migrations/             ← central migrations
└── migrations/tenant/      ← tenant-specific migrations
resources/
├── css/
│   ├── central/            ← central area styles
│   └── tenant/             ← tenant area styles
├── js/
│   ├── central/            ← central area scripts
│   ├── tenant/             ← tenant area scripts
│   ├── shared/             ← shared scripts
│   └── validation/         ← client-side validation scripts
└── views/
    ├── central/            ← central Blade views
    ├── tenant/             ← tenant Blade views
    ├── components/         ← reusable Blade components
    ├── layouts/            ← layout templates
    └── errors/             ← error pages
```

---

## 💻 How to Run the Project (Local Setup)

Follow these instructions meticulously to set up the project locally.

### Prerequisites

- PHP >= 8.3
- Composer
- Node.js & NPM
- MySQL/MariaDB (Your database user MUST have privileges to create new databases)
- A local domain server (Laravel Valet, Laravel Herd, or manual hosts file mapping)

### 1. Clone & Install Dependencies

```bash
git clone <repository-url> multi-tenant
cd multi-tenant
composer install
npm install
```

### 2. Environment Setup

Create your local environment file:

```bash
cp .env.example .env
php artisan key:generate
```

Open the `.env` file and configure the essential variables:

```env
# It is highly recommended to use a local domain like .test instead of localhost for session stability across subdomains
APP_URL=http://multi-tenant.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multi_tenant_master # Create this DB in your MySQL client first
DB_USERNAME=root # Must have CREATE DATABASE privileges
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="admin@multi-tenant.test"

QUEUE_CONNECTION=database # CRITICAL: Must be database (or redis) for tenant creation to work
```

### 3. Database Setup

Create a new database named `multi_tenant_master` in your MySQL server.
Run the central migrations and database seeders to set up the main tables, the `jobs` table, and the super admin record:

```bash
php artisan migrate --seed
```

### 4. Hosts File Configuration

To test subdomains locally without Valet/Herd, you must map them in your OS `hosts` file (`/etc/hosts` on Mac/Linux, `C:\Windows\System32\drivers\etc\hosts` on Windows):

```text
127.0.0.1   multi-tenant.test
127.0.0.1   company1.multi-tenant.test
127.0.0.1   company2.multi-tenant.test
```

### 5. Start the Services

You need to run **three** separate terminal processes simultaneously:

**Terminal 1: Laravel Web Server**

```bash
php artisan serve --host=multi-tenant.test --port=8000
# (Skip this if using Valet or Herd)
```

**Terminal 2: Frontend Asset Compilation (Vite)**

```bash
npm run dev
```

**Terminal 3: Background Queue Worker (CRITICAL)**

```bash
php artisan queue:work
```

_(Without the queue worker, new tenant databases will **never** be created when you register)._

### 6. Using the Application

1. **Register a Tenant**: Go to `http://multi-tenant.test:8000/company-register` (or your configured URL) and create a new company (e.g., "Company 1" with subdomain "company1").
2. **Watch the Queue**: Look at Terminal 3. You should see the `CreateCompanyDatabase` job process successfully.
3. **Login to Tenant**: Navigate to `http://company1.multi-tenant.test:8000` to access the isolated tenant environment and log in with the credentials you just created.

---

## ⚡ Useful Commands

```bash
# Fresh migration with seed (central DB)
php artisan migrate:fresh --seed

# Run queue worker
php artisan queue:work

# Run tests
php artisan test

# Tenant database operations
php artisan tenants:migrate        # run pending migrations on all tenant DBs
php artisan tenants:rollback       # rollback last migration on all tenant DBs
php artisan tenants:migrate:reset  # rollback all migrations on all tenant DBs
```

---

## 📺 Video Demonstration

Click on the thumbnail below to watch the full application flow in action:

<p align="center">
  <a href="https://www.youtube.com/watch?v=TuYLEXKYl0A" target="_blank">
    <img src="https://img.youtube.com/vi/TuYLEXKYl0A/maxresdefault.jpg" alt="Multi-Tenant Application Demo" width="100%" />
  </a>
</p>

---

## 🛑 Common Troubleshooting

- **"Tenant not found" error after registration:**
  Ensure your `queue:work` command is running. The database provisioning happens in the background. Check the `failed_jobs` table in the central database to see if the job crashed.

- **"Access Denied" or "Database does not exist" SQL errors in the queue worker:**
  Your `.env` database user (`DB_USERNAME`) does not have the necessary MySQL permissions to execute `CREATE DATABASE`. Grant the user full privileges.
- **Vite manifest not found:**
  You forgot to run `npm run dev` or `npm run build`.

---

## 🔒 Security Considerations

- **Encrypted Credentials**: Tenant database credentials are encrypted in the central database using Laravel's `Crypt` facade.
- **Strict Guarding**: Middleware explicitly blocks central users from logging into tenant subdomains and vice-versa, ensuring complete data boundary integrity.
- **Database Isolation**: By using a separate database per tenant, the risk of data leaking between tenants due to missing `where('tenant_id', ...)` clauses is completely eliminated.
- **Audit Trail**: All key operations are recorded via Spatie Activity Log in the `activity_log` table of the active database (central or tenant).
