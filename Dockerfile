FROM php:8.3.6-apache-bullseye

ARG DEBIAN_FRONTEND=noninteractive

# Instalar dependências necessárias
RUN apt-get update \
    && apt-get -y install software-properties-common \
    && apt-get -y install build-essential \
    && apt-get -y install libaio1 \
    && apt-get -y install wget \
    && apt-get -y install curl \
    && apt-get -y install libmemcached-dev \
    && apt-get -y install libfreetype6-dev \
    && apt-get -y install libjpeg62-turbo-dev \
    && apt-get -y install libzip-dev \
    && apt-get -y install memcached libmemcached-tools \
    && apt-get -y install libxml2-dev \
    && apt-get -y install unzip zip \
    && apt-get -y install libldap2-dev \
    && apt-get -y install gnupg

# Instalar Composer
COPY --from=composer:2.2.0 /usr/bin/composer /usr/local/bin/composer

# Instalar extensões PHP
RUN docker-php-ext-install gettext intl pdo_mysql gd zip exif bcmath \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Habilitar módulos Apache
RUN a2enmod headers \
    && a2enmod rewrite \
    && a2enmod log_forensic \
    && echo "ForensicLog /var/log/apache2/access.log" >> /etc/apache2/apache2.conf

# Instalar Node.js 20.15.0
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs=20.15.0-1nodesource1

COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf

# Copiar arquivos do projeto para o diretório do servidor
COPY --chown=www-data:www-data . /var/www/html/

# Instalar dependências PHP e Node.js
WORKDIR /var/www/html
RUN composer install
RUN php artisan npm:install

# Expor a porta em que o Laravel irá rodar
EXPOSE 80
