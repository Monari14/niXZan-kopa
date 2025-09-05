FROM composer:2.8.11 AS vendor

WORKDIR /app
COPY composer.json composer.lock /app/
COPY app /app/app/
COPY config /app/config/
COPY bootstrap /app/bootstrap/
COPY database /app/database/
COPY resources /app/resources/
COPY routes /app/routes/
COPY storage /app/storage/
COPY artisan /app
RUN composer install --no-dev --optimize-autoloader

FROM php:8.4.12-apache AS dist
COPY --from=vendor /app/vendor /var/www/vendor/
COPY public /var/www/html/

RUN docker-php-ext-install pdo pdo_mysql

ENV APP_ENV=production
ENV APP_DEBUG=false

COPY app /var/www/app/
COPY config /var/www/config/
COPY --from=vendor /app/bootstrap /var/www/bootstrap/
COPY database /var/www/database/
COPY resources /var/www/resources/
COPY routes /var/www/routes/
COPY storage /var/www/storage/
COPY composer.json /var/www/
COPY composer.lock /var/www/
COPY artisan /var/www/

RUN cd /var/www && \
    php artisan config:cache && \
    php artisan route:cache && \
    chmod 777 -R /var/www/storage/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite
