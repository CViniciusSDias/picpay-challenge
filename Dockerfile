FROM php:8.3-cli

RUN pecl install xdebug && docker-php-ext-enable xdebug
