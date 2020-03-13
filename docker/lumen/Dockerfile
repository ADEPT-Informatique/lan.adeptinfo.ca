FROM php:7.2-fpm-alpine

RUN apk add --no-cache bash

RUN docker-php-ext-install pdo pdo_mysql

ADD wait-for-it /opt/wait-for-it

RUN ["chmod", "+x", "/opt/wait-for-it/wait-for-it.sh"]