#!/bin/bash
php artisan migrate --force --seed
php-fpm -D
nginx -g 'daemon off;'
