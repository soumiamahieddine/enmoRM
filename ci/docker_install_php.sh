#!/usr/bin/env bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && exit 0

set -xe

apt-get update -yqq
apt-get install git p7zip-full default-jre -yqq

curl --location --output /usr/local/bin/phpunit https://phar.phpunit.de/phpunit.phar
chmod +x /usr/local/bin/phpunit

apt-get install -y libpq-dev libxml2-dev libxslt1-dev libpng-dev \
&& docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
&& docker-php-ext-install pdo_pgsql pgsql xsl gd \
&& pecl install xdebug-2.7.0RC2 \
&& docker-php-ext-enable xdebug
