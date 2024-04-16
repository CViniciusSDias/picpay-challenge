FROM php:8.3-fpm as dev

RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN apt update && apt install -y libpq-dev && docker-php-ext-install pdo_pgsql && docker-php-ext-enable pdo_pgsql

WORKDIR /app
