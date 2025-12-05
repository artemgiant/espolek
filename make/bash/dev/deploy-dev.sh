#!/bin/bash


# Скинути локальні зміни
git reset --hard && git clean -df

# Завантажити інформацію про віддалену гілку
git fetch origin

# Синхронізувати з віддаленою гілкою
git reset --hard origin/master



# Очистити кеш
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear


php artisan migrate

# Оновити Composer залежності
composer install --optimize-autoloader


# Очистити всі кеші
php artisan optimize:clear




sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart horizon
sudo supervisorctl status
