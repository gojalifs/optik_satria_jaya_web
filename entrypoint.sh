#!/bin/bash
set -euo pipefail

APP_USER=www
APP_DIR=/var/www/html

echo "Fixing permissions..."
if [ "$(id -u)" -eq 0 ]; then
  chown -R "${APP_USER}:${APP_USER}" \
    "${APP_DIR}/storage" \
    "${APP_DIR}/bootstrap/cache"
fi
chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"

exec su-exec "${APP_USER}" /usr/local/bin/docker-php-entrypoint "$@"
