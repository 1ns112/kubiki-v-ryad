FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
        libonig-dev \
    && docker-php-ext-install pdo_pgsql mbstring \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN mkdir -p storage/framework/views storage/framework/sessions \
           storage/framework/cache/data storage/framework/testing bootstrap/cache \
    && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && chown -R www-data:www-data storage bootstrap/cache

ENV PHP_CLI_SERVER_WORKERS=4
ENV PORT=10000

EXPOSE 10000

CMD ["sh", "-c", "export APP_URL=\"${APP_URL:-${RENDER_EXTERNAL_URL:-http://localhost:${PORT}}}\"; if [ -z \"$APP_KEY\" ]; then export APP_KEY=\"base64:$(php -r 'echo base64_encode(random_bytes(32));')\"; fi; php artisan migrate --force && php artisan config:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT}"]
