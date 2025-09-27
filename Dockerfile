FROM php:8.2-apache
RUN apt-get update && apt-get install -y unzip zlib1g-dev libonig-dev libzip-dev     && docker-php-ext-install mysqli pdo pdo_mysql zip mbstring     && pecl install mongodb     && docker-php-ext-enable mongodb
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN a2enmod rewrite
WORKDIR /var/www/html
COPY . /var/www/html
RUN if [ -f composer.json ]; then composer install --no-interaction --no-dev --prefer-dist; fi
EXPOSE 80
