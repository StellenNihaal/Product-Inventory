#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

export PORT="${PORT:-8080}"

# Render terminates TLS; always prefer its https public URL
if [ -n "${RENDER_EXTERNAL_URL:-}" ]; then
  export APP_URL="$RENDER_EXTERNAL_URL"
elif [ -n "${APP_URL:-}" ]; then
  export APP_URL="$(php -r 'echo preg_replace("#^http://#","https://", getenv("APP_URL") ?: "");')"
fi
export ASSET_URL="${ASSET_URL:-$APP_URL}"

# Railway-style MySQL plugin vars
if [ -n "${MYSQLHOST:-}" ]; then
  export DB_CONNECTION="${DB_CONNECTION:-mysql}"
  export DB_HOST="${DB_HOST:-$MYSQLHOST}"
  export DB_PORT="${DB_PORT:-${MYSQLPORT:-3306}}"
  export DB_DATABASE="${DB_DATABASE:-${MYSQLDATABASE:-railway}}"
  export DB_USERNAME="${DB_USERNAME:-${MYSQLUSER:-root}}"
  export DB_PASSWORD="${DB_PASSWORD:-${MYSQLPASSWORD:-}}"
fi

# Render Postgres: Laravel reads DB_URL
if [ -n "${DATABASE_URL:-}" ] && [ -z "${DB_URL:-}" ]; then
  export DB_URL="$DATABASE_URL"
fi
if [ -n "${DB_URL:-}" ]; then
  export DB_CONNECTION="${DB_CONNECTION:-pgsql}"
fi

if [ -z "${APP_KEY:-}" ]; then
  echo "WARNING: APP_KEY missing — generating a temporary key for this boot"
  export APP_KEY="$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")"
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
