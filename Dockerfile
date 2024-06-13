FROM php:8.1-fpm-alpine

# Instalar dependencias del sistema
RUN apk update && apk add --no-cache \
    bash \
    curl \
    libzip-dev \
    zip \
    unzip

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo_mysql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiamos el código de la aplicación
COPY . /var/www

# Establecer el directorio de trabajo
WORKDIR /var/www

COPY --chown=www-data:www-data . .

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Copiar script de inicio
COPY docker/start.sh /home/

# Exponer el puerto 9000 para PHP-FPM
EXPOSE 9000

# Configurar el script de inicio
CMD ["bash","/home/start.sh"]
