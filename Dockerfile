FROM php:8.4-fpm
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-configure pdo_mysql && docker-php-ext-install pdo_mysql && docker-php-ext-enable pdo_mysql
RUN pecl install xdebug && docker-php-ext-enable xdebug