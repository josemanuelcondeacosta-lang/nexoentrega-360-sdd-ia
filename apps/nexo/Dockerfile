FROM php:8.5.0-cli-bookworm
RUN apt-get update && apt-get install -y --no-install-recommends libpq-dev libonig-dev unzip git && docker-php-ext-install pdo_pgsql mbstring && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2.8.12 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-interaction --no-scripts
COPY . .
RUN composer dump-autoload --optimize && chmod +x start.sh
EXPOSE 8000
CMD ["sh", "start.sh"]
