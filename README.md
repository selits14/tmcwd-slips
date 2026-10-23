# TMCWD Slips System

Laravel application for managing TMCWD pass slips, vehicle gate passes, gas slips, approvals, and reports.

## Requirements

- PHP 8.2 or later with the extensions required by Laravel and the selected database driver
- Composer
- Node.js and npm
- SQLite for a local setup, or MySQL/MariaDB for a shared installation

## Local setup (PowerShell)

From the project directory:

```powershell
composer install
Copy-Item .env.example .env
New-Item -ItemType File -Path database/database.sqlite -Force
php artisan key:generate
```

For SQLite, keep `DB_CONNECTION=sqlite` in `.env` and set `DB_DATABASE=database/database.sqlite`. For MySQL/MariaDB, create an empty database and set `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` instead. Never commit `.env`.

Then migrate, seed the roles/divisions and inactive placeholder accounts, and build the front end:

```powershell
php artisan migrate --seed
npm ci
npm run build
```

The seeded `admin@tmcwd.com` and `gm@tmcwd.com` accounts are deliberately inactive and receive random passwords. There is no default login. Create or activate the first super admin from Tinker:

```powershell
php artisan tinker
```

At the Tinker prompt, replace the example name, email, and password with the actual account details:

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

Exit Tinker with `exit`, then start the local server and visit `http://127.0.0.1:8000/login`:

```powershell
php artisan serve
```

Public registration is disabled. The super admin creates staff accounts from **Admin → Users**.

## Tests

Run the automated suite with:

```powershell
php artisan test
```

Tests use an in-memory SQLite database; they do not modify your configured application database.

## Temporary Render demo

The repository includes a Docker-based Render Blueprint (`render.yaml`) for a temporary demo. It creates a Laravel web service and PostgreSQL database. The free PostgreSQL database expires after 30 days, and the free web service sleeps when idle; use sample data only, not official records.

1. Generate an app key locally with `php artisan key:generate --show`; keep the output private.
2. Push this project to GitHub and connect the `tmcwd-slips` repository in Render.
3. In Render, create a Blueprint from the repository and apply `render.yaml`. When prompted for `APP_KEY`, paste the generated key.
4. After Render creates the web service, copy its HTTPS URL into the service's `APP_URL` environment variable.
5. To create the initial demo super admin, set `BOOTSTRAP_DEMO_ADMIN=true`, `DEMO_ADMIN_NAME`, `DEMO_ADMIN_EMAIL`, and a strong `DEMO_ADMIN_PASSWORD` (at least 12 characters) in the service environment, then save and wait for the deploy to finish.
6. Sign in at `<your Render URL>/login`. After confirming the account works, set `BOOTSTRAP_DEMO_ADMIN=false`, remove the three `DEMO_ADMIN_*` variables, and save. Do not reuse a real staff password.

The container runs outstanding migrations at startup. It does not run the regular database seeder or create sample slips. Create any demo records through the application after signing in. Render's free database is temporary, so export anything you need before it expires.

## Back up and restore

Back up the database regularly and keep a copy away from the application computer. Test restoration periodically. If the system stores uploaded files, back up those files as well as the database.

### SQLite

Stop the application before copying the database file, or use the `sqlite3` CLI backup command while it is running:

```powershell
New-Item -ItemType Directory -Force backups
sqlite3 database/database.sqlite ".backup 'backups/tmcwd-slips.sqlite'"
```

To restore, stop the application, keep a separate copy of the current database, then copy the backup over the database path configured by `DB_DATABASE`:

```powershell
Copy-Item backups/tmcwd-slips.sqlite database/database.sqlite -Force
```

### MySQL or MariaDB

The `-p` option prompts for the database password instead of putting it in the command:

```powershell
mysqldump --single-transaction -u tmcwd_user -p tmcwd_slips > backups/tmcwd-slips.sql
mysql -u tmcwd_user -p tmcwd_slips < backups/tmcwd-slips.sql
```

Restore only into the intended database. Keep an independent backup of its current contents first.

## Production checklist

- Set `APP_ENV=production`, `APP_DEBUG=false`, and the real HTTPS `APP_URL` in the server environment.
- Use a dedicated database account with only the permissions the app needs.
- Serve the app over HTTPS and keep secrets in the deployment environment, never in Git.
- Configure scheduled, off-site backups and verify restoration before relying on the system for official records.
- Run `php artisan migrate --force` during deployment after taking a backup.

## LLM7 connectivity check

Set `LLM7_API_KEY` in `.env` or the server environment. `LLM7_BASE_URL` defaults to `https://api.llm7.io/v1` and `LLM7_MODEL` defaults to `default`. Run `php artisan llm7:check` to verify the server-side connection.
