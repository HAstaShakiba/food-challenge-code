FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Install dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader # --no-dev

# Install nginx
RUN apt-get update && apt-get install -y nginx supervisor \
    && rm -rf /var/lib/apt/lists/*

# Remove default nginx config and copy our own
RUN rm /etc/nginx/sites-enabled/default
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy entrypoint script
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 80 for nginx
EXPOSE 80

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

# Use entrypoint script to start both services
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
