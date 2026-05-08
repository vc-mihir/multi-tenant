# Multi-Tenant Laravel Architecture

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

## 🚀 Overview

This is a robust, custom-built multi-tenant web application developed on Laravel 12. It implements a strict **Database-per-Tenant** architecture with domain-based isolation. The project cleanly separates the central administrative platform from isolated tenant environments, ensuring data security, performance scalability, and clean code separation.

---

## 🏗 Architecture & Flow

### Central Domain vs Tenant Subdomain

- **Central Domain**: (e.g., `myapp.test`) Handles the public landing page, registration of new tenants (companies), and central system administration.
- **Tenant Subdomain**: (e.g., `company1.myapp.test`) Each tenant receives their own dedicated subdomain. When a request hits a subdomain, the application dynamically resolves the tenant and switches to their dedicated database connection.

### Multi-Tenant Implementation

The application uses a **Database-per-Tenant** approach:

1. **Central Database**: Stores global users (Central Admins), a master list of all registered `companies` (tenants), and a `company_databases` lookup table. This lookup table safely stores encrypted MySQL credentials and database names for each specific tenant.
2. **Tenant Databases**: When a new company registers, the system dynamically provisions a completely new MySQL database for them. It automatically runs tenant-specific migrations and seeds their initial profile data.

---

## 🔐 Authentication & Guards

To prevent cross-domain session hijacking and ensure strict access control, the project uses multiple authentication guards:

- **`web` Guard (Central)**: Authenticates Central Admins using the `User` model strictly on the central domain.
- **`company` Guard (Tenant Admin)**: Authenticates the Company Owner using the `Company` model on their specific tenant subdomain.
- **`tenant_user` Guard (Tenant User)**: Authenticates regular users/employees using the `TenantUser` model within a specific tenant's environment.

---

## ⚙️ Middleware & Tenancy Handling

- **`IdentifyTenant`**: This is the core middleware for tenancy. It intercepts incoming requests to subdomains, looks up the corresponding tenant in the central database, decrypts their specific database credentials, purges the default connection, and dynamically connects Laravel to the tenant's database.
- **`CentralDomainOnly`**: Ensures that central administrative routes are completely inaccessible from tenant subdomains.

---

## 🔄 Queues, Jobs, and Background Processing

Tenant provisioning is heavy (creating databases, running migrations). To keep the user experience fast, this is handled via background queues.

- **`CreateCompanyDatabase` Job**: Dispatched immediately upon tenant registration. It handles executing `CREATE DATABASE`, running `php artisan migrate --database=tenant --path=database/migrations/tenant`, and seeding the initial company data into the new database without blocking the user's HTTP request.

---

## 🛠 Project Structure Overview

The codebase is strictly organized to separate Central logic from Tenant logic:

- `app/Models/Central` & `app/Models/Tenant`
- `app/Http/Controllers/Central` & `app/Http/Controllers/Tenant`
- `routes/central/` & `routes/tenant/`
- `database/migrations/` (Central) & `database/migrations/tenant/` (Tenant-specific)

---

## 💻 How to Run the Project (Local Setup)

Follow these instructions meticulously to set up the project locally.

### Prerequisites

- PHP >= 8.2
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
APP_URL=http://myapp.test
SESSION_DOMAIN=.myapp.test # Prefix with a dot to allow sessions across subdomains, or leave null for strict isolation

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multi_tenant_central # Create this DB in your MySQL client first
DB_USERNAME=root # Must have CREATE DATABASE privileges
DB_PASSWORD=

QUEUE_CONNECTION=database # CRITICAL: Must be database (or redis) for tenant creation to work
```

### 3. Database Setup

Create a new database named `multi_tenant_central` in your MySQL server.
Run the central migrations and database seeders to set up the main tables, the `jobs` table, and the super admin record:

```bash
php artisan migrate --seed
```

### 4. Hosts File Configuration

To test subdomains locally without Valet/Herd, you must map them in your OS `hosts` file (`/etc/hosts` on Mac/Linux, `C:\Windows\System32\drivers\etc\hosts` on Windows):

```text
127.0.0.1   myapp.test
127.0.0.1   company1.myapp.test
127.0.0.1   company2.myapp.test
```

### 5. Start the Services

You need to run **three** separate terminal processes simultaneously:

**Terminal 1: Laravel Web Server**

```bash
php artisan serve --host=myapp.test --port=8000
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

1. **Register a Tenant**: Go to `http://myapp.test:8000/register` (or your configured URL) and create a new company (e.g., "Company 1" with subdomain "company1").
2. **Watch the Queue**: Look at Terminal 3. You should see the `CreateCompanyDatabase` job process successfully.
3. **Login to Tenant**: Navigate to `http://company1.myapp.test:8000` to access the isolated tenant environment and log in with the credentials you just created.

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
