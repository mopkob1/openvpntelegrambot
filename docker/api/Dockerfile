FROM composer:latest AS composer
FROM php:8.2-fpm-alpine

ARG USER_ID
ARG GROUP_ID
ARG XDEBUG_MODES
ARG REMOTE_HOST
ARG REMOTE_PORT
ARG IDE_KEY

ENV MODES=$XDEBUG_MODES
ENV CLIENT_HOST=$REMOTE_HOST
ENV CLIENT_PORT=$REMOTE_PORT
ENV IDEKEY=$IDE_KEY

RUN apk add --no-cache --virtual cron supervisor grep \
    --update libzip-dev curl-dev \
    git curl wget -U shadow

#RUN pecl install xdebug \
#  && docker-php-ext-enable xdebug

RUN mkdir -p /home/xdebug 
RUN mkdir -p /var/log/supervisor

COPY ./docker/api/xdebug-debug.ini /home/xdebug/xdebug-debug.ini
COPY ./docker/api/xdebug-default.ini /home/xdebug/xdebug-default.ini
COPY ./docker/api/xdebug-off.ini /home/xdebug/xdebug-off.ini
COPY ./docker/api/xdebug-profile.ini /home/xdebug/xdebug-profile.ini
COPY ./docker/api/xdebug-trace.ini /home/xdebug/xdebug-trace.ini
COPY ./docker/api/php.ini /usr/local/etc/php/php.ini
COPY ./docker/api/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY ./docker/api/fpm-entrypoint.sh /home/fpm-entrypoint
RUN chmod +x /home/fpm-entrypoint

WORKDIR /var/www/html
RUN usermod -u "${USER_ID}" "www-data" && groupmod -g "${GROUP_ID}" "www-data"

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

