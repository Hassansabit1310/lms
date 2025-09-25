# Use PHP 8.3 specifically for Railway deployment
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

RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get update && apt-get install -y nodejs

# Set working directory
WORKDIR /var/www/html 

COPY . .

# Copy built frontend assets from Node stage
COPY --from=frontend /app/public/build ./public/build

EXPOSE $PORT

RUN composer install
RUN npm install



# Start Laravel development server
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
