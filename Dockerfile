FROM php:7-apache

COPY ./app /var/www/html
COPY ./docs /var/www/docs
COPY ./database /var/www/database

RUN useradd -U -u 1000 appuser && chown -R 1000:1000 /var/www
USER 1000