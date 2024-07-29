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
RUN docker-php-ext-install gettext intl pdo_mysql gd zip \
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

# Copiar arquivos do projeto para o diretório do servidor
COPY . /var/www/html/

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chown -R $USER:www-data /var/www/html/storage/* \
    && chmod -R 775 /var/www/html/bootstrap/cache/ \
    && chmod -R 0777 /var/www/html/storage

# Instalar dependências PHP e Node.js
RUN cd /var/www/html \
    && composer install \
    && php artisan npm:install

# Expor a porta em que o Laravel irá rodar
EXPOSE 80

# Comando para rodar a aplicação usando `php artisan serve`
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
