# Staging/production image for Laravel + phpBolt (Render / Docker hosts)
FROM php:8.2-cli-bookworm

WORKDIR /var/www/html

# System deps + PHP extensions (MySQL + Postgres) + Node 20 for Vite 7
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip curl ca-certificates gnupg \
        libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev libxml2-dev libpq-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli pdo_pgsql pgsql zip gd bcmath mbstring exif pcntl \
    && node -v && npm -v \
    && rm -rf /var/lib/apt/lists/*

# Install phpBolt loader (Linux PHP 8.2 NTS)
COPY tools/phpbolt/loaders/linux-php8.2/bolt.so /tmp/bolt.so
RUN EXT_DIR="$(php -r 'echo ini_get("extension_dir");')" \
    && cp /tmp/bolt.so "$EXT_DIR/bolt.so" \
    && printf "extension=bolt.so\n" > /usr/local/etc/php/conf.d/docker-php-ext-bolt.ini \
    && rm /tmp/bolt.so \
    && php -d display_startup_errors=1 -r 'if (!extension_loaded("bolt")) { fwrite(STDERR, "phpBolt failed to load\n"); exit(1);} echo "bolt-loaded\n";'

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

RUN npm ci \
    && npm run build \
    && rm -rf node_modules

# Encrypt app/ + routes/ inside the image only (migrations stay plaintext)
RUN php tools/phpbolt/encrypt.php --apply \
    && rm -rf tools/phpbolt/src-original encrypted staging-build \
    && php -r 'echo extension_loaded("bolt") ? "bolt-ok\n" : "bolt-missing\n";'

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

ENV APP_ENV=staging \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

EXPOSE 8080
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
