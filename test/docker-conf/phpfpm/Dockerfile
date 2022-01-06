ARG PHP_VERSION
FROM 3liz/liz-php-fpm:${PHP_VERSION}

RUN apt-get update; \
    apt-get -y install \
    php${PHP_VERSION}-xdebug \
    ; \
    apt-get clean

COPY jcomm_entrypoint.sh /bin/entrypoint.d/
COPY appctl.sh /bin/
RUN chmod 755 /bin/entrypoint.d/jcomm_entrypoint.sh /bin/appctl.sh

