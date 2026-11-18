#!/bin/sh
set -e

# Make Laravel's writable folders writable by Apache (www-data).
# Runs only once the app is actually present in the bind mount.
if [ -f /var/www/html/artisan ]; then
    mkdir -p \
        /var/www/html/storage/logs \
        /var/www/html/storage/framework/cache/data \
        /var/www/html/storage/framework/sessions \
        /var/www/html/storage/framework/views \
        /var/www/html/bootstrap/cache
    chown -R www-data:www-data \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache
fi

exec "$@"