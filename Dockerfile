# Use the PHP 8.1 image with Apache on Debian Bullseye
FROM php:8.1-apache-bullseye

# Set environment variables
ARG DEBIAN_FRONTEND=noninteractive
ENV COMPOSER_VERSION 2.2.0
ENV CONTEXT infrastructure/environments/production

# Update and install system dependencies
RUN apt-get update && apt-get install -y \
    software-properties-common \
    build-essential \
    libaio1 \
    wget \
    curl \
    libmemcached-dev \
    libfreetype6-dev \
    libzip-dev \
    memcached \
    libmemcached-tools \
    libxml2-dev \
    libldap2-dev \
    libpq-dev \
    libmysqlclient-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install zip opcache pdo_mysql pdo_pgsql ldap soap \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql pdo_pgsql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --version=${COMPOSER_VERSION} --install-dir=/usr/bin && \
    ln -s /usr/bin/composer.phar /usr/bin/composer

# Copy Apache configuration
COPY ${CONTEXT}/apache/default.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache modules
RUN a2enmod headers rewrite log_forensic \
    && echo "ForensicLog /var/log/apache2/access.log" >> /etc/apache2/apache2.conf

# Set up the application
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 0777 /var/www/html/storage

# Install Laravel dependencies
RUN composer install

# Expose port 80
EXPOSE 8080

# Start Apache server
CMD ["apache2-foreground"]
