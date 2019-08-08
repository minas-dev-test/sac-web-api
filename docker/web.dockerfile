FROM php:7.2-apache

COPY ./src /var/www/html

WORKDIR /var/www/html
RUN a2enmod rewrite
RUN apt-get update
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli