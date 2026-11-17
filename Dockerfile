# Staging/production image for Laravel + phpBolt (Render / Docker hosts)
FROM php:8.2-cli-bookworm

WORKDIR /var/www/html

# System deps + PHP extensions Laravel needs (MySQL + Postgres for Render)
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip curl libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev libxml2-dev \
        libpq-dev nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli pdo_pgsql pgsql zip gd bcmath mbstring exif pcntl \
    && rm -rf /var/lib/apt/lists/*

# Install phpBolt loader (Linux PHP 8.2 NTS)
COPY tools/phpbolt/loaders/linux-php8.2/bolt.so /tmp/bolt.so
RUN EXT_DIR="$(php -r 'echo ini_get("extension_dir");')" \
    && cp /tmp/bolt.so "$EXT_DIR/bolt.so" \
    && echo "extension=bolt.so" > /usr/local/etc/php/conf.d/docker-php-ext-bolt.ini \
    && rm /tmp/bolt.so \
    && php -m | grep -i bolt

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# App source (plaintext from git)
COPY . .

# Install PHP deps (no-dev for deploy)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Build frontend assets if package.json exists
RUN if [ -f package.json ]; then npm ci && npm run build; fi \
    && rm -rf node_modules

# Encrypt app/routes/database for the deployed image only
RUN php tools/phpbolt/encrypt.php --apply \
    && rm -rf tools/phpbolt/src-original encrypted staging-build \
    && php -r "echo extension_loaded('bolt') ? 'bolt-ok' : 'bolt-missing';"

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

ENV APP_ENV=staging \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

EXPOSE 8080
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
