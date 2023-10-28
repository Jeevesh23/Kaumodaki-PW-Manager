FROM php:8.2-apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y git unzip zip curl
RUN apt-get update && apt-get install -y \
	libfreetype-dev \
	libjpeg62-turbo-dev \
	libpng-dev \
	\
	&& docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update && \
	apt-get install -y \
	libc-client-dev libkrb5-dev
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl && \
	docker-php-ext-install -j$(nproc) imap

RUN apt install -y cron

RUN /bin/bash -c 'mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini'
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY ./scripts/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["sh","/usr/local/bin/entrypoint.sh"]

RUN pecl install xdebug
COPY ./php_config/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY ./php_config/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini
RUN docker-php-ext-enable xdebug

ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /app
COPY composer.json .
COPY composer.lock .
RUN /bin/bash -c 'composer install --working-dir=/app'

RUN /bin/bash -c 'a2enmod rewrite && service apache2 restart'

WORKDIR /var/www/html
