FROM php:8.3-fpm

# dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip zip libpng-dev libonig-dev libxml2-dev && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# instalar composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
