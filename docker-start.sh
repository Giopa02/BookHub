#!/bin/bash
set -e
php artisan migrate:fresh --force --seed
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php-fpm -D
nginx -g 'daemon off;'
