FROM php:8.2-apache

# Install OpenSSL + PHP extensions
RUN apt-get update && \
    apt-get install -y openssl && \
    docker-php-ext-install mysqli && \
    docker-php-ext-enable mysqli

# Enable required Apache modules
RUN a2enmod rewrite ssl && \
    a2ensite default-ssl

# Create cert directory
RUN mkdir -p /etc/apache2/ssl

# Copy site files and config
COPY public/ /var/www/html/
COPY includes/ /var/www/includes/
COPY config/ /var/www/config/
COPY default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
COPY entrypoint.sh /entrypoint.sh

# Set permissions
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www && chmod +x /entrypoint.sh

EXPOSE 443

ENTRYPOINT ["/entrypoint.sh"]
