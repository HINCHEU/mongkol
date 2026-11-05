# syntax=docker/dockerfile:1

# 1. PHP dependencies (no dev packages in the image).
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts

# 2. Front-end build. Tailwind scans vendor's pagination views, so vendor comes along.
FROM node:24-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# 3. PHP-FPM runtime. PHP 8.2 matches local XAMPP and CI; bump all three together.
FROM php:8.2-fpm-alpine AS app
COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql gd zip intl bcmath exif opcache \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php/ $PHP_INI_DIR/conf.d/
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build
# filament:assets publishes the panel's CSS/JS into public/, which is gitignored.
RUN php artisan package:discover --ansi \
    && php artisan filament:assets --ansi \
    && chmod +x /usr/local/bin/entrypoint
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]

# 4. Nginx with the built public files baked in.
FROM nginx:1.27-alpine AS web
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
