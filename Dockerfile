# Base image
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev git unzip && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql

# Set working directory
WORKDIR /var/www

# Copy composer.json and install dependencies
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

# Copy the rest of the application
COPY . .

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 storage bootstrap/cache

# Expose port 9000
EXPOSE 9000

CMD ["php-fpm"]