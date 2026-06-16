FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libpq-dev libicu-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql intl zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

EXPOSE 8000

CMD ["sh", "-c", "rm -f /app/.composer-ready && [ -f /app/.env ] || printf 'APP_ENV=dev\\nAPP_SECRET=change_me_in_production\\nDEFAULT_URI=http://localhost:8000\\nCORS_ALLOW_ORIGIN=^https?://(localhost|127\\\\.0\\\\.0\\\\.1)(:[0-9]+)?$\\n' > /app/.env && composer install --no-interaction --prefer-dist && touch /app/.composer-ready && php -S 0.0.0.0:8000 -t public public/index.php"]
