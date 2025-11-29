# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip

# Enable Apache rewrite for Laravel routes
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project code
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies for production
RUN composer install --no-dev --optimize-autoloader

# Expose port
EXPOSE 80

# No artisan optimize in build phase!
# Render Start Command will handle it
CMD ["apache2-foreground"]

# Expose port
EXPOSE 80

CMD ["apache2-foreground"]
