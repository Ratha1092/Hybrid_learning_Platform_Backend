# Hybrid Learning Platform — Backend

The backend for **Hybrid Learning**, an online course marketplace: instructors create and sell video courses, students purchase and track progress through them, payments are settled via Cambodia's **KHQR / Bakong** QR payment network, and the platform automatically splits revenue and pays instructors out. Built with **Laravel 12** (PHP 8.4) and administered through a **Filament 5** admin panel; the student/instructor-facing client is a separate frontend that consumes this backend's REST API.

## Tech stack

- **Framework:** Laravel 12, PHP 8.4
- **Admin panel:** Filament 5 (`/admin`), with Spatie Media Library / Settings / Tags plugins
- **Auth:** Laravel Sanctum (API tokens), Laravel Socialite (Google/GitHub OAuth)
- **Access control:** spatie/laravel-permission (role & permission based)
- **Payments:** khqr-gateway/bakong-khqr-php (KHQR/Bakong QR generation & verification), endroid/qr-code
- **Queues:** Redis + Laravel Horizon
- **Email:** Resend
- **Real-time:** Pusher (broadcasting)
- **File storage:** Cloudflare R2 (S3-compatible, via Flysystem) — public and private buckets
- **PDF generation:** barryvdh/laravel-dompdf
- **Database:** PostgreSQL
- **Production runtime:** FrankenPHP (see `Dockerfile`)

## Requirements

- PHP 8.4 with extensions: `pdo_pgsql`, `pgsql`, `pcntl`, `bcmath`, `gd`, `exif`, `intl`, `zip`, `opcache`
- Composer 2.x
- Node.js 20+ and npm
- PostgreSQL
- Redis (queues, cache, Horizon)

## Getting started

```bash
git clone <repo-url>
cd Hybrid_learning_Platform_Backend

composer install

# .env.example is not currently checked into this repo — create .env
# yourself (copy from another environment or from the variable reference
# below) before continuing.
cp .env.example .env   # once an .env.example exists
php artisan key:generate

# create the database, then:
php artisan migrate --seed

npm install
npm run build

php artisan storage:link
```

Or run the bundled setup script (does the same, non-interactively):

```bash
composer run setup
```

## Running locally

```bash
composer run dev
```

This runs, concurrently: the app server (`php artisan serve`), a queue worker (`php artisan queue:listen`), live log tailing (`php artisan pail`), and the Vite dev server for admin-panel assets — all in one terminal, color-coded.

For production-realistic queue processing, run Horizon instead of `queue:listen`:

```bash
php artisan horizon
```

### Scheduled tasks

Several background tasks run on Laravel's scheduler (`routes/console.php`) — payment expiry (every minute), scheduled reports (hourly), wallet balance release (daily), monthly payout generation, and data retention pruning. In production, point cron at:

```bash
* * * * * php artisan schedule:run >> /dev/null 2>&1
```

## Environment variables

Grouped reference of the variables this app reads (see the relevant `config/*.php` file for each group). No values are listed here — get real values from your team's secret store; never commit `.env`.

| Group | Variables |
|---|---|
| App | `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`, `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_MAINTENANCE_DRIVER`, `FRONTEND_URL` |
| Database | `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` |
| Cache / Queue / Session | `CACHE_STORE`, `QUEUE_CONNECTION`, `SESSION_DRIVER`, `SESSION_LIFETIME`, `SESSION_DOMAIN`, `SESSION_ENCRYPT`, `REDIS_CLIENT`, `REDIS_HOST`, `REDIS_PORT`, `REDIS_PASSWORD` |
| Auth | `SANCTUM_STATEFUL_DOMAINS`, `BCRYPT_ROUNDS` |
| OAuth | `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`, `GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET`, `GITHUB_REDIRECT_URI` |
| Payments (Bakong/KHQR) | `BAKONG_MERCHANT_NAME`, `BAKONG_MERCHANT_CITY`, `BAKONG_MERCHANT_ACCOUNT_ID`, `BAKONG_MERCHANT_CATEGORY_CODE`, `BAKONG_COUNTRY_CODE`, `BAKONG_CURRENCY`, `BAKONG_QR_TTL_MINUTES`, `BAKONG_VERIFY_URL`, `BAKONG_API_TOKEN`, `BAKONG_TIMEOUT` |
| File storage | `FILESYSTEM_DISK`, `R2_ACCESS_KEY_ID`, `R2_SECRET_ACCESS_KEY`, `R2_BUCKET`, `R2_ENDPOINT`, `R2_URL`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_USE_PATH_STYLE_ENDPOINT` |
| Mail | `MAIL_MAILER`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`, `RESEND_API_KEY` |
| Real-time | `BROADCAST_CONNECTION`, `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, `PUSHER_APP_CLUSTER`, `PUSHER_HOST`, `PUSHER_PORT`, `PUSHER_SCHEME`, `VITE_PUSHER_APP_KEY`, `VITE_PUSHER_APP_CLUSTER` |
| Logging | `LOG_CHANNEL`, `LOG_STACK`, `LOG_LEVEL`, `LOG_DEPRECATIONS_CHANNEL` |
| Misc | `APP_VERSION`, `VITE_APP_NAME` |

> No `.env.example` currently exists in this repository. Consider adding one (with placeholder values, no secrets) so new environments can be set up without guessing variable names.

## Project structure

The app is organized as domain modules rather than Laravel's default flat structure:

```
app/Domains/<Domain>/
    Models/
    Controllers/
    Services/
    routes.php
    ...
```

Domains include: `Users`, `Auth`, `Courses`, `Learning`, `Orders`, `Payments`, `Promotions`, `Billing`, `Finance`, `Analytics`, `Reports`, `Notifications`, `System`. Each domain's `routes.php` is auto-loaded under `/v1` by `routes/api.php`. The Filament admin panel lives in `app/Filament/{Resources,Pages}` and its supporting session routes are in `routes/web.php`.

## Custom artisan commands

| Command | Purpose |
|---|---|
| `payments:verify-pending` | Poll/expire pending KHQR payments (scheduled every minute) |
| `reports:run-scheduled` | Run and email any due scheduled business reports (hourly) |
| `wallet:release-pending-balance` | Mature instructors' pending wallet balances into withdrawable balance (daily) |
| `payouts:generate-monthly` | Auto-generate monthly payout requests (last day of month) |
| `app:prune-retained-data` | Data-retention cleanup (daily) |
| `app:backfill-billing-documents` | One-off backfill of invoices/receipts for historical orders |

## Testing

```bash
composer run test
```

Feature and unit tests live in `tests/Feature` (Auth, Orders, Payments, Filament) and `tests/Unit`. End-to-end tests for the admin panel live in `tests/playwright`.

## Deployment

The `Dockerfile` builds a production image running **FrankenPHP** (Caddy-based) rather than `php artisan serve`, which is single-threaded and unsuitable for production traffic. Queue processing in production should run via Horizon, and the scheduler via cron as shown above.

## Admin panel

Once running, the staff admin panel is available at `/admin`. Only accounts with the `super-admin` or `finance` role can sign in there; run the relevant seeder (`RolePermissionSeeder`) to create the default roles, then assign a user to `super-admin` to get initial access.
