FROM php:8.3-apache


WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
  libpng-dev \
  libjpeg62-turbo-dev \
  libfreetype6-dev \
  locales \
  zip \
  jpegoptim optipng pngquant gifsicle \
  vim \
  unzip \
  git \
  curl

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install gd \
  && docker-php-ext-install pdo_mysql

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

COPY --chown=www-data:www-data . /var/www/html

USER www-data


EXPOSE 80


CMD ["apache2-foreground"]
