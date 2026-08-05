#!/bin/sh
set -e

# PROCESS_TYPE selects which process this Railway service runs.
# Set it per-service in the Railway dashboard: web (default), horizon, scheduler.
PROCESS_TYPE="${PROCESS_TYPE:-web}"

case "$PROCESS_TYPE" in
  web)
    php artisan migrate --force
    php artisan storage:link || true
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
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
