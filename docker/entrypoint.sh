#!/bin/sh
set -e

# Config and route caches read env, so build them when the container starts, not in the image.
if [ "$APP_ENV" = "production" ] || [ "$APP_ENV" = "staging" ]; then
    php artisan optimize
fi

# This script runs as root; PHP-FPM workers run as www-data and must be able to write here.
chown -R www-data:www-data storage bootstrap/cache

exec "$@"
