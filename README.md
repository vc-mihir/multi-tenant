# Multi-Tenant Laravel Architecture

## 🚀 Overview

This is a robust, custom-built multi-tenant web application developed on Laravel 13. It implements a strict **Database-per-Tenant** architecture with domain-based isolation. The project cleanly separates the central administrative platform from isolated tenant environments, ensuring data security, performance scalability, and clean code separation.

---

## 🧰 Tech Stack

| Layer        | Technology                                                         |
| ------------ | ------------------------------------------------------------------ |
| **Backend**  | Laravel 13+, PHP 8.4+                                              |
| **Frontend** | TailwindCSS v3, Alpine.js v3, Vite v8                              |
| **Database** | MySQL                                                              |
| **Queue**    | Laravel Jobs (database driver)                                     |
| **Auth**     | Laravel Guards (`web`, `company`, `tenant_user`)                   |
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

Key operations (login, logout, registration, soft-delete, restore, and permanent deletion) are tracked using **Spatie Laravel Activity Log (v5)**. Each entry records the causer, the affected model (`subject_type` / `subject_id`), and the event name. Logs are written to the `activity_log` table in whichever database is active — central or tenant.

---

## 🧪 Testing

The suite runs on **Pest PHP** and mirrors the app's central/tenant split across two databases:

- **Central** — a real MySQL test database (`multi_tenant_testing`), managed by the `RefreshDatabase` trait, for central models (companies, super admins).
- **Tenant** — a disposable SQLite `:memory:` database set up per test via the `setUpTenantDb()` helper, standing in for the per-company MySQL database provisioned at runtime.

Shared helpers (`setUpTenantDb()`, `seedCompany()`, `makeTenantUser()`, `tenantUrl()`, etc.) live in `tests/Pest.php`. Test files are organized by feature area under `tests/Feature/` and `tests/Unit/`.

```bash
composer test                            # run the full suite
php artisan test --filter "ClassName"    # run a specific test class
php artisan test --filter "test name"    # run a single test
```

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

Setup is split into a **common** part (identical on every OS) and an **OS-specific** part. The OS-specific part matters because serving wildcard `*.multi-tenant.test` subdomains and the automatic `/etc/hosts` mapping behave very differently on **Ubuntu** vs **Windows**.

### Prerequisites

- **PHP >= 8.4** with the usual Laravel extensions (`mbstring`, `xml`, `curl`, `pdo_mysql`, `bcmath`, `intl`, `zip`)
- **Composer**
- **Node.js & npm**
- **MySQL / MariaDB** — the DB user MUST be able to create databases (`CREATE DATABASE`)
- A way to resolve and serve `multi-tenant.test` and its subdomains — see **Step 4** (differs per OS)

### 1. Clone & Install Dependencies (all OSes)

```bash
git clone <repository-url> multi-tenant
cd multi-tenant
composer install
npm install
```

### 2. Environment Setup (all OSes)

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and configure the essential variables:

```env
# APP_ENV must be "local" — the automatic /etc/hosts subdomain mapping only runs in local.
APP_ENV=local

# Use a real .test domain (not localhost) for stable sessions across subdomains.
APP_URL=http://multi-tenant.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multi_tenant_master   # create this DB in your MySQL client first
DB_USERNAME=root                  # must have CREATE DATABASE privileges
DB_PASSWORD=

# CRITICAL: must be database (or redis) — tenant provisioning runs on the queue.
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="admin@multi-tenant.test"
```

### 3. Database Setup (all OSes)

Create the central database, then run the central migrations + seeders (core tables, the `jobs` table, and the SuperAdmin user):

```bash
# In your MySQL client:  CREATE DATABASE multi_tenant_master;
php artisan migrate --seed
```

### 4. Web Server & Domain Resolution (OS-specific)

#### 🐧 Ubuntu — our setup (nginx + PHP-FPM)

1. **nginx vhost** — point the central host **and** a wildcard for tenant subdomains at `public/`:

    ```nginx
    server {
        listen 80;
        server_name multi-tenant.test *.multi-tenant.test;
        root /var/www/Projects/multi-tenant/public;

        index index.php;
        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }
        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/run/php/php8.4-fpm.sock;   # match your PHP-FPM socket
        }
    }
    ```

    Then: `sudo nginx -t && sudo systemctl reload nginx`.

2. **Map only the CENTRAL domain** in `/etc/hosts` — tenant subdomains are added automatically (see Step 6):

    ```text
    127.0.0.1   multi-tenant.test
    ```

3. **PHP-FPM is sandboxed.** Our `php-fpm` service runs hardened with `ProtectSystem=full`, which mounts `/etc` read-only inside the web process. This is deliberate: the **web request cannot edit `/etc/hosts`**, so the subdomain mapping is delegated to the **queue worker** (Step 6) instead.

