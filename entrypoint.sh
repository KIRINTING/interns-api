#!/bin/bash
# รัน Migration อัตโนมัติทุกครั้งที่รันเครื่อง
php artisan migrate --force
# รันคำสั่งหลักของ Docker (Apache)
exec "$@"