FROM php:8.2-alpine

ENV COMPOSER_HOME="/tmp/composer"

COPY --from=composer:2.5.4 /usr/bin/composer /usr/bin/composer

RUN set -x \
    && apk add --no-cache git \
    && mkdir --parents --mode=777 /src ${COMPOSER_HOME}/cache/repo ${COMPOSER_HOME}/cache/files \
    && ln -s /usr/bin/composer /usr/bin/c \
    && composer --version \
    && php -v \
    && php -m

WORKDIR /src