4. **Grant the worker user a passwordless sudo rule** so it can append tenant entries to `/etc/hosts`. The worker runs as the login user (`mihirkothari`). Use a dedicated drop-in — never edit `/etc/sudoers` directly:

    ```bash
    sudo visudo -f /etc/sudoers.d/tenant-hosts
    ```

    Add the append rule (this is the only rule required for auto-mapping on activation):

    ```text
    mihirkothari ALL=(root) NOPASSWD: /usr/bin/tee -a /etc/hosts
    ```

    > **Optional — auto-removal on permanent deletion.** When a company is permanently deleted, the app also tries to _remove_ its subdomain, which rewrites the whole file and therefore needs a second rule:
    >
    > ```text
    > mihirkothari ALL=(root) NOPASSWD: /usr/bin/tee /etc/hosts
    > ```
    >
    > Without it, auto-removal fails silently (it's logged). You can still clean up with `php artisan tenant:remove-host <subdomain>` or by editing `/etc/hosts` by hand.

#### 🪟 Windows

- **No automatic `/etc/hosts` mapping.** The auto-mapping shells out to `sudo` + `/usr/bin/tee`, which don't exist on Windows — so every domain must be added by hand. Open Notepad **as Administrator** and edit `C:\Windows\System32\drivers\etc\hosts`:

    ```text
    127.0.0.1   multi-tenant.test
    127.0.0.1   acme.multi-tenant.test      # add one line per tenant you create
    ```

- **Serving:** the simplest path for wildcard `.test` subdomains is **Laragon** or **Laravel Herd** — both provide automatic `*.test` resolution and virtual hosts; point the site root at `public/`. (`php artisan serve` works for the central domain but won't transparently host wildcard subdomains.)

### 5. Build Front-End Assets (all OSes)

```bash
npm run dev      # development (hot reload)
# or
npm run build    # production build
```

### 6. Start the Queue Worker (all OSes — keep it running)

```bash
php artisan queue:work
```

This worker is **critical**: it runs the `CreateCompanyDatabase` job that creates and seeds each tenant database. On **Ubuntu** it _also_ maps the new subdomain into `/etc/hosts` (via the sudo rule from Step 4). On our setup it is run **manually in a terminal** as `mihirkothari`. Without it, tenant databases (and local subdomain mappings) are never created.

### 7. Using the Application

1. **Register a tenant** at `http://multi-tenant.test/company-register` (e.g. "Acme" with subdomain `acme`).
2. **Verify the email** — open the verification link from your Mailtrap inbox. This activates the account and queues provisioning.
3. **Provisioning loader** — after verifying you land on a loader that polls every ~1.5s and forwards you to the tenant automatically once provisioning is `ready` (the tenant DB row exists **and**, locally, the subdomain is mapped in `/etc/hosts`).
    - Watch the **queue worker** terminal — you should see `CreateCompanyDatabase` run (and, on Ubuntu, the host-mapping job).
    - If it isn't ready within ~10s, the loader falls back to the registration page with a "finishing setup" notice — the account is already created, so this is not an error.
    - **Local note:** because "ready" also requires the subdomain mapping, the auto-forward only happens on Ubuntu when the Step 4 sudo rule is in place. On Windows (or without the rule) expect the fallback, then visit the subdomain manually after adding the hosts entry.
4. **Log in to the tenant** at `http://acme.multi-tenant.test`.

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

# Tenant subdomain /etc/hosts mapping (local, Linux/macOS; needs the sudo rule from setup Step 4)
php artisan tenant:add-host <subdomain>     # map <subdomain>.multi-tenant.test -> 127.0.0.1
php artisan tenant:remove-host <subdomain>  # remove that mapping
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

- **Provisioning loader keeps falling back to the registration page (subdomain never resolves):**
  The loader only forwards once the subdomain is mapped in `/etc/hosts`. On **Ubuntu**, confirm the queue worker is running as the user named in the sudoers drop-in and that the append rule from setup Step 4 exists. On **Windows**, this is expected — add the host entry manually, then open `http://<subdomain>.multi-tenant.test` yourself.

- **Subdomain not added to `/etc/hosts` / `sudo: a password is required` in the worker:**
  The passwordless sudoers rule is missing or names a different user than the one running `php artisan queue:work`. Re-check `/etc/sudoers.d/tenant-hosts` (setup Step 4) and that the rule's username matches the worker's user.

---

## 🔒 Security Considerations

- **Encrypted Credentials**: Tenant database credentials are encrypted in the central database using Laravel's `Crypt` facade.
- **Strict Guarding**: Middleware explicitly blocks central users from logging into tenant subdomains and vice-versa, ensuring complete data boundary integrity.
- **Database Isolation**: By using a separate database per tenant, the risk of data leaking between tenants due to missing `where('tenant_id', ...)` clauses is completely eliminated.
- **Audit Trail**: All key operations are recorded via Spatie Activity Log in the `activity_log` table of the active database (central or tenant).
