# ---- PHP base: system deps + extensions (shared by vendor + app stages) ----
# FrankenPHP (Caddy-based) replaces `php artisan serve` as the production
# server — the dev server is single-threaded and blocks on every request.
FROM dunglas/frankenphp:1-php8.4-alpine AS php-base
WORKDIR /var/www/html

RUN apk add --no-cache \
        icu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        postgresql-dev \
        oniguruma-dev \
        autoconf \
        gcc \
        g++ \
        make \
        linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        pcntl \
        bcmath \
        gd \
        exif \
        intl \
        zip \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del autoconf gcc g++ make linux-headers

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---- Vendor: composer install (produces vendor/, needed by both PHP and the frontend build) ----
FROM php-base AS vendor
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --optimize-autoloader --no-progress

# ---- Frontend assets ----
FROM node:20-alpine AS frontend
WORKDIR /app
ARG VITE_PUSHER_APP_KEY
ARG VITE_PUSHER_APP_CLUSTER
ENV VITE_PUSHER_APP_KEY=$VITE_PUSHER_APP_KEY
ENV VITE_PUSHER_APP_CLUSTER=$VITE_PUSHER_APP_CLUSTER
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY public ./public
COPY --from=vendor /var/www/html/vendor ./vendor
RUN npm run build

# ---- Final application image ----
FROM php-base AS app
COPY . .
COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN composer dump-autoload --no-dev --optimize --no-scripts \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 8080
CMD ["/usr/local/bin/start.sh"]
