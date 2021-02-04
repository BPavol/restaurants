FROM php:8.0-fpm-buster

RUN apt-get update \
	&& apt-get install -y openssl libssl-dev libcurl4-openssl-dev zlib1g-dev libicu-dev g++

RUN pecl install xdebug-3.0.2 \
    && docker-php-ext-enable xdebug

RUN docker-php-ext-install opcache
RUN docker-php-ext-install pdo_mysql

ENV TZ=Europe/Bratislava
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

COPY php.ini /usr/local/etc/php/php.ini