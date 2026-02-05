# 1. ใช้ PHP 8.2+ พร้อม Apache
FROM php:8.2-apache

# 2. ติดตั้ง System Dependencies ที่จำเป็น (ต้องทำก่อนลง PHP Ext และ Composer)
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# 3. ติดตั้ง Composer (ต้องอยู่ก่อนการเรียกใช้คำสั่ง composer install)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. ตั้งค่า Environment สำหรับ Composer
ENV COMPOSER_MEMORY_LIMIT=-1

# 5. เปิด Mod Rewrite สำหรับ Laravel
RUN a2enmod rewrite

# 6. ตั้งค่า Document Root ไปที่ /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 7. เปลี่ยน Port ให้รองรับ Render
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 8. เตรียมไฟล์และติดตั้ง Dependencies
WORKDIR /var/www/html
COPY . .

# ติดตั้ง Laravel Dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# 9. ตั้ง Permission ให้ Storage และ Cache (สำคัญมากสำหรับ Laravel)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 10. คำสั่งรัน
CMD ["apache2-foreground"]