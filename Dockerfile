FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM composer:2 AS dependencies
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

FROM php:8.3-apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql gd \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache-mpm-prefork.conf /etc/apache2/mods-available/mpm_prefork.conf
WORKDIR /var/www/html
COPY . .
COPY --from=dependencies /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN php artisan package:discover --ansi \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x /usr/local/bin/entrypoint.sh
EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
