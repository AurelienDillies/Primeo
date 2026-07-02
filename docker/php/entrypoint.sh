#!/bin/sh
set -e

cd /var/www/backend

# Installation auto si vendor absent
if [ ! -f "vendor/autoload.php" ]; then
  echo "Installing PHP dependencies..."
  composer install --no-interaction --prefer-dist
fi

exec php-fpm -F