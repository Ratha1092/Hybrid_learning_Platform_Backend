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
    exec php artisan horizon
    ;;
  scheduler)
    exec php artisan schedule:work
    ;;
  *)
    echo "Unknown PROCESS_TYPE: $PROCESS_TYPE" >&2
    exit 1
    ;;
esac
