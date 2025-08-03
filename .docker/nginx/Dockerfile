FROM nginx:alpine

# Tambahkan procps jika Anda ingin menggunakan `ps`
RUN apk add --no-cache procps

# Tambahkan user nginx ke grup www-data
# RUN addgroup -S www-data && adduser -S -G www-data nginx

# Salin file konfigurasi Nginx Anda
COPY default.conf /etc/nginx/conf.d/default.conf