#!/usr/bin/env bash
set -e

# Kill all child processes when this script exits (Ctrl+C)
trap 'echo ""; echo "Stopping..."; kill $(jobs -p) 2>/dev/null; wait' EXIT INT TERM

echo "Starting dev environment..."

php artisan serve       2>&1 | sed 's/^/[serve]    /' &
php artisan horizon     2>&1 | sed 's/^/[horizon]  /' &
php artisan schedule:work 2>&1 | sed 's/^/[schedule] /' &

wait
