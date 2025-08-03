# Gunakan image PHP-FPM dengan Alpine Linux sebagai base
FROM php:8.3-fpm-alpine

# Setel working directory di dalam container
WORKDIR /var/www/html

# Install dependencies yang dibutuhkan
# `nodejs` dan `npm` ditambahkan di sini
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    sqlite-dev \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite zip exif pcntl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install intl 

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin source code aplikasi ke dalam container
COPY . .

# Install dependencies PHP menggunakan Composer
RUN composer install --no-dev --optimize-autoloader

# Install dependencies Node.js dan jalankan build aset frontend
RUN npm install \
    && npm run build

# Atur ownership dan permission
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Atur entrypoint atau command jika diperlukan
# ENTRYPOINT ["php-fpm"]
# Set working directory
# WORKDIR /var/www/html

# Copy entrypoint script
# COPY entrypoint.sh /usr/local/bin/entrypoint.sh
# ENTRYPOINT ["entrypoint.sh"]

# Start php-fpm
CMD ["php-fpm"]