# Use PHP 8.3 specifically for Railway deployment
FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-scripts --no-interaction

# Copy application files
COPY . .

# Set permissions
RUN chmod -R 755 storage bootstrap/cache

# Cache Laravel configuration
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Expose port (Railway will set $PORT)
EXPOSE $PORT

# Start Laravel development server
CMD php artisan serve --host=0.0.0.0 --port=$PORT
