FROM php:8.4-cli

WORKDIR /app

# Install system dependencies (FIXED: includes sqlite)
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Generate Laravel caches (safe if env not set yet)
RUN php artisan config:clear || true
RUN php artisan cache:clear || true

# Expose port (Railway uses dynamic PORT)
EXPOSE 8080

# Start Laravel server (IMPORTANT: use Railway PORT)
CMD php -S 0.0.0.0:${PORT:-8080} -t public