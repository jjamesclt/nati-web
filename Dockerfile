# Base image with Apache and PHP
FROM php:8.2-apache

# Install dependencies
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy app files
COPY public/ /var/www/html/
COPY config/ /var/www/config/

# Set permissions and ownership
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Apache config to serve from /var/www/html
EXPOSE 80
