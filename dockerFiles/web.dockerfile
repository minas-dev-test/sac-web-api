FROM php:7.2-apache

COPY . /app

WORKDIR /app
RUN a2enmod rewrite
RUN apt-get update
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli