FROM php:8.2-fpm-alpine

# System deps
RUN apk add --no-cache \
    nginx \
    bash \
    curl \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    icu-dev \
    libzip-dev \
    mysql-client

# PHP extensions
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
 && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app code
COPY . .

# Laravel required dirs
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Install PHP deps (NO artisan during build)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# RDS SSL cert
COPY certs/global-bundle.pem /var/www/html/certs/global-bundle.pem

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
