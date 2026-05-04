FROM php:8.4-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git curl zip unzip nginx \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring bcmath zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --optimize-autoloader --no-interaction \
    && chown -R www-data:www-data storage bootstrap/cache

RUN echo 'server {\
    listen 80;\
    root /var/www/html/public;\
    index index.php index.html;\
    fastcgi_read_timeout 300;\
    location / { try_files $uri $uri/ /index.php?$query_string; }\
    location ~ \.php$ {\
        fastcgi_pass 127.0.0.1:9000;\
        fastcgi_index index.php;\
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;\
        fastcgi_read_timeout 300;\
        include fastcgi_params;\
    }\
}' > /etc/nginx/sites-available/default

COPY docker-start.sh /docker-start.sh
RUN chmod +x /docker-start.sh

EXPOSE 80
CMD ["/docker-start.sh"]
