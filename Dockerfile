FROM php:8.4-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Fix Laravel permissions (prevents runtime crashes)
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

# IMPORTANT: bind directly to Railway PORT
CMD ["sh", "-c", "php -S 0.0.0.0:10000 -t public"]