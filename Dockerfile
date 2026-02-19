FROM php:8.2-fpm

WORKDIR /var/www

# Dependencias sistema
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev

# Extensiones PHP
RUN docker-php-ext-install pdo_mysql zip gd bcmath

# Copiar proyecto
COPY . .

# Instalar composer deps
RUN composer install --no-dev --optimize-autoloader

# Permisos Laravel
RUN chmod -R 775 storage bootstrap/cache
