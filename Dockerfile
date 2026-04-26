FROM richarvey/nginx-php-fpm:latest

COPY . /var/www/html

WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:clear

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache

ENV WEBROOT /var/www/html/public

CMD php artisan migrate --force && php artisan db:seed --force && /start.sh
