FROM richarvey/nginx-php-fpm:latest

COPY . /var/www/html

WORKDIR /var/www/html

COPY docker/nginx-site.conf /etc/nginx/sites-available/default.conf

ENV WEBROOT=/var/www/html/public
ENV VIEW_COMPILED_PATH=/var/www/html/storage/framework/views

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache

RUN chown -R nginx:nginx storage bootstrap/cache || chown -R www-data:www-data storage bootstrap/cache || true

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

CMD mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache && if [ "$DB_CONNECTION" = "sqlite" ]; then mkdir -p "$(dirname "$DB_DATABASE")" && touch "$DB_DATABASE"; fi && chmod -R 775 storage bootstrap/cache && php artisan optimize:clear && php artisan migrate --force && php artisan db:seed --force && php artisan optimize && /start.sh
