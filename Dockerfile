FROM php:8.3.13-apache-bookworm AS php_core
WORKDIR /var/www/html
ENV BUILD_LIBS="libicu-dev zlib1g-dev libjpeg-dev libpng-dev libwebp-dev libfreetype-dev"
RUN apt update && apt upgrade -y &&  apt install -y $BUILD_LIBS && docker-php-ext-configure gd --with-jpeg --with-freetype && docker-php-ext-install -j$(nproc) pdo_mysql opcache intl gd && pecl install apcu-5.1.23 && docker-php-ext-enable apcu && pecl install redis-6.1.0 && docker-php-ext-enable redis && apt purge -y $BUILD_LIBS && rm -rf /var/lib/apt/lists/*
COPY docker/apache.conf /etc/apache2/sites-available/apache.conf
COPY docker/ini/ /usr/local/etc/php/conf.d/
COPY docker/ini/php.ini /usr/local/etc/php/conf.d/
RUN cd /etc/apache2/sites-available && a2dissite 000-default.conf && a2ensite apache.conf && cd /var/www/html/ && a2enmod rewrite && a2enmod negotiation

FROM composer:2.7.1 AS composer
COPY ./composer.json ./composer.lock /app/
RUN composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts --prefer-dist

FROM php_core AS developer
RUN pecl install xdebug-3.3.0 && docker-php-ext-enable xdebug
COPY docker/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
ENTRYPOINT mkdir -p var/cache/prod/starxen/file_manager/files && chmod 777 var/ -R && apache2-foreground

FROM php_core AS php_base
COPY ./ /var/www/html
COPY --from=composer /app/vendor/ /var/www/html/vendor/
RUN php bin/console c:c

FROM node:21.6.2 AS yarn
WORKDIR /app
COPY ./package.json ./yarn.lock ./webpack.config.js ./tsconfig.json /app/
COPY --from=composer /app/vendor/ /app/vendor
COPY --from=php_base /var/www/html/var/ /app/var
RUN yarn add @hotwired/stimulus
RUN yarn install
COPY ./assets/ /app/assets/
RUN yarn encore prod

FROM php_base AS php
COPY --from=yarn /app/public/build/ /var/www/html/public/build/
ARG VERSION
RUN echo -n $VERSION >> version.txt
ENTRYPOINT rm -rf var/cache && php bin/console c:c && php bin/console doctrine:migrations:migrate --no-interaction && mkdir -p var/cache/prod/starxen/file_manager/files && chmod 777 var/ -R && apache2-foreground && chmod 777 /tmp/ -R