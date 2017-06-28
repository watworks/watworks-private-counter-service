# adapted from: https://github.com/TrafeX/docker-php-nginx/blob/master/Dockerfile
FROM php:7.1-fpm-alpine

LABEL Maintainer="Evan Villemez <evillemez@gmail.com>" \
      Description="Example Silex app using nginx 1.10 & PHP-FPM 7.1 based on Alpine Linux."

# install other deps
RUN apk --no-cache add nginx supervisor curl

# override configs
COPY conf/fpm-pool.conf /etc/php7/php-fpm.d/zzz_custom.conf
COPY conf/php.ini /etc/php7/conf.d/zzz_custom.ini
COPY conf/nginx.conf /etc/nginx/nginx.conf
COPY conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN mkdir -p /var/app
WORKDIR /var/app
COPY src/ /var/app/src
COPY www/ /var/app/www
COPY vendor/ /var/app/vendor
COPY docs/ /var/app/docs

# start supervisor & expose ports
USER root
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
EXPOSE 80 443
