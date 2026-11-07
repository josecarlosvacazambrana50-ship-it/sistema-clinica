FROM php:8.2-apache

# Instalar dependencias del sistema y Node.js para Vite
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Instalar extensiones PHP para PostgreSQL
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Activar mod_rewrite de Apache
RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Instalar dependencias de PHP y Frontend (Vite)
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Crear estructura completa de directorios storage
RUN mkdir -p /var/www/html/storage/app/public /var/www/html/storage/framework/cache/data /var/www/html/storage/framework/sessions /var/www/html/storage/framework/testing /var/www/html/storage/framework/views /var/www/html/storage/logs /var/www/html/bootstrap/cache

# Asignar propietario www-data y permisos de escritura
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Apuntar Apache a la carpeta public
RUN sed -i -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# Limpiar cache, ejecutar migraciones automaticamente y levantar Apache
CMD php artisan view:clear && php artisan config:clear && php artisan migrate --force && apache2-foreground