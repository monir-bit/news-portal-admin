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
# Apache module
# ===============================
RUN a2enmod rewrite

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
    bootstrap/cache

# ===============================
# Permissions
# ===============================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# ===============================
# Storage link
# ===============================
RUN php artisan storage:link || true

# ===============================
# Cloud Run
# ===============================
ENV PORT=8080
EXPOSE 8080

CMD bash -c '\
echo "Listen ${PORT}" > /etc/apache2/ports.conf && \
echo "<VirtualHost *:${PORT}>" > /etc/apache2/sites-available/000-default.conf && \
echo "  DocumentRoot /var/www/html/public" >> /etc/apache2/sites-available/000-default.conf && \
echo "  <Directory /var/www/html/public>" >> /etc/apache2/sites-available/000-default.conf && \
echo "    AllowOverride All" >> /etc/apache2/sites-available/000-default.conf && \
echo "    Require all granted" >> /etc/apache2/sites-available/000-default.conf && \
echo "  </Directory>" >> /etc/apache2/sites-available/000-default.conf && \
echo "</VirtualHost>" >> /etc/apache2/sites-available/000-default.conf && \
apache2-foreground'
