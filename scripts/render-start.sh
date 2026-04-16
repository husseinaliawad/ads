#!/usr/bin/env bash
set -euo pipefail

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/framework/testing storage/logs storage/app/public bootstrap/cache
touch storage/logs/laravel.log

# Mirror Laravel file logs to Render runtime logs for easier debugging.
tail -n +1 -F storage/logs/laravel.log &

php artisan storage:link || true
php artisan migrate --force

php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
