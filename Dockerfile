FROM php:8.4-cli

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip \
    && docker-php-ext-install pdo pdo_sqlite zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Laravel setup
RUN cp .env.example .env || true
RUN php artisan key:generate || true
RUN mkdir -p database && touch database/database.sqlite
RUN php artisan migrate --force || true
RUN php artisan storage:link || true

# Start app
CMD php artisan serve --host=0.0.0.0 --port=$PORT