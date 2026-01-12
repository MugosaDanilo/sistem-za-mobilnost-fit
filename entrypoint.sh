#!/bin/bash
set -e

echo "Running Laravel migrations..."
php artisan migrate --force || true

echo "Starting Apache..."
exec apache2-foreground
