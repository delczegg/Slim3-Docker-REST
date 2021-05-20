FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libicu-dev libpq-dev libzip-dev && docker-php-ext-install -j$(nproc) intl zip pgsql pdo_pgsql exif 
