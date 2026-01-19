FROM php:8.1-apache

# ১. প্রয়োজনীয় টুলস ইন্সটল
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    curl

# ২. ক্যাশ ক্লিয়ার
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# ৩. লারাভেলের প্রয়োজনীয় এক্সটেনশন
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# ৪. Apache Rewrite Module চালু করা
RUN a2enmod rewrite

# ৫. পোর্ট ৮০৮০ সেট করা (Cloud Run এর জন্য বাধ্যতামূলক)
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf

# ৬. কাস্টম Apache কনফিগারেশন সেট করা (এটাই আপনার সমস্যার সমাধান)
# আমরা ডিফল্ট কনফিগ ডিলিট করে নতুন করে বানাচ্ছি যাতে .htaccess কাজ করে
RUN echo '<VirtualHost *:8080>' > /etc/apache2/sites-available/000-default.conf && \
    echo '    DocumentRoot /var/www/html/public' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    <Directory /var/www/html/public>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        Options Indexes FollowSymLinks' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# ৭. সব ফাইল কপি করা
COPY . .

# ৮. কম্পোজার ইন্সটল
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# ৯. স্টোরেজ পারমিশন ফিক্স করা
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ১০. পোর্ট এক্সপোজ
EXPOSE 8080
