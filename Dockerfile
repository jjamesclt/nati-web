# Base image with Apache and PHP
FROM php:8.2-apache

# Enable Apache mod_rewrite if needed
RUN a2enmod rewrite

# Copy your site into the web root
COPY . /var/www/html/

# Optionally set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose HTTP port
EXPOSE 80

# Apache is already the default CMD in this image
