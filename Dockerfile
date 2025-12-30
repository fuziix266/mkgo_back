FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install \
    pdo_mysql \
    intl \
    zip \
    opcache

# Habilitar mod_rewrite para Apache (necesario para Laminas)
RUN a2enmod rewrite

# Configurar el DocumentRoot a /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e "s!/var/www/html!$APACHE_DOCUMENT_ROOT!g" /etc/apache2/sites-available/*.conf
RUN sed -ri -e "s!/var/www/!$APACHE_DOCUMENT_ROOT!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . /var/www/html

# Instalar dependencias de PHP
# Usamos --no-dev para producción y --optimize-autoloader para rendimiento
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Asegurar que el directorio data existe y crear estructura
RUN mkdir -p /var/www/html/data/cache \
    && mkdir -p /var/www/html/data/log \
    && mkdir -p /var/www/html/data/sessions

# Asignar permisos correctos (especialmente para data/ cache)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/data

# Puerto expuesto
EXPOSE 80
