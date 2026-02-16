# TODO: Build and push automatically https://docs.github.com/en/actions/publishing-packages/publishing-docker-images#publishing-images-to-github-packages
# Until then, do it manually
# docker build -t ghcr.io/elbformat/field-helper-bundle/php:8.1 . -f docker/Dockerfile.php
# docker push
FROM php:8.3-alpine
LABEL org.opencontainers.image.source="https://github.com/elbformat/field-helper-bundle"

# For code coverage
RUN apk add --no-cache --virtual .build-deps autoconf g++ make && \
pecl install pcov-1.0.11 && \
apk del .build-deps && \
rm -rf /tmp/* &&\
docker-php-ext-enable pcov

# For debugging
RUN apk add --no-cache --virtual .build-deps autoconf g++ make linux-headers &&\
pecl install xdebug-3.4.0 && \
apk del .build-deps && \
rm -rf /tmp/*

# ext-intl
RUN apk add --no-cache icu icu-data-full && \
apk add --no-cache --virtual .build-deps icu-dev && \
docker-php-ext-install intl  && \
apk del .build-deps && \
rm -rf /tmp/*

#ext-xsl
RUN apk add --no-cache libxslt && \
apk add --no-cache --virtual .build-deps libxslt-dev && \
docker-php-ext-install xsl  && \
apk del .build-deps && \
rm -rf /tmp/*

# xdebug
RUN apk add --no-cache --virtual .build-deps autoconf g++ make linux-headers && \
pecl install xdebug-3.4.1 && \
docker-php-ext-enable xdebug && \
apk del .build-deps && \
rm -rf /tmp/*

# ext-gd
RUN apk add --no-cache libpng libjpeg-turbo freetype && \
apk add --no-cache --virtual .build-deps libpng-dev libjpeg-turbo-dev freetype-dev && \
docker-php-ext-configure gd && \
docker-php-ext-install gd && \
apk del .build-deps && \
rm -rf /tmp/*

# Database connection (for integration tests)
RUN docker-php-ext-install pdo pdo_mysql

# For codecov upload inside circleci
RUN apk add gpg gpg-agent gpgv

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_MEMORY_LIMIT=-1

WORKDIR /var/www