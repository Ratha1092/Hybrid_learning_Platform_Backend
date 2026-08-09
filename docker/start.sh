#!/bin/sh
set -e

# PROCESS_TYPE selects which process this Railway service runs.
# Set it per-service in the Railway dashboard: web (default), horizon, scheduler.
PROCESS_TYPE="${PROCESS_TYPE:-web}"

# Package discovery needs real env vars (e.g. Pusher key), which only exist
# at runtime, not during the Docker build — so it runs here instead.
php artisan package:discover --ansi

case "$PROCESS_TYPE" in
  web)
    php artisan migrate --force
    php artisan storage:link || true
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    exec frankenphp php-server --root public --listen "0.0.0.0:${PORT:-8080}"
    ;;
  horizon)
    # Plain queue worker instead of `horizon` — at current traffic, Horizon's
    # metrics/heartbeat/dashboard bookkeeping is pure Redis overhead compared
    # to just popping and running jobs. Switch back to `horizon` here (and
    # revert config/horizon.php's minProcesses/queue.php's block_for tuning if
    # desired) once real job volume justifies the dashboard and auto-scaling.
    exec php artisan queue:work redis --queue=high,default,low --sleep=3 --tries=3 --timeout=90
    ;;
  scheduler)
    exec php artisan schedule:work
    ;;
  *)
    echo "Unknown PROCESS_TYPE: $PROCESS_TYPE" >&2
    exit 1
    ;;
esac
