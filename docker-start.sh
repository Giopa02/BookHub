#!/bin/bash
set -e
php artisan migrate:fresh --force --seed
php-fpm -D
nginx -g 'daemon off;'
