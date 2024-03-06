# Use an official PHP image with Apache
FROM php:8.1-apache

# Install PDO PostgreSQL extension
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy your application source code to the working directory
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Expose port 80 to access the application
EXPOSE 80
