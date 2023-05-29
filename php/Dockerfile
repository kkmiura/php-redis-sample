FROM php:8.2-apache

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_NO_INTERACTION 1

# composer インストール
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt update -y && \
    apt install -y git unzip && \
    docker-php-ext-install pdo_mysql && \
    pecl install redis && \
    docker-php-ext-enable redis


COPY php.ini /usr/local/etc/php
COPY src/composer.json .
COPY src/ .

RUN composer install

CMD ["apache2-foreground"]