#!/bin/bash
set -e
php artisan migrate:fresh --force --seed
php artisan config:cache
php artisan route:cache
php-fpm -D
nginx -g 'daemon off;'
