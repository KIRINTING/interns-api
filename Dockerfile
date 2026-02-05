# 1. ใช้ PHP Apache image
FROM php:8.2-apache

# 2. ติดตั้ง PHP Extensions ที่จำเป็นสำหรับ API (เช่น PDO, MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# 3. เปิดการใช้งาน mod_rewrite (จำเป็นสำหรับ Framework อย่าง Laravel หรือ Slim)
RUN a2enmod rewrite

# 4. เปลี่ยน Port ของ Apache ให้ตรงกับที่ Render กำหนด (Dynamic Port)
# เราจะแก้ไฟล์ config ให้ Apache ฟังที่ Port จาก Environment Variable
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 5. Copy โค้ดเข้าไปใน Container
WORKDIR /var/www/html
COPY . .

# 6. ตั้งค่า Permission (ถ้าต้องมีการเขียนไฟล์)
RUN chown -R www-data:www-data /var/www/html

# 7. รัน Apache ในโหมด Foreground
CMD ["apache2-foreground"]