#
# PHP Composer dependencies
#
FROM composer:1.9.3 as vendor

ARG PACKAGIST_AUTH_TOKEN
COPY database database/
COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer config --global --auth http-basic.repo.packagist.com token $PACKAGIST_AUTH_TOKEN
RUN composer install \
    --no-dev \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

#
# Application
#
FROM nginx:1.17.8-alpine

RUN apk add --no-cache php php-fpm php-pdo php-json php-xml php-simplexml php-xmlwriter php-session php-tokenizer php-ctype php-openssl \
    php-mbstring php-gettext php-curl php-zip php-dom php-fileinfo php-mysqli php-pdo_mysql php7-pecl-redis php-bcmath php-gd \
    supervisor && rm -fr /etc/nginx /etc/supervisor

COPY . /var/www/html
COPY --from=vendor /app/vendor/ /var/www/html/vendor/

COPY docker/config/ /etc
COPY docker/config/supervisor/stop-supervisor.sh /etc/supervisor
COPY docker/bootstrap-application /etc/bootstrap-application

RUN chmod +x /etc/bootstrap-application && \
    chmod +x /etc/supervisor/stop-supervisor.sh && \
    mv /var/www/html/docker/config/ /etc && \
    chown -R nginx:nginx /var/www/html

# Create the log files
RUN touch /var/www/html/storage/logs/queue-worker.log && touch /var/www/html/storage/logs/laravel.log && \
    chmod -R 777 /var/www/html/storage/logs/queue-worker.log && \
    chmod -R 777 /var/www/html/storage/logs/laravel.log


CMD ["/etc/bootstrap-application"]
