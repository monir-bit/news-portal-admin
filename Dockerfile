FROM php:8.1-apache

# ===============================
# System + PHP Extensions (GD SAFE)
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
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ===============================
# Apache Config (Cloud Run)
# ===============================
RUN a2enmod rewrite
RUN echo "Listen 8080" > /etc/apache2/ports.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN echo '<VirtualHost *:8080>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# ===============================
# Workdir
# ===============================
WORKDIR /var/www/html

# ===============================
# Composer (cache optimized)
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
# Laravel Required Directories
# ===============================
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    storage/app/public \
    bootstrap/cache

# ===============================
# Permissions (CRITICAL)
# ===============================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# ===============================
# Storage Symlink
# ===============================
RUN php artisan storage:link || true

# ===============================
# Cloud Run
# ===============================
EXPOSE 8080
CMD ["apache2-foreground"]
