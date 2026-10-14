#!/bin/sh
set -eu
test -f .env || cp .env.example .env
if [ -z "${APP_KEY:-}" ]; then php artisan key:generate --force; fi
php artisan migrate --force
php artisan db:seed --force
exec php artisan serve --host=0.0.0.0 --port=8000 --no-reload
