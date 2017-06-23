# originally adapted from: https://github.com/TrafeX/docker-php-nginx/blob/master/Dockerfile
FROM alpine:3.5

LABEL Maintainer="Evan Villemez <evillemez@gmail.com>" \
      Description="Example Silex app using nginx 1.10 & PHP-FPM 7.1 based on Alpine Linux."

# install packages from testing repo's
RUN apk --no-cache add php7 php7-fpm php7-mysqli php7-json php7-openssl php7-curl \
    php7-zlib php7-xml php7-phar php7-intl php7-dom php7-xmlreader php7-ctype \
    php7-mbstring php7-gd nginx \
    --repository http://dl-cdn.alpinelinux.org/alpine/edge/main/ \
    --repository http://dl-cdn.alpinelinux.org/alpine/edge/testing/

RUN apk --no-cache add supervisor curl

# configure nginx
COPY conf/app.nginx.conf /etc/nginx/conf.d/default.conf

# configure php
COPY conf/fpm-pool.conf /etc/php7/php-fpm.d/zzz_custom.conf
COPY conf/php.ini /etc/php7/conf.d/zzz_custom.ini

# TODO: maybe make nginx/php-fpm/php more configurable via env vars?

# create target dirs and copy
# NOTE: you must have run composer install via the dev environment
RUN mkdir -p /var/app/src /var/app/vendor /var/app/www
WORKDIR /var/app/www
COPY src/ /var/app/src/
COPY vendor/ /var/app/vendor/
COPY www/ /var/app/www/

EXPOSE 80 443