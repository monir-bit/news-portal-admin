FROM php:8.1-apache

# ===============================
# System + PHP Extensions
# ===============================
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    curl \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ===============================
# Apache Configuration (CRITICAL FIX)
# ===============================
RUN a2enmod rewrite

# 1. Change Apache port from 80 to 8080 (Required for Cloud Run)
# 2. Change DocumentRoot to /var/www/html/public (Required for Laravel)
RUN sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf \
    && sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# ===============================
# Workdir
# ===============================
WORKDIR /var/www/html

# ===============================
# Composer
# ===============================
COPY composer.json composer.lock ./
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# ===============================
# App Source
# ===============================
COPY . .

# ===============================
# Laravel directories
# ===============================
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    storage/app/public \
    public/images \
    bootstrap/cache

# ===============================
# Permissions (Includes previous fix)
# ===============================
# We fix permissions for storage AND the public/images folder
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 storage bootstrap/cache public/images

# ===============================
# Storage link
# ===============================
RUN php artisan storage:link || true

# ===============================
# Start
# ===============================
EXPOSE 8080
