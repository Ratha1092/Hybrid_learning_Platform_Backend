#!/bin/sh
set -e


PROCESS_TYPE="${PROCESS_TYPE:-web}"
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
    php artisan schedule:work &
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