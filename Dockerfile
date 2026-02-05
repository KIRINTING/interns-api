# 1. ใช้ PHP 8.2+ พร้อม Apache
FROM php:8.2-apache
# เพิ่มบรรทัดนี้เพื่อบอก Composer ว่า "ใช้ RAM เท่าไหร่ก็ได้ที่มี"
ENV COMPOSER_MEMORY_LIMIT=-1

# ปรับคำสั่ง install: 
# เพิ่ม --no-scripts เพื่อไม่ให้รันพวก post-install scripts ตอน build (ไปรันตอน start แทนถ้าจำเป็น)
# และเพิ่ม --no-interaction
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction
# 2. ติดตั้ง System Dependencies & PHP Extensions
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# 3. เปิด Mod Rewrite สำหรับ Laravel Routing
RUN a2enmod rewrite

# 4. ตั้งค่า Document Root ไปที่ /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 5. เปลี่ยน Port ให้รองรับ Render (Dynamic Port)
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 6. Copy Code และติดตั้ง Composer
WORKDIR /var/www/html
COPY . .
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 7. ตั้ง Permission ให้ Storage และ Cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. คำสั่งรัน
CMD ["apache2-foreground"]