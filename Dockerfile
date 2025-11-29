# ============================================================
# 1) BUILD STAGE — Composer + NPM + Vite build
# ============================================================
FROM php:8.2-apache AS build

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
    openssl \
    && docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install JS dependencies and build Vite assets
RUN npm ci
RUN npx vite build --emptyOutDir

# ============================================================
# 2) PRODUCTION STAGE — Apache + Laravel + SSL
# ============================================================
FROM php:8.2-apache

# System dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    curl \
    openssl \
    && docker-php-ext-install pdo pdo_pgsql zip

# Enable Apache SSL and rewrite modules
RUN a2enmod rewrite ssl

# Copy SSL certificates (if you have them, otherwise for development you can use self-signed certificates)
# Assuming you have SSL certificates in ./certs/ folder
COPY ./certs/ /etc/ssl/

# Enable default SSL site
RUN a2ensite default-ssl.conf

# Set up DocumentRoot to Laravel public folder
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Set ServerName to suppress warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Working directory
WORKDIR /var/www/html

# Copy built project from build stage
COPY --from=build /app /var/www/html

# Set permissions
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Expose ports for HTTP and HTTPS
EXPOSE 80 443

CMD ["apache2-foreground"]


