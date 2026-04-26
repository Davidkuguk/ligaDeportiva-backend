FROM richarvey/nginx-php-fpm:latest

COPY . /var/www/html

WORKDIR /var/www/html

ENV WEBROOT=/var/www/html/public
ENV VIEW_COMPILED_PATH=/var/www/html/storage/framework/views

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache

RUN chown -R nginx:nginx storage bootstrap/cache || chown -R www-data:www-data storage bootstrap/cache || true

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:clear

CMD mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache && chmod -R 775 storage bootstrap/cache && php artisan config:clear && php artisan migrate --force && php artisan db:seed --force && /start.sh
