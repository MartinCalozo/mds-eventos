FROM php:8.3-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev

# Instalar extensiones de PHP necesarias para Laravel
RUN docker-php-ext-install pdo pdo_mysql mbstring bcmath gd

# Instalar XDebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN { \
    echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20230831/xdebug.so"; \
    echo "xdebug.mode=coverage"; \
    echo "xdebug.start_with_request=default"; \
} > /usr/local/etc/php/conf.d/xdebug.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar todos los archivos del proyecto
COPY . .

# Dar permisos necesarios
RUN mkdir -p storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Instalar dependencias de Laravel
RUN composer install

# Exponer puerto
EXPOSE 8000 

# Comando final: levantar Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
