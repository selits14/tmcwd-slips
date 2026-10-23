# TMCWD Slips System

A Laravel web application for preparing, approving, tracking, printing, and reporting TMCWD employee pass slips, vehicle gate passes, and gas slips.

## Contents

- [Features](#features)
- [Roles and access](#roles-and-access)
- [Slip workflows](#slip-workflows)
- [Requirements](#requirements)
- [Local installation](#local-installation)
- [First administrator](#first-administrator)
- [Using the system](#using-the-system)
- [Configuration](#configuration)
- [Render demo deployment](#render-demo-deployment)
- [Database operations and backups](#database-operations-and-backups)
- [Tests](#tests)
- [Troubleshooting](#troubleshooting)
- [Project structure](#project-structure)

## Features

- **Pass slips:** employee, pass type, purpose, destination, departure and expected return; approval and guard time-out/time-in recording.
- **Vehicle gate passes:** requester, driver, vehicle, destination, passengers, purpose, departure and arrival; approval and guard release/return recording.
- **Gas slips:** requester/driver, vehicle, fuel type, station, requested liters, trip information and fuel accounting; approval and driver trip completion.
- **Continuous document numbering:** numbers include the year in their displayed prefix (for example `PS-2026-00001`, `GP-2026-00001`, `GS-2026-0001`). The sequence is stored separately from the displayed year, so it continues when the year changes.
- **PDF printing:** print one slip or selected slips as a batch.
- **Reports:** pass slips, gate passes, fuel consumption, vehicle MROT and RFC reports with printable reports where provided.
- **Administration:** manage staff accounts, divisions, division heads, and vehicles. User and vehicle deletion actions deactivate records instead of removing them.
- **Dashboard:** visible pending counts and recent activity, scoped to the signed-in user's access.
- **In-app notifications:** approvers are alerted to newly submitted slips; requesters and encoders are notified when slips are approved or rejected.
- **Audited workflow fields:** slips record requester/encoder and approval, rejection, release, return or verification details where applicable.

## Roles and access

The application uses Spatie Laravel Permission roles. The role seeder creates `super_admin`, `approver`, `encoder`, `driver`, `guard`, and `viewer`. Some approval and visibility rules also use the user's `position` and assigned division.

| Role / position | Intended access in the current application |
|---|---|
| `super_admin` | Administer users, divisions, and vehicles; create and view slips; approve or reject slips; access reports. |
| `encoder` | Create slips and view all slips; access reports. |
| `approver` | View slips and access reports. Approval itself requires General Manager position, division manager/head authority for that slip's division, or `super_admin`. |
| Division head / Division Manager | Can approve or reject slips in their division. A division head is configured on the division record; the `Division Manager` position also grants approval within their assigned division. |
| General Manager | Can approve or reject slips across divisions when the user's position is exactly `General Manager`. The seeded GM also has the `approver` role for viewing/report access. |
| `driver` | View slips associated with them and complete their approved gas slip trip record. |
| `guard` | View all pass slips and gate passes; record pass slip time-out/time-in and gate pass release/return. Guards do not have gas-slip access. |
| `viewer` | View all slips and access reports; cannot create or approve slips. |

The super admin creates accounts; public registration routes are not enabled. Create accounts with the appropriate role, position, division, and active status. Assign division heads from **Admin → Divisions**. A person's role alone does not make them an approver: approval checks the position or configured division head, except for super admins.

Slip visibility is also filtered in application queries and individual slip pages. Current visibility behavior is: super admins, encoders, viewers, and General Managers can see all slips; drivers see slips associated with themselves; guards see all pass/gate slips; division managers/heads see slips associated with their division. The UI can hide actions, but server-side route/controller checks determine whether an operation is allowed.

## Slip workflows

### Pass slip

1. A super admin or encoder creates the slip for an active employee; the selected employee is the requester.
2. The slip starts as `pending`.
3. A super admin, General Manager, or manager/head authorized for the slip's division approves or rejects it. A rejection can include remarks.
4. After approval, an authorized guard records time-out (`out`) and time-in (`returned`).
5. Authorized staff can view and print the slip and its recorded workflow details.

### Vehicle gate pass

1. A super admin or encoder creates a gate pass and selects an active requester, then enters driver and vehicle/trip details.
2. The pass starts as `pending`.
3. A super admin, General Manager, or manager/head authorized for the slip's division approves or rejects it.
4. A guard releases an approved pass (`released`) and records its return (`returned`). The application records the corresponding departure and arrival times.
5. Authorized staff can view and print the pass.

### Gas slip

1. A super admin or encoder creates a gas slip, selects an active vehicle and driver, and enters the request and fuel details. The selected driver is recorded as requester; the signed-in account is recorded as encoder.
2. The slip starts as `pending`.
3. A super admin, General Manager, or manager/head authorized for the slip's division approves or rejects it.
4. The assigned driver records trip times, distance, fuel used and other trip details. On completion the slip becomes `released`; fuel used cannot exceed the recorded starting balance, issued fuel, and fuel purchased during the trip combined.
5. Authorized staff can view, print, and include released trips in fuel reports.

State transitions only succeed from the expected prior state. If another user changes a slip first, refresh the page and review its current status before trying again.

## Requirements

- PHP 8.2 or later and required Laravel extensions (including PDO for the chosen database; `pdo_pgsql` for PostgreSQL)
- Composer 2
- Node.js and npm
- SQLite for local use, or PostgreSQL/MySQL/MariaDB for a shared installation

The included Render Docker image uses PHP 8.3, Apache, PostgreSQL, and Node 22 for asset builds.

## Local installation

From the project directory in PowerShell:

```powershell
composer install
Copy-Item .env.example .env
New-Item -ItemType File -Path database/database.sqlite -Force
```

Set the database connection in `.env`. For SQLite, set `DB_CONNECTION=sqlite` and `DB_DATABASE=database/database.sqlite`. For MySQL/MariaDB or PostgreSQL, create an empty database and configure the matching `DB_CONNECTION`, host, port, database name, username, and password. Never commit `.env` or production credentials.

Then initialize Laravel, the database, and front-end assets:

```powershell
php artisan key:generate
php artisan migrate --seed
npm ci
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000/login`. The database seeder creates the standard divisions, roles, and inactive placeholder administrator/GM accounts; it does not provide a default login or password. Follow [First administrator](#first-administrator) to create an active administrator.

For local front-end development, run `npm run dev` in a second terminal while `php artisan serve` is running.

## First administrator

There is no public registration. For a local installation, create the first administrator in Tinker:

```powershell
php artisan tinker
```

At the Tinker prompt, use your own account details and a unique strong password:

```php
$admin = \App\Models\User::updateOrCreate(
    ['email' => 'your-admin@example.com'],
    [
        'name' => 'Your Name',
        'password' => \Illuminate\Support\Facades\Hash::make('Replace-With-A-Strong-Password'),
        'position' => 'IT Officer',
        'is_active' => true,
    ]
);
$admin->syncRoles(['super_admin']);
```

Exit Tinker with `exit`. The user must have a valid email, active status, and the `super_admin` role. Change the example values before using the command.

## Using the system

1. Sign in at `/login` with an active account.
2. A super admin should first check divisions and division heads, create staff accounts with their correct roles/positions, and add active vehicles.
3. An encoder creates a pass slip, gate pass, or gas slip and selects the person requesting it. For gas slips, select the driver who is requesting/using the fuel.
4. The authorized manager or approver reviews pending slips and approves or rejects them. Rejections may include remarks.
5. Guards complete pass and gate movements; the assigned driver completes gas trip details.
6. Use each module's list filters to find records, open a record to review it, then print one record or select records for batch printing.
7. Use **Reports** for slip registers, monthly fuel consumption, and vehicle MROT/RFC reports.

## Configuration

Set environment values in `.env` locally or in the hosting provider's environment settings. Do not place secrets in source control.

| Variable | Purpose |
|---|---|
| `APP_NAME` | Application/browser title, e.g. `TMCWD Slips System`. |
| `APP_ENV`, `APP_DEBUG`, `APP_URL` | Environment mode, debug output, and canonical application URL. Production should use `production`, `false`, and the HTTPS URL. |
| `APP_KEY` | Laravel encryption key. Generate once with `php artisan key:generate`; keep it private and stable for the deployment. |
| `DB_CONNECTION` and database variables | Select and configure SQLite, PostgreSQL, MySQL, or MariaDB. Render's Blueprint uses PostgreSQL via `DB_URL`. |
| `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` | Laravel session, cache, and queue backends. The Render Blueprint configures database-backed sessions/cache/queue. |
| `MAIL_*` | Password reset and other email delivery. The example environment uses the log mailer; configure a real mail provider for delivery. |
| `LLM7_API_KEY`, `LLM7_BASE_URL`, `LLM7_MODEL` | Optional LLM7 connectivity command configuration. Defaults for base URL and model are documented in `config/services.php`. |
| `BOOTSTRAP_DEMO_ADMIN` | Render container startup option. When `true`, runs the demo admin seeder; normally leave `false`. |
| `DEMO_ADMIN_NAME`, `DEMO_ADMIN_EMAIL`, `DEMO_ADMIN_PASSWORD` | Required only when demo admin bootstrap is enabled; password must be at least 12 characters. Remove these values and disable bootstrap after initial account creation. |

After changing Laravel configuration in an existing deployment, clear/rebuild cached configuration as appropriate for that host. Render deploys the application from the connected Git branch; its free web service can sleep when idle and its free database is temporary, so the included Render setup is for demonstrations, not official record keeping.

## Render demo deployment

The repository's `render.yaml` defines a Docker web service and PostgreSQL database. The Docker entrypoint applies pending migrations before Apache starts. It does not create sample slips or run the regular database seeder on each start.

1. Push the repository to GitHub and create a Render Blueprint from it.
2. Apply the Blueprint. Provide a private `APP_KEY` when Render requests it; generate a key with `php artisan key:generate --show` in a secure local environment and do not publish it.
3. Set the service's `APP_URL` to its HTTPS URL. The Blueprint configures `APP_NAME` and database connection details.
4. To bootstrap the initial demo super admin, set `BOOTSTRAP_DEMO_ADMIN=true`, `DEMO_ADMIN_NAME`, `DEMO_ADMIN_EMAIL`, and a strong `DEMO_ADMIN_PASSWORD` (at least 12 characters), then deploy.
5. Sign in at `<your Render URL>/login`. Once the account works, set `BOOTSTRAP_DEMO_ADMIN=false`, remove the three `DEMO_ADMIN_*` variables, and deploy again. Do not reuse a real staff password.
6. Create sample records through the UI. Use demonstration data only.

The free Render PostgreSQL database expires and the free web service may sleep. Export any demo data you need before database expiration. Do not use this configuration for official slips without durable hosting, backups, and an operational recovery plan.

## Database operations and backups

Back up the database regularly and store copies away from the application host. Test restoring a backup periodically. This application currently stores its operational data in the database; also back up any uploaded files if that feature is added/configured later.

### Migrations

For local development:

```powershell
php artisan migrate
```

In production, take a backup first and run:

```powershell
php artisan migrate --force
```

The Render image runs this command automatically on container startup. Review new migrations before deploying them.

### SQLite backup and restore

Stop the app before copying the database, or use the `sqlite3` backup command while it is running:

```powershell
New-Item -ItemType Directory -Force backups
sqlite3 database/database.sqlite ".backup 'backups/tmcwd-slips.sqlite'"
```

To restore, stop the app and keep a copy of the current database before replacing it:

```powershell
Copy-Item backups/tmcwd-slips.sqlite database/database.sqlite -Force
```

### PostgreSQL backup and restore

Use the connection values provided by your host. `pg_dump` makes a logical backup; `pg_restore` restores a custom-format dump into the intended database.

```powershell
pg_dump --format=custom --file=backups/tmcwd-slips.dump --dbname="$env:DATABASE_URL"
pg_restore --clean --if-exists --dbname="$env:DATABASE_URL" backups/tmcwd-slips.dump
```

Keep the database URL secret. Restore only into the intended database, and retain an independent backup of its current contents first.

### MySQL or MariaDB backup and restore

The `-p` option prompts for a password rather than putting it directly in the command:

```powershell
mysqldump --single-transaction -u tmcwd_user -p tmcwd_slips > backups/tmcwd-slips.sql
mysql -u tmcwd_user -p tmcwd_slips < backups/tmcwd-slips.sql
```

## Tests

Run the automated test suite with:

```powershell
php artisan test
```

Tests are configured to use an in-memory SQLite database and should not change the configured application database. Current coverage includes authentication, profile updates, slip workflow/authorization, slip numbering, and the optional LLM7 connectivity command.

Run the optional API connectivity check only when `LLM7_API_KEY` is configured:

```powershell
php artisan llm7:check
```

## Troubleshooting

| Symptom | Check |
|---|---|
| Login rejects credentials | Confirm the account is active, email is correct, and password is set. Seeded placeholder accounts are inactive and have random passwords. |
| User cannot create a slip | Only `super_admin` and `encoder` are allowed to create slips. Check the account's assigned role. |
| User cannot approve/reject | Check that the user is `super_admin`, has position `General Manager`, is the configured division head, or has position `Division Manager` and the matching division assignment. The generic `approver` role alone does not grant approval. |
| A record appears missing or returns 404 | Slip list/detail/print queries are visibility-scoped. Check whether the user's role, driver identity, division, and head assignment allow access. |
| A workflow action reports the record changed | Another user may have completed a transition. Reload the slip and use its current status. |
| Database connection fails | Check `DB_CONNECTION`, host/port, database name, credentials, network access, and that the appropriate PHP PDO extension is installed. Render uses PostgreSQL. |
| Render deploy fails during migration | Open the deploy logs and inspect the first migration error, not only the final stack trace. Check that database variables are available and the migration is compatible with the selected database. |
| PDF is missing data or layout is wrong | Confirm the slip contains the required fields and inspect the matching print view under `resources/views`. PDFs are generated by the DomPDF package. |
| Password reset email is not received | Configure a real mail transport using `MAIL_*`; the example setup logs mail instead of sending it. |

## Project structure

```text
app/Http/Controllers/     Request handling for slips, reports, admin, and auth
app/Http/Requests/        Form validation for slip creation and profile changes
app/Models/               Users, divisions, vehicles, and slip records
app/Support/              Slip numbering, visibility, and state-transition helpers
database/migrations/      Schema and data migrations
database/seeders/         Roles, divisions, local bootstrap accounts, demo admin
resources/views/          Blade pages, layouts, reports, and print templates
routes/                   Web, authentication, and console routes
tests/                    Feature and unit tests
docker/                   Apache configuration and container entrypoint
Dockerfile                Production image definition
render.yaml               Render Blueprint configuration
```

## License and operational note

This repository is an application codebase; confirm the organization's intended license and data-retention requirements before distributing or using it for official records. Public registration is disabled. Keep production credentials private and restrict production database access.
