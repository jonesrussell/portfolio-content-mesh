
# You can copy this Dockerfile.example to Dockerfile to add configuration
# or packages or anything else to your webimage
ARG BASE_IMAGE
FROM $BASE_IMAGE

ENV PHP_VERSION=7.4

RUN apt-get update && apt-get install \
    php${PHP_VERSION}-redis \
    #    php${PHP_VERSION}-mongodb \
    supervisor \
    -y

RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y -o Dpkg::Options::="--force-confnew" --no-install-recommends --no-install-suggests gcc make autoconf libc-dev pkg-config php-pear php${PHP_VERSION}-dev
# The "echo" below just forces accepting the "automatic" configuration, the same as hitting <RETURN>
RUN echo | sudo pecl install mongodb

COPY ./mongodb.ini /etc/php/${PHP_VERSION}/mods-available/mongodb.ini
RUN phpenmod mongodb
