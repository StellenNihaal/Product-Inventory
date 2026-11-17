#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

# Render/Railway inject PORT; bind artisan serve to it
export PORT="${PORT:-8080}"

# Railway-style MySQL plugin vars
if [ -n "${MYSQLHOST:-}" ]; then
  export DB_CONNECTION="${DB_CONNECTION:-mysql}"
  export DB_HOST="${DB_HOST:-$MYSQLHOST}"
  export DB_PORT="${DB_PORT:-${MYSQLPORT:-3306}}"
  export DB_DATABASE="${DB_DATABASE:-${MYSQLDATABASE:-railway}}"
  export DB_USERNAME="${DB_USERNAME:-${MYSQLUSER:-root}}"
  export DB_PASSWORD="${DB_PASSWORD:-${MYSQLPASSWORD:-}}"
fi

# Render Postgres often provides DATABASE_URL / DB_URL
if [ -n "${DATABASE_URL:-}" ] || [ -n "${DB_URL:-}" ]; then
  export DB_CONNECTION="${DB_CONNECTION:-pgsql}"
  export DATABASE_URL="${DATABASE_URL:-$DB_URL}"
fi

if [ -z "${APP_KEY:-}" ]; then
  echo "ERROR: APP_KEY is not set. Generate one with: php artisan key:generate --show"
  exit 1
fi

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true

php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force
fi

php artisan storage:link || true
php artisan config:cache || true

if [ "$#" -gt 0 ]; then
  if [ "$1" = "php" ] && [ "${2:-}" = "artisan" ] && [ "${3:-}" = "serve" ]; then
    exec php artisan serve --host=0.0.0.0 --port="$PORT"
  fi
  exec "$@"
fi

exec php artisan serve --host=0.0.0.0 --port="$PORT"
