#!/usr/bin/env bash

set -e  # Exit immediately if a command exits with a non-zero status

echo "Running composer..."
if ! composer global require hirak/prestissimo; then
    echo "Failed to install prestissimo. Continuing with composer install..."
fi

# Install dependencies
echo "Installing application dependencies..."
composer install --no-dev --working-dir=/var/www/html

echo "Generating application key..."
php artisan key:generate --show --no-interaction

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force

# Uncomment the following line if you want to seed the database
 echo "Seeding database..."
 php artisan db:seed --force

echo "Setup complete!"