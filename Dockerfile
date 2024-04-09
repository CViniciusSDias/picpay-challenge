FROM php:8.3-cli

RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN apt update && apt install -y libpq-dev && docker-php-ext-install pdo_pgsql && docker-php-ext-enable pdo_pgsql
RUN pecl install swoole && docker-php-ext-enable swoole

WORKDIR /app

CMD ["php", "public/index.php"]