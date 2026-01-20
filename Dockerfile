FROM php:8.1-apache

# ১. প্রয়োজনীয় টুলস এবং এক্সটেনশন ইন্সটল (একবারই ক্যাশ হবে)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ২. Apache কনফিগারেশন এবং পোর্ট সেটআপ
RUN a2enmod rewrite
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf

# ৩. কাস্টম Apache VirtualHost (এক লাইনে সাজানো)
RUN echo '<VirtualHost *:8080>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# ৪. কাজের ফোল্ডার সেট করা
WORKDIR /var/www/html

# ৫. [ম্যাজিক স্টেপ] শুধু কম্পোজার ফাইলগুলো আগে কপি করা
# এর ফলে কোড পাল্টালেও ডিপেন্ডেন্সি নতুন করে ডাউনলোড হবে না
COPY composer.json composer.lock ./

# ৬. কম্পোজার ইন্সটল (শুধু লাইব্রেরিগুলো নামবে)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist --no-dev

# ৭. বাকি সব কোড কপি করা
COPY . .

# ৮. অটোলোডার অপ্টিমাইজ এবং ফাইল পারমিশন
RUN composer dump-autoload --optimize
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache


RUN mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/public/images

# ২. সব ফোল্ডারের মালিকানা www-data (ওয়েব সার্ভার ইউজার) কে দেওয়া
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/public/images

# ৩. ফোল্ডারগুলোতে রাইট পারমিশন (775) দেওয়া
RUN chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/public/images


# ৯. পোর্ট এক্সপোজ
EXPOSE 8080
