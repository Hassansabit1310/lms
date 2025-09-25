# Stage 1: Frontend build with Node
FROM node:20 AS frontend

WORKDIR /app

# Install frontend deps
COPY package.json package-lock.json ./
RUN npm install

# Copy all app files (for vite build)
COPY . .

# Build assets for production (creates public/build)
RUN npm run build


# Stage 2: Laravel backend with PHP
FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libsodium-dev \
    libpq-dev \
    default-mysql-client \
    default-libmysqlclient-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip sodium

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy Laravel app files
COPY . .

# Copy built assets from Node stage
COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies (optimized for prod)
RUN composer install --no-dev --optimize-autoloader

# Expose Railway’s dynamic port
EXPOSE $PORT

# Start Laravel app (with migrations)
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
