FROM php:8.3-cli as base

RUN apt update && apt install -y libpq-dev && docker-php-ext-install pdo_pgsql && docker-php-ext-enable pdo_pgsql
RUN pecl install swoole && docker-php-ext-enable swoole

WORKDIR /app

CMD ["php", "public/index.php"]

FROM base as dev

RUN pecl install xdebug && docker-php-ext-enable xdebug

FROM base as prod

COPY ./bin /app/bin
COPY ./config /app/config
COPY ./migrations /app/migrations
COPY ./public /app/public
COPY ./src /app/src
COPY composer.* /app

COPY --from=composer:2.7.2 /usr/bin/composer /usr/bin/composer

RUN composer install -o