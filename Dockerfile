# ---- Stage 1: deps PHP (composer) ----
FROM php:8.3-cli AS vendor
WORKDIR /app

# install tools needed to run composer
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl unzip git \
    && rm -rf /var/lib/apt/lists/*

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-plugins --no-scripts --no-interaction --no-progress

# ---- Stage 2: deps JS (npm) ----
FROM node:20 AS node_modules
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---- Stage 3: runtime PHP-FPM ----
FROM php:8.3-fpm-alpine AS php_runtime
WORKDIR /var/www/html

# Install php extensions# Penting: toolchain buat compile ekstensi PHP
# $PHPIZE_DEPS sudah ada di image resmi (autoconf, make, g++)
# Tambah header yang sering dibutuhkan saat compile
RUN set -eux; \
    apk add --no-cache \
    $PHPIZE_DEPS \
    linux-headers \
    bash git su-exec \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev;  # <- untuk pdo_pgsql

# GD: pastikan pakai freetype & jpeg
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install ekstensi yang diperlukan
# (intl butuh icu-dev; zip butuh libzip-dev; pdo_pgsql butuh postgresql-dev)
RUN docker-php-ext-install -j"$(nproc)" \
      intl \
      pdo \
      pdo_pgsql \
      zip \
      gd \
      bcmath \
      opcache

# (opsional) kalau TIDAK pakai mbstring, bisa di-skip. Kalau mau pakai:
RUN docker-php-ext-install -j"$(nproc)" mbstring

# opcache tuning
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.jit=1255'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache.ini 

COPY docker/php.ini /usr/local/etc/php/conf.d/laravel.ini

# copy app source
COPY . .

# copy vendor from stage 1
COPY --from=vendor /app/vendor ./vendor

# copy assets from stage 2
COPY --from=node_modules /app/public/build ./public/build

# storage and cache permissions
RUN addgroup -g 1000 www && adduser -G www -u 1000 -D www \
    && chown -R www:www storage bootstrap/cache \
    && find storage -type d -exec chmod 775 {} \; \
    && find storage -type f -exec chmod 664 {} \; \
    && chmod -R 775 bootstrap/cache

USER root
ENTRYPOINT ["/var/www/html/entrypoint.sh"]
CMD ["php-fpm"]

# ---- stage 4: NGINX as web ----
FROM nginx:1.27-alpine AS web
WORKDIR /var/www/html

# get file app from stage php
COPY --from=php_runtime --chown=nginx:nginx /var/www/html /var/www/html
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# simple health check
HEALTHCHECK --interval=30s --timeout=3s CMD wget -qO- http://127.0.0.1/health || exit 1
