# ============================================================
# 1) BUILD STAGE — Composer + NPM + Vite buildd
# ============================================================
FROM php:8.2-apache as build

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    curl \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Working directory
WORKDIR /app

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install JS dependencies + build Vite assets
RUN npm ci && npm run build



# ============================================================
# 2) PRODUCTION STAGE — Apache + Laravel
# ============================================================
FROM php:8.2-apache

# System dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip

# Enable Apache rewrite
RUN a2enmod rewrite

# Set DocumentRoot to Laravel public folder
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# ServerName to suppress warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Working directory
WORKDIR /var/www/html

# Copy built project from build stage
COPY --from=build /app /var/www/html

# Permissions
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Copy entrypoint script
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Use entrypoint to run migrations automatically
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
