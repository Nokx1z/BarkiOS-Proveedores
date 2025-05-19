FROM php:8.2-apache

# Instala dependencias y extensiones
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

# Habilita mod_rewrite
RUN a2enmod rewrite

# Instala Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copia SOLO los archivos necesarios para composer install
COPY composer.* ./

# Copia el resto del código
COPY . .

# Permisos para Apache
RUN chown -R www-data:www-data /var/www/html
