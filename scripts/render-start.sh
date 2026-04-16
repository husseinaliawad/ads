#!/usr/bin/env bash
set -euo pipefail

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/framework/testing storage/logs storage/app/public bootstrap/cache

php artisan storage:link || true
php artisan migrate --force

php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
